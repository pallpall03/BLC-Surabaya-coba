<?php

namespace App\Modules\SuperAdmin\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\SuperAdmin\Contracts\SuperAdminRepositoryInterface;
use App\Modules\SuperAdmin\Infrastructure\Repositories\SuperAdminRepository;

class SuperAdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SuperAdminRepositoryInterface::class, SuperAdminRepository::class);
    }

    public function boot(): void
    {
        //
    }
}