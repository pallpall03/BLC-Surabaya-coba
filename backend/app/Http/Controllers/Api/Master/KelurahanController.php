<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\KelurahanResource;
use App\Models\ActivityReport;
use App\Models\Blc;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class KelurahanController extends Controller
{
    public function index(Kecamatan $kecamatan): AnonymousResourceCollection
    {
        return KelurahanResource::collection($kecamatan->kelurahans()->orderBy('name')->get());
    }

    public function store(Request $request, Kecamatan $kecamatan): JsonResponse
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('kelurahans', 'name')->where('kecamatan_id', $kecamatan->id),
            ],
        ]);

        $kelurahan = $kecamatan->kelurahans()->create($data);

        return (new KelurahanResource($kelurahan))->response()->setStatusCode(201);
    }

    public function update(Request $request, Kelurahan $kelurahan): KelurahanResource
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('kelurahans', 'name')
                    ->where('kecamatan_id', $kelurahan->kecamatan_id)
                    ->ignore($kelurahan->id),
            ],
        ]);

        $kelurahan->update($data);

        return new KelurahanResource($kelurahan);
    }

    public function destroy(Kelurahan $kelurahan): JsonResponse
    {
        $used = Blc::where('kelurahan_id', $kelurahan->id)->exists()
            || Participant::where('kelurahan_id', $kelurahan->id)->exists()
            || ActivityReport::where('kelurahan_id', $kelurahan->id)->exists();

        if ($used) {
            return response()->json([
                'message' => 'Kelurahan masih dipakai data lain sehingga tidak bisa dihapus.',
            ], 409);
        }

        $kelurahan->delete();

        return response()->json(['message' => 'Kelurahan dihapus.']);
    }
}
