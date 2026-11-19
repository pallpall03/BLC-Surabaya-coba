<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\KecamatanResource;
use App\Models\ActivityReport;
use App\Models\Blc;
use App\Models\Kecamatan;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class KecamatanController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return KecamatanResource::collection(
            Kecamatan::query()->withCount('kelurahans')->orderBy('name')->get()
        );
    }

    public function show(Kecamatan $kecamatan): KecamatanResource
    {
        return new KecamatanResource($kecamatan->loadCount('kelurahans'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('kecamatans', 'name')],
        ]);

        $kecamatan = Kecamatan::create($data)->loadCount('kelurahans');

        return (new KecamatanResource($kecamatan))->response()->setStatusCode(201);
    }

    public function update(Request $request, Kecamatan $kecamatan): KecamatanResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('kecamatans', 'name')->ignore($kecamatan->id)],
        ]);

        $kecamatan->update($data);

        return new KecamatanResource($kecamatan->loadCount('kelurahans'));
    }

    public function destroy(Kecamatan $kecamatan): JsonResponse
    {
        $used = $kecamatan->kelurahans()->exists()
            || Blc::where('kecamatan_id', $kecamatan->id)->exists()
            || Participant::where('kecamatan_id', $kecamatan->id)->exists()
            || ActivityReport::where('kecamatan_id', $kecamatan->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Kecamatan masih dipakai (punya kelurahan atau dipakai data lain) sehingga tidak bisa dihapus.',
            ], 409);
        }

        $kecamatan->delete();

        return response()->json(['message' => 'Kecamatan dihapus.']);
    }
}
