<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\KoordinatorResource;
use App\Models\Koordinator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KoordinatorController extends Controller
{
    /**
     * Daftar koordinator yang terhubung ke BLC.
     * Koordinator dapat mengakses beberapa BLC (array lokasi_blc_ids).
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = $this->koordinators()
            ->with('blcs:id,name,slug')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.mb_strtolower((string) $request->query('q')).'%';
                $q->where(fn ($w) => $w
                    ->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(kantorku_id) LIKE ?', [$term])
                );
            })
            ->when($request->filled('is_active'), function ($q) use ($request) {
                $q->where('is_active', $request->boolean('is_active'));
            })
            ->orderBy('name');

        return KoordinatorResource::collection($query->paginate($this->perPage($request)));
    }

    /**
     * Tampilkan detail koordinator.
     */
    public function show(int $koordinator): \Illuminate\Http\JsonResponse
    {
        return new KoordinatorResource($this->koordinators()->findOrFail($koordinator));
    }

    /**
     * Daftar semua koordinator.
     */
    private function koordinators()
    {
        return User::query()->where('role', UserRole::Koordinator->value);
    }

    /**
     * Buat koordinator baru.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'kantorku_id' => ['nullable', 'string', 'max:100', 'unique:users,kantorku_id'],
            'password' => ['required_without:kantorku_id', 'nullable', 'string', 'min:8', 'max:100'],
            'is_active' => ['boolean'],
            'lokasi_blc_ids' => ['required', 'array', 'min:1'],
            'lokasi_blc_ids.*' => ['integer', 'distinct', 'exists:blcs,id'],
        ]);

        $user = new User();
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            // password diisi secara eksplisit di sini
            'password' => $data['password'] ?? \Illuminate\Support\Str::random(40),
        ]);

        // role, is_active, dan kantorku_id sengaja tidak bisa diisi massal, jadi diisi eksplisit di sini
        $user->forceFill([
            'role' => UserRole::Koordinator->value,
            'is_active' => $data['is_active'] ?? true,
            'kantorku_id' => $data['kantorku_id'] ?? null,
        ])->save();

        // assign koordinator ke beberapa BLC
        $user->blcs()->sync($data['lokasi_blc_ids'] ?? []);

        return (new KoordinatorResource($user->load('blcs:id,name,slug')))->response()->setStatusCode(201);
    }

    /**
     * Update koordinator.
     */
    public function update(Request $request, int $koordinator): \Illuminate\Http\JsonResponse
    {
        $user = $this->koordinators()->findOrFail($koordinator);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'kantorku_id' => ['sometimes', 'nullable', 'string', 'max:100', 'unique:users,kantorku_id,' . $user->id],
            'password' => ['sometimes', 'string', 'min:8', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'lokasi_blc_ids' => ['sometimes', 'array', 'min:1'],
            'lokasi_blc_ids.*' => ['integer', 'distinct', 'exists:blcs,id'],
        ]);

        $user->fill(collect($data)->only(['name', 'email', 'phone', 'password'])->all());

        if (array_key_exists('kantorku_id', $data)) {
            $user->forceFill(['kantorku_id' => $data['kantorku_id']]);
        }

        if (array_key_exists('is_active', $data)) {
            $user->forceFill(['is_active' => $data['is_active']]);
        }

        $user->save();

        if (array_key_exists('lokasi_blc_ids', $data)) {
            $user->blcs()->sync($data['lokasi_blc_ids']);
        }

        return new KoordinatorResource($user->load('blcs:id,name,slug'));
    }

    /**
     * Hapus koordinator (dinonaktifkan, bukan dihapus secara permanen).
     */
    public function destroy(int $koordinator): JsonResponse
    {
        $user = $this->koordinators()->findOrFail($koordinator);

        // dinonaktifkan, bukan dihapus
        $user->update(['is_active' => false]);

        return response()->json(['message' => 'Koordinator dinonaktifkan.']);
    }
}