<?php

namespace App\Modules\SuperAdmin\Infrastructure\Repositories;

use App\Modules\SuperAdmin\Contracts\SuperAdminRepositoryInterface;
use App\Models\User;
use App\Models\Blc;
use App\Models\TrainingSession;
use App\Models\ParticipantCategory;
use App\Models\VisitorCategory;

class SuperAdminRepository implements SuperAdminRepositoryInterface
{
    public function getDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_blcs' => Blc::count(),
            'total_training_sessions' => TrainingSession::count(),
            'total_participant_categories' => ParticipantCategory::count(),
            'total_visitor_categories' => VisitorCategory::count(),
            'users_by_role' => User::selectRaw('role, count(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
            'active_blcs' => Blc::where('is_active', true)->count(),
            'inactive_blcs' => Blc::where('is_active', false)->count(),
        ];
    }

    public function getSystemOverview(): array
    {
        return [
            'recent_users' => User::latest()->take(10)->get(['id', 'name', 'email', 'role', 'created_at']),
            'recent_blcs' => Blc::latest()->take(10)->get(['id', 'name', 'slug', 'is_active', 'created_at']),
            'users_per_blc' => Blc::withCount('users')->get(['id', 'name', 'users_count']),
        ];
    }
}