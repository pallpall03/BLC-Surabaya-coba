<?php

namespace App\Http\Controllers\Api\Master;

use App\Enums\BlcRegion;
use App\Enums\UserRole;
use App\Http\Controllers\Api\Concerns\MasterDataHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlcResource;
use App\Http\Resources\InstructorResource;
use App\Models\Blc;
use App\Models\Kelurahan;
use App\Models\Participant;
use App\Support\Slugger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BlcController extends Controller
{
    use MasterDataHelpers;

    public function index(Request $request): AnonymousResourceCollection
    {
        $staff = $this->isStaff($request);

        $query = $this->onlyVisible(Blc::query(), $request)
            ->with(['kecamatan:id,name', 'kelurahan:id,name'])
            ->when($staff, fn ($q) => $q->withCount(['users as instructors_count' => fn ($u) => $u->where('role', UserRole::Instruktur->value)]))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.mb_strtolower((string) $request->query('q')).'%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(name) LIKE ?', [$term])->orWhereRaw('LOWER(address) LIKE ?', [$term]));
            })
            ->when($request->filled('region'), fn ($q) => $q->where('region', $request->query('region')))
            ->when($request->filled('kecamatan_id'), fn ($q) => $q->where('kecamatan_id', $request->integer('kecamatan_id')))
            ->when($request->filled('show_on_landing'), fn ($q) => $q->where('show_on_landing', $request->boolean('show_on_landing')))
            ->when($staff && $request->filled('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->orderBy('name');

        return BlcResource::collection($query->paginate($this->perPage($request)));
    }

    public function show(Request $request, Blc $blc): BlcResource
    {
        $this->ensureVisible($request, $blc);

        $blc->load(['kecamatan:id,name', 'kelurahan:id,name']);

        if ($this->isStaff($request)) {
            $blc->loadCount(['users as instructors_count' => fn ($u) => $u->where('role', UserRole::Instruktur->value)]);
        }

        return new BlcResource($blc);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $this->assertKelurahanInKecamatan($data['kecamatan_id'] ?? null, $data['kelurahan_id'] ?? null);

        $data['slug'] = $data['slug'] ?? Slugger::unique(Blc::class, $data['name']);

        $blc = Blc::create($data)->refresh()->load(['kecamatan:id,name', 'kelurahan:id,name']);

        return (new BlcResource($blc))->response()->setStatusCode(201);
    }

    public function update(Request $request, Blc $blc): BlcResource
    {
        $data = $request->validate($this->rules($blc));

        $this->assertKelurahanInKecamatan(
            array_key_exists('kecamatan_id', $data) ? $data['kecamatan_id'] : $blc->kecamatan_id,
            array_key_exists('kelurahan_id', $data) ? $data['kelurahan_id'] : $blc->kelurahan_id,
        );

        $blc->fill($data)->save();

        return new BlcResource($blc->load(['kecamatan:id,name', 'kelurahan:id,name']));
    }

    public function destroy(Blc $blc): JsonResponse
    {
        $used = $blc->classes()->exists()
            || $blc->guestbookEntries()->exists()
            || $blc->activityReports()->exists()
            || Participant::where('blc_id', $blc->id)->exists();

        if ($used) {
            return $this->inUse('BLC sudah punya data (kelas, peserta, buku tamu, atau laporan). Nonaktifkan saja, jangan dihapus.');
        }

        $blc->users()->detach();
        $blc->delete();

        return response()->json(['message' => 'BLC dihapus.']);
    }

    /** Daftar instruktur yang ditugaskan di BLC ini. */
    public function instructors(Blc $blc): AnonymousResourceCollection
    {
        return InstructorResource::collection(
            $blc->users()->where('role', UserRole::Instruktur->value)->orderBy('name')->get()
        );
    }

    /**
     * Mengatur ulang instruktur BLC. Hanya menyentuh akun berperan instruktur;
     * akun lain yang terhubung ke BLC (mis. role user) tidak ikut terhapus.
     */
    public function syncInstructors(Request $request, Blc $blc): AnonymousResourceCollection
    {
        $data = $request->validate([
            'user_ids' => ['present', 'array'],
            'user_ids.*' => [
                'integer', 'distinct',
                Rule::exists('users', 'id')->where('role', UserRole::Instruktur->value),
            ],
        ]);

        $wanted = array_map('intval', $data['user_ids']);
        $current = $blc->users()->where('role', UserRole::Instruktur->value)->pluck('users.id')->all();

        $blc->users()->detach(array_values(array_diff($current, $wanted)));
        $blc->users()->attach(array_values(array_diff($wanted, $current)));

        return $this->instructors($blc);
    }

    /** @return array<string, array<int, mixed>> */
    private function rules(?Blc $blc = null): array
    {
        $sometimes = $blc ? ['sometimes'] : [];

        return [
            'name' => [...$sometimes, 'required', 'string', 'max:150', Rule::unique('blcs', 'name')->ignore($blc?->id)],
            'slug' => [$blc ? 'sometimes' : 'nullable', 'string', 'max:150', 'alpha_dash', Rule::unique('blcs', 'slug')->ignore($blc?->id)],
            'region' => ['nullable', Rule::enum(BlcRegion::class)],
            'address' => ['nullable', 'string', 'max:500'],
            'kecamatan_id' => ['nullable', 'integer', Rule::exists('kecamatans', 'id')],
            'kelurahan_id' => ['nullable', 'integer', Rule::exists('kelurahans', 'id')],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'phone' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:5000'],
            'show_on_landing' => [...$sometimes, 'boolean'],
            'is_active' => [...$sometimes, 'boolean'],
        ];
    }

    private function assertKelurahanInKecamatan(?int $kecamatanId, ?int $kelurahanId): void
    {
        if ($kelurahanId === null) {
            return;
        }

        $ok = $kecamatanId !== null
            && Kelurahan::query()->whereKey($kelurahanId)->where('kecamatan_id', $kecamatanId)->exists();

        if (! $ok) {
            throw ValidationException::withMessages([
                'kelurahan_id' => 'Kelurahan tidak termasuk dalam kecamatan yang dipilih.',
            ]);
        }
    }
}
