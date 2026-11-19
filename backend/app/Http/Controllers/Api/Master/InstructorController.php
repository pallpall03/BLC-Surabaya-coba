<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\UserRole;
use App\Http\Controllers\Api\Concerns\MasterDataHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstructorResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Master data instruktur (akun berperan `instruktur`).
 * Akun yang terhubung Kantorku (kantorku_id terisi) login lewat Kantorku, jadi tidak perlu password.
 * Instruktur tidak dihapus: cukup dinonaktifkan lewat is_active supaya riwayat kelas dan absensi aman.
 */
class InstructorController extends Controller
{
    use MasterDataHelpers;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->instructors()
            ->with('blcs:id,name,slug')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.mb_strtolower((string) $request->query('q')).'%';
                $q->where(fn ($w) => $w
                    ->whereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(username) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$term]));
            })
            ->when($request->filled('blc_id'), fn ($q) => $q->whereHas('blcs', fn ($b) => $b->where('blcs.id', $request->integer('blc_id'))))
            ->when($request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('name');

        return InstructorResource::collection($query->paginate($this->perPage($request)));
    }

    public function show(int $instructor): InstructorResource
    {
        return new InstructorResource($this->instructors()->with('blcs:id,name,slug')->findOrFail($instructor));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'kantorku_id' => ['nullable', 'string', 'max:100', Rule::unique('users', 'kantorku_id')],
            'password' => ['required_without:kantorku_id', 'nullable', 'string', 'min:8', 'max:100'],
            'is_active' => ['boolean'],
            'blc_ids' => ['array'],
            'blc_ids.*' => ['integer', 'distinct', Rule::exists('blcs', 'id')],
        ]);

        $user = new User();
        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            // Akun Kantorku tetap butuh nilai password di database; dibuat acak dan tidak pernah dipakai login.
            'password' => $data['password'] ?? Str::random(40),
        ]);
        // role, kantorku_id, dan is_active sengaja tidak bisa diisi massal, jadi diisi eksplisit di sini.
        $user->forceFill([
            'role' => UserRole::Instruktur,
            'kantorku_id' => $data['kantorku_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ])->save();

        $user->blcs()->sync($data['blc_ids'] ?? []);

        return (new InstructorResource($user->load('blcs:id,name,slug')))->response()->setStatusCode(201);
    }

    public function update(Request $request, int $instructor): InstructorResource
    {
        $user = $this->instructors()->findOrFail($instructor);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'username' => ['sometimes', 'required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'kantorku_id' => ['sometimes', 'nullable', 'string', 'max:100', Rule::unique('users', 'kantorku_id')->ignore($user->id)],
            'password' => ['sometimes', 'string', 'min:8', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'blc_ids' => ['sometimes', 'array'],
            'blc_ids.*' => ['integer', 'distinct', Rule::exists('blcs', 'id')],
        ]);

        $user->fill(collect($data)->only(['name', 'username', 'email', 'phone', 'password'])->all());

        if (array_key_exists('kantorku_id', $data)) {
            $user->forceFill(['kantorku_id' => $data['kantorku_id']]);
        }

        if (array_key_exists('is_active', $data)) {
            $user->forceFill(['is_active' => $data['is_active']]);
        }

        $user->save();

        if (array_key_exists('blc_ids', $data)) {
            $user->blcs()->sync($data['blc_ids']);
        }

        // Akun yang dinonaktifkan atau diganti passwordnya harus keluar dari semua perangkat.
        if (($data['is_active'] ?? true) === false || array_key_exists('password', $data)) {
            $user->tokens()->delete();
        }

        return new InstructorResource($user->load('blcs:id,name,slug'));
    }

    private function instructors()
    {
        return User::query()->where('role', UserRole::Instruktur->value);
    }
}
