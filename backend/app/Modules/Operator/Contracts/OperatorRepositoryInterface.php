<?php

namespace App\Modules\Operator\Contracts;

interface OperatorRepositoryInterface
{
    public function getManagedBlcs(): array;
    public function getAssignedKoordinators(): array;
    public function getAssignedInstrukturs(): array;
}