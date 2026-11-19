<?php

namespace App\Modules\Operator\Infrastructure\Repositories;

use App\Modules\Operator\Contracts\OperatorRepositoryInterface;
use App\Models\User;
use App\Models\Blc;
use App\Enums\UserRole;

class OperatorRepository implements OperatorRepositoryInterface
{
    public function getManagedBlcs(): array
    {
        return Blc::where('is_active', true)
            ->get(['id', 'name', 'slug', 'region'])
            ->toArray();
    }

    public function getAssignedKoordinators(): array
    {
        return User::where('role', UserRole::Koordinator)
            ->where('is_active', true)
            ->with('blcs:id,name,slug')
            ->get(['id', 'name', 'email', 'phone', 'kantorku_id', 'lokasi_blc_ids'])
            ->toArray();
    }

    public function getAssignedInstrukturs(): array
    {
        return User::where('role', UserRole::Instruktur)
            ->where('is_active', true)
            ->with('blcs:id,name,slug')
            ->get(['id', 'name', 'email', 'phone', 'username', 'kantorku_id', 'lokasi_blc_ids'])
            ->toArray();
    }
}