<?php

namespace App\Modules\Koordinator\Contracts;

interface KoordinatorRepositoryInterface
{
    public function getManagedBlcs(int $koordinatorId): array;
    public function getInstruktursByBlc(int $blcId): array;
    public function getParticipantsByBlc(int $blcId): array;
    public function getVisitsByBlc(int $blcId): array;
    public function getTrainingSessionsByBlc(int $blcId): array;
}