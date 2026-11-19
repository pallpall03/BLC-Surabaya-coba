<?php

namespace App\Modules\Pengguna\Providers;

use Illuminate\Support\ServiceProvider;
use App\Modules\Pengguna\Contracts\PenggunaRepositoryInterface;
use App\Modules\Pengguna\Infrastructure\Repositories\PenggunaRepository;

class PenggunaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PenggunaRepositoryInterface::class, PenggunaRepository::class);
    }

    public function boot(): void
    {
        //
    }
}