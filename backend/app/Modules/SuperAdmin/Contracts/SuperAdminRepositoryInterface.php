<?php

namespace App\Modules\SuperAdmin\Contracts;

interface SuperAdminRepositoryInterface
{
    public function getDashboardStats(): array;
    public function getSystemOverview(): array;
}