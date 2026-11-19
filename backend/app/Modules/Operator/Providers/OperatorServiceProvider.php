<?php

namespace App\Modules\Operator\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Operator\Contracts\OperatorRepositoryInterface;
use App\Modules\Operator\Infrastructure\Repositories\OperatorRepository;

class OperatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OperatorRepositoryInterface::class, OperatorRepository::class);
    }

    public function boot(): void
    {
        //
    }
}