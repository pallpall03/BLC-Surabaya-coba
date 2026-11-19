<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait MasterDataHelpers
{
    /** Staf = admin, koordinator, instruktur. Role user hanya melihat data aktif. */
    protected function isStaff(Request $request): bool
    {
        return $request->user()->role->isStaff();
    }

    protected function onlyVisible(Builder $query, Request $request): Builder
    {
        return $this->isStaff($request) ? $query : $query->where('is_active', true);
    }

    protected function ensureVisible(Request $request, Model $model): void
    {
        abort_if(! $this->isStaff($request) && ! $model->is_active, 404);
    }

    protected function perPage(Request $request): int
    {
        return min(max($request->integer('per_page', 15), 1), 100);
    }

    protected function inUse(string $message): JsonResponse
    {
        return response()->json(['message' => $message], 409);
    }
}
