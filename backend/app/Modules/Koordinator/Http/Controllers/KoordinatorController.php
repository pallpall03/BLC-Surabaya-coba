<?php

namespace App\Modules\Koordinator\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Koordinator\Contracts\KoordinatorRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KoordinatorController extends Controller
{
    public function __construct(
        private KoordinatorRepositoryInterface $repository
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $blcs = $this->repository->getManagedBlcs($user->id);

        return response()->json([
            'managed_blcs' => $blcs,
            'blcs_count' => count($blcs),
        ]);
    }

    public function blcs(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->repository->getManagedBlcs($user->id));
    }

    public function blcInstrukturs(Request $request, int $blcId): JsonResponse
    {
        $user = $request->user();
        // Verify koordinator has access to this BLC
        $managedBlcIds = array_column($this->repository->getManagedBlcs($user->id), 'id');
        if (!in_array($blcId, $managedBlcIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->repository->getInstruktursByBlc($blcId));
    }

    public function blcParticipants(Request $request, int $blcId): JsonResponse
    {
        $user = $request->user();
        $managedBlcIds = array_column($this->repository->getManagedBlcs($user->id), 'id');
        if (!in_array($blcId, $managedBlcIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->repository->getParticipantsByBlc($blcId));
    }

    public function blcVisits(Request $request, int $blcId): JsonResponse
    {
        $user = $request->user();
        $managedBlcIds = array_column($this->repository->getManagedBlcs($user->id), 'id');
        if (!in_array($blcId, $managedBlcIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->repository->getVisitsByBlc($blcId));
    }

    public function blcTrainingSessions(Request $request, int $blcId): JsonResponse
    {
        $user = $request->user();
        $managedBlcIds = array_column($this->repository->getManagedBlcs($user->id), 'id');
        if (!in_array($blcId, $managedBlcIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->repository->getTrainingSessionsByBlc($blcId));
    }
}