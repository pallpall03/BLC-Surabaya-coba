<?php

namespace App\Modules\Pengguna\Infrastructure\Repositories;

use App\Modules\Pengguna\Contracts\PenggunaRepositoryInterface;
use App\Models\User;
use App\Models\Blc;
use App\Models\TrainingSession;
use App\Models\Participant;
use App\Models\Visit;
use App\Models\ParticipantCategory;
use App\Models\VisitorCategory;
use App\Enums\UserRole;

class PenggunaRepository implements PenggunaRepositoryInterface
{
    public function getAvailableBlcs(): array
    {
        return Blc::where('is_active', true)
            ->where('show_on_landing', true)
            ->get(['id', 'name', 'slug', 'region', 'address', 'phone', 'latitude', 'longitude'])
            ->toArray();
    }

    public function getAvailableTrainingSessions(): array
    {
        return TrainingSession::whereHas('classes', fn ($q) => $q->whereHas('blc', fn ($b) => $b->where('is_active', true)))
            ->with(['classes.blc', 'classes.instruktur'])
            ->get(['id', 'name', 'start_time', 'end_time'])
            ->toArray();
    }

    public function getParticipantProfile(int $userId): ?array
    {
        $user = User::with('participant.participantCategory')->find($userId);
        if (!$user || $user->role !== UserRole::Pengguna || !$user->participant) {
            return null;
        }

        return [
            'id' => $user->participant->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'participant_category' => $user->participant->participantCategory?->name,
            'participant_category_id' => $user->participant->participant_category_id,
        ];
    }

    public function getVisitHistory(int $userId): array
    {
        return Visit::where('user_id', $userId)
            ->with(['blc', 'visitorCategory'])
            ->latest('visited_at')
            ->get(['id', 'blc_id', 'visitor_category_id', 'visited_at', 'notes'])
            ->toArray();
    }

    public function registerTraining(int $userId, int $trainingSessionId, int $blcId): bool
    {
        $user = User::find($userId);
        if (!$user || $user->role !== UserRole::Pengguna) {
            return false;
        }

        $participant = Participant::firstOrCreate(
            ['user_id' => $userId],
            ['name' => $user->name, 'participant_category_id' => ParticipantCategory::where('slug', 'umum')->first()?->id]
        );

        // Logic to register for training would go here
        // This is a placeholder - actual implementation depends on business logic
        return true;
    }

    public function registerVisit(int $userId, int $blcId, int $visitorCategoryId): bool
    {
        $user = User::find($userId);
        if (!$user || $user->role !== UserRole::Pengguna) {
            return false;
        }

        Visit::create([
            'user_id' => $userId,
            'blc_id' => $blcId,
            'visitor_category_id' => $visitorCategoryId,
            'visited_at' => now(),
        ]);

        return true;
    }
}