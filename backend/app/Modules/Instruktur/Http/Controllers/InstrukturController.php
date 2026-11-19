<?php

namespace App\Modules\Instruktur\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Instruktur\Contracts\InstrukturRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstrukturController extends Controller
{
    public function __construct(
        private InstrukturRepositoryInterface $repository
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $blcs = $this->repository->getAssignedBlcs($user->id);
        $classes = $this->repository->getClasses($user->id);

        return response()->json([
            'assigned_blcs' => $blcs,
            'classes' => $classes,
        ]);
    }

    public function classes(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->repository->getClasses($user->id));
    }

    public function classParticipants(Request $request, int $classId): JsonResponse
    {
        $user = $request->user();
        // Verify instruktur owns this class
        $classes = $this->repository->getClasses($user->id);
        $classIds = array_column($classes, 'id');
        if (!in_array($classId, $classIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->repository->getParticipantsByClass($classId));
    }

    public function classAttendance(Request $request, int $classId): JsonResponse
    {
        $user = $request->user();
        $classes = $this->repository->getClasses($user->id);
        $classIds = array_column($classes, 'id');
        if (!in_array($classId, $classIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($this->repository->getAttendanceByClass($classId));
    }
}