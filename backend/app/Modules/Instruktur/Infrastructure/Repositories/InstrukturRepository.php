<?php

namespace App\Modules\Instruktur\Infrastructure\Repositories;

use App\Modules\Instruktur\Contracts\InstrukturRepositoryInterface;
use App\Models\User;
use App\Models\Blc;
use App\Models\ClassModel;
use App\Models\Participant;
use App\Models\Attendance;
use App\Enums\UserRole;

class InstrukturRepository implements InstrukturRepositoryInterface
{
    public function getAssignedBlcs(int $instrukturId): array
    {
        $instruktur = User::find($instrukturId);
        if (!$instruktur || $instruktur->role !== UserRole::Instruktur) {
            return [];
        }

        return $instruktur->blcs()
            ->where('blcs.is_active', true)
            ->get(['id', 'name', 'slug', 'region'])
            ->toArray();
    }

    public function getClasses(int $instrukturId): array
    {
        return ClassModel::where('instruktur_id', $instrukturId)
            ->with(['blc', 'trainingSession', 'participants.participantCategory'])
            ->get(['id', 'name', 'blc_id', 'training_session_id', 'instruktur_id', 'schedule'])
            ->toArray();
    }

    public function getParticipantsByClass(int $classId): array
    {
        return Participant::whereHas('classes', fn ($q) => $q->where('classes.id', $classId))
            ->with(['participantCategory'])
            ->get(['id', 'name', 'email', 'phone', 'participant_category_id'])
            ->toArray();
    }

    public function getAttendanceByClass(int $classId): array
    {
        return Attendance::where('class_id', $classId)
            ->with(['participant', 'trainingSession'])
            ->get(['id', 'participant_id', 'class_id', 'training_session_id', 'status', 'attended_at', 'notes'])
            ->toArray();
    }
}