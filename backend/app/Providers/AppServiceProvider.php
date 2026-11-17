<?php

namespace App\Providers;

use App\Services\Auth\FakeKantorkuClient;
use App\Services\Auth\HttpKantorkuClient;
use App\Services\Auth\KantorkuClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(KantorkuClient::class, fn () => match (config('kantorku.driver')) {
            'http' => new HttpKantorkuClient(),
            default => new FakeKantorkuClient(),
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Batas kasar per IP untuk endpoint login dan captcha (di luar hitungan gagal per akun).
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(20)->by((string) $request->ip()));
    }
}
