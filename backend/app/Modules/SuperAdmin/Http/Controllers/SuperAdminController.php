<?php

namespace App\Modules\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\Contracts\SuperAdminRepositoryInterface;
use Illuminate\Http\JsonResponse;

class SuperAdminController extends Controller
{
    public function __construct(
        private SuperAdminRepositoryInterface $repository
    ) {}

    public function dashboard(): JsonResponse
    {
        $stats = $this->repository->getDashboardStats();
        $overview = $this->repository->getSystemOverview();

        return response()->json([
            'stats' => $stats,
            'overview' => $overview,
        ]);
    }

    public function users(): JsonResponse
    {
        // Delegate to existing User management endpoints
        return response()->json(['message' => 'Use /api/v1/users endpoints']);
    }
}