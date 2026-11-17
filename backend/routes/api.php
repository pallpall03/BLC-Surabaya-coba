<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/ping', fn () => response()->json([
        'message' => 'pong',
        'app' => config('app.name'),
        'time' => now()->toIso8601String(),
    ]));

    // Satu pintu login untuk semua role.
    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:login')->group(function () {
            Route::get('/captcha', [AuthController::class, 'captcha']);
            Route::post('/login', [AuthController::class, 'login']);
        });

        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Contoh route yang dibatasi per role (dipakai juga oleh test).
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin'])
        ->get('/admin/ping', fn () => response()->json(['message' => 'pong admin']));
});
