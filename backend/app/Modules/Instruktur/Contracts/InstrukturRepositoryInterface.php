<?php

namespace App\Modules\Instruktur\Contracts;

interface InstrukturRepositoryInterface
{
    public function getAssignedBlcs(int $instrukturId): array;
    public function getClasses(int $instrukturId): array;
    public function getParticipantsByClass(int $classId): array;
    public function getAttendanceByClass(int $classId): array;
}