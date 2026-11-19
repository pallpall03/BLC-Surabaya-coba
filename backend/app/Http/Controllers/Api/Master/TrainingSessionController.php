<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Api\Concerns\MasterDataHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingSessionResource;
use App\Models\ActivityReport;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Sesi pelatihan (global, dipakai semua BLC): nama + jam mulai dan selesai. */
class TrainingSessionController extends Controller
{
    use MasterDataHelpers;

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->onlyVisible(TrainingSession::query(), $request)->orderBy('start_time')->orderBy('name');

        return TrainingSessionResource::collection($query->get());
    }

    public function show(Request $request, TrainingSession $trainingSession): TrainingSessionResource
    {
        $this->ensureVisible($request, $trainingSession);

        return new TrainingSessionResource($trainingSession);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());

        $this->assertEndAfterStart($data['start_time'], $data['end_time']);

        $session = TrainingSession::create($data)->refresh();

        return (new TrainingSessionResource($session))->response()->setStatusCode(201);
    }

    public function update(Request $request, TrainingSession $trainingSession): TrainingSessionResource
    {
        $data = $request->validate($this->rules($trainingSession));

        $this->assertEndAfterStart(
            $data['start_time'] ?? substr((string) $trainingSession->start_time, 0, 5),
            $data['end_time'] ?? substr((string) $trainingSession->end_time, 0, 5),
        );

        $trainingSession->fill($data)->save();

        return new TrainingSessionResource($trainingSession->refresh());
    }

    public function destroy(TrainingSession $trainingSession): JsonResponse
    {
        if ($trainingSession->classes()->exists() || ActivityReport::where('training_session_id', $trainingSession->id)->exists()) {
            return $this->inUse('Sesi sudah dipakai kelas atau laporan. Nonaktifkan saja, jangan dihapus.');
        }

        $trainingSession->delete();

        return response()->json(['message' => 'Sesi dihapus.']);
    }

    /** @return array<string, array<int, mixed>> */
    private function rules(?TrainingSession $session = null): array
    {
        $sometimes = $session ? ['sometimes'] : [];

        return [
            'name' => [...$sometimes, 'required', 'string', 'max:100', Rule::unique('training_sessions', 'name')->ignore($session?->id)],
            'start_time' => [...$sometimes, 'required', 'date_format:H:i'],
            'end_time' => [...$sometimes, 'required', 'date_format:H:i'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => [...$sometimes, 'boolean'],
        ];
    }

    private function assertEndAfterStart(string $start, string $end): void
    {
        // Format H:i dengan nol di depan, jadi perbandingan teks sama dengan perbandingan waktu.
        if ($end <= $start) {
            throw ValidationException::withMessages([
                'end_time' => 'Jam selesai harus setelah jam mulai.',
            ]);
        }
    }
}
