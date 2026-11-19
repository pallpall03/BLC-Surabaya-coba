<?php

namespace App\Modules\Koordinator\Infrastructure\Repositories;

use App\Modules\Koordinator\Contracts\KoordinatorRepositoryInterface;
use App\Models\User;
use App\Models\Blc;
use App\Models\TrainingSession;
use App\Models\Participant;
use App\Models\Visit;
use App\Enums\UserRole;

class KoordinatorRepository implements KoordinatorRepositoryInterface
{
    public function getManagedBlcs(int $koordinatorId): array
    {
        $koordinator = User::find($koordinatorId);
        if (!$koordinator || $koordinator->role !== UserRole::Koordinator) {
            return [];
        }

        return $koordinator->blcs()
            ->where('blcs.is_active', true)
            ->get(['id', 'name', 'slug', 'region', 'address', 'phone'])
            ->toArray();
    }

    public function getInstruktursByBlc(int $blcId): array
    {
        return User::where('role', UserRole::Instruktur)
            ->where('is_active', true)
            ->whereHas('blcs', fn ($q) => $q->where('blcs.id', $blcId))
            ->with(['blcs' => fn ($q) => $q->where('blcs.id', $blcId)])
            ->get(['id', 'name', 'email', 'phone', 'username', 'kantorku_id'])
            ->toArray();
    }

    public function getParticipantsByBlc(int $blcId): array
    {
        return Participant::whereHas('blc', fn ($q) => $q->where('blcs.id', $blcId))
            ->with(['participantCategory', 'blc'])
            ->get(['id', 'name', 'email', 'phone', 'participant_category_id', 'blc_id'])
            ->toArray();
    }

    public function getVisitsByBlc(int $blcId): array
    {
        return Visit::where('blc_id', $blcId)
            ->with(['visitorCategory', 'blc'])
            ->latest('visited_at')
            ->get(['id', 'visitor_category_id', 'blc_id', 'visited_at', 'notes'])
            ->toArray();
    }

    public function getTrainingSessionsByBlc(int $blcId): array
    {
        return TrainingSession::whereHas('classes', fn ($q) => $q->where('blc_id', $blcId))
            ->with(['classes' => fn ($q) => $q->where('blc_id', $blcId)->with('instruktur')])
            ->get(['id', 'name', 'start_time', 'end_time'])
            ->toArray();
    }
}