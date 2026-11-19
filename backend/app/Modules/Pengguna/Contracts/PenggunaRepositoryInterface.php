<?php

namespace App\Modules\Pengguna\Contracts;

interface PenggunaRepositoryInterface
{
    public function getAvailableBlcs(): array;
    public function getAvailableTrainingSessions(): array;
    public function getParticipantProfile(int $userId): ?array;
    public function getVisitHistory(int $userId): array;
    public function registerTraining(int $userId, int $trainingSessionId, int $blcId): bool;
    public function registerVisit(int $userId, int $blcId, int $visitorCategoryId): bool;
}