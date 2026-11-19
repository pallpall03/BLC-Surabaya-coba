<?php

namespace App\Modules\Koordinator\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Koordinator\Contracts\KoordinatorRepositoryInterface;
use App\Modules\Koordinator\Infrastructure\Repositories\KoordinatorRepository;

class KoordinatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(KoordinatorRepositoryInterface::class, KoordinatorRepository::class);
    }

    public function boot(): void
    {
        //
    }
}