<?php

namespace App\Modules\Instruktur\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Instruktur\Contracts\InstrukturRepositoryInterface;
use App\Modules\Instruktur\Infrastructure\Repositories\InstrukturRepository;

class InstrukturServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InstrukturRepositoryInterface::class, InstrukturRepository::class);
    }

    public function boot(): void
    {
        //
    }
}