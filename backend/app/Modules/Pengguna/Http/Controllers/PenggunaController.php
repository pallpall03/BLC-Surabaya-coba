<?php

namespace App\Modules\Pengguna\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Pengguna\Contracts\PenggunaRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function __construct(
        private PenggunaRepositoryInterface $repository
    ) {}

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'available_blcs' => $this->repository->getAvailableBlcs(),
            'available_training_sessions' => $this->repository->getAvailableTrainingSessions(),
        ]);
    }

    public function blcs(): JsonResponse
    {
        return response()->json($this->repository->getAvailableBlcs());
    }

    public function trainingSessions(): JsonResponse
    {
        return response()->json($this->repository->getAvailableTrainingSessions());
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $this->repository->getParticipantProfile($user->id);
        $visits = $this->repository->getVisitHistory($user->id);

        return response()->json([
            'profile' => $profile,
            'visit_history' => $visits,
        ]);
    }

    public function registerTraining(Request $request): JsonResponse
    {
        $request->validate([
            'training_session_id' => 'required|integer|exists:training_sessions,id',
            'blc_id' => 'required|integer|exists:blcs,id',
        ]);

        $user = $request->user();
        $success = $this->repository->registerTraining($user->id, $request->training_session_id, $request->blc_id);

        if (!$success) {
            return response()->json(['message' => 'Gagal mendaftar pelatihan'], 400);
        }

        return response()->json(['message' => 'Berhasil mendaftar pelatihan']);
    }

    public function registerVisit(Request $request): JsonResponse
    {
        $request->validate([
            'blc_id' => 'required|integer|exists:blcs,id',
            'visitor_category_id' => 'required|integer|exists:visitor_categories,id',
        ]);

        $user = $request->user();
        $success = $this->repository->registerVisit($user->id, $request->blc_id, $request->visitor_category_id);

        if (!$success) {
            return response()->json(['message' => 'Gagal mencatat kunjungan'], 400);
        }

        return response()->json(['message' => 'Kunjungan berhasil dicatat']);
    }
}