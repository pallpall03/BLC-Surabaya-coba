<?php

namespace App\Modules\Operator\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Operator\Contracts\OperatorRepositoryInterface;
use Illuminate\Http\JsonResponse;

class OperatorController extends Controller
{
    public function __construct(
        private OperatorRepositoryInterface $repository
    ) {}

    public function dashboard(): JsonResponse
    {
        $blcs = $this->repository->getManagedBlcs();
        $koordinators = $this->repository->getAssignedKoordinators();
        $instrukturs = $this->repository->getAssignedInstrukturs();

        return response()->json([
            'managed_blcs' => $blcs,
            'koordinators' => $koordinators,
            'instrukturs' => $instrukturs,
        ]);
    }

    public function blcs(): JsonResponse
    {
        return response()->json($this->repository->getManagedBlcs());
    }

    public function koordinators(): JsonResponse
    {
        return response()->json($this->repository->getAssignedKoordinators());
    }

    public function instrukturs(): JsonResponse
    {
        return response()->json($this->repository->getAssignedInstrukturs());
    }
}