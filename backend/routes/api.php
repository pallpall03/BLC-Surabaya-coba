<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Master\BlcController;
use App\Http\Controllers\Api\Master\InstructorController;
use App\Http\Controllers\Api\Master\KecamatanController;
use App\Http\Controllers\Api\Master\KelurahanController;
use App\Http\Controllers\Api\Master\KoordinatorController;
use App\Http\Controllers\Api\Master\ParticipantCategoryController;
use App\Http\Controllers\Api\Master\TrainingSessionController;
use App\Http\Controllers\Api\Master\VisitorCategoryController;
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
    Route::middleware(['auth:sanctum', 'active', 'role:super_admin,operator'])
        ->get('/admin/ping', fn () => response()->json(['message' => 'pong admin']));

    // ---------------------------------------------------------------------
    // Fase 3: master data (existing routes)
    // ---------------------------------------------------------------------
    Route::middleware(['auth:sanctum', 'active'])->group(function () {

        // Baca: semua pengguna yang sudah login (role user hanya melihat data aktif).
        Route::apiResource('kecamatans', KecamatanController::class)->only(['index', 'show']);
        Route::get('kecamatans/{kecamatan}/kelurahans', [KelurahanController::class, 'index']);
        Route::apiResource('blcs', BlcController::class)->only(['index', 'show']);
        Route::apiResource('participant-categories', ParticipantCategoryController::class)->only(['index', 'show']);
        Route::apiResource('visitor-categories', VisitorCategoryController::class)->only(['index', 'show']);
        Route::apiResource('training-sessions', TrainingSessionController::class)
            ->parameters(['training-sessions' => 'trainingSession'])
            ->only(['index', 'show']);

        // Data koordinator yang terhubung ke beberapa BLC.
        Route::middleware('role:super_admin,operator,koordinator')->group(function () {
            Route::get('blcs/{blc}/instructors', [BlcController::class, 'instructors']);
            Route::get('instructors', [InstructorController::class, 'index']);
            Route::get('instructors/{instructor}', [InstructorController::class, 'show']);
            Route::get('koordinator', [KoordinatorController::class, 'index']);
            Route::get('koordinator/{koordinator}', [KoordinatorController::class, 'show']);
        });

        // Tulis: hanya super_admin, operator.
        Route::middleware('role:super_admin,operator')->group(function () {
            Route::apiResource('kecamatans', KecamatanController::class)->except(['index', 'show']);
            Route::post('kecamatans/{kecamatan}/kelurahans', [KelurahanController::class, 'store']);
            Route::put('kelurahans/{kelurahan}', [KelurahanController::class, 'update']);
            Route::patch('kelurahans/{kelurahan}', [KelurahanController::class, 'update']);
            Route::delete('kelurahans/{kelurahan}', [KelurahanController::class, 'destroy']);

            Route::apiResource('blcs', BlcController::class)->except(['index', 'show']);
            Route::put('blcs/{blc}/instructors', [BlcController::class, 'syncInstructors']);

            Route::post('instructors', [InstructorController::class, 'store']);
            Route::put('instructors/{instructor}', [InstructorController::class, 'update']);
            Route::patch('instructors/{instructor}', [InstructorController::class, 'update']);

            Route::post('koordinator', [KoordinatorController::class, 'store']);
            Route::put('koordinator/{koordinator}', [KoordinatorController::class, 'update']);
            Route::patch('koordinator/{koordinator}', [KoordinatorController::class, 'update']);
            Route::delete('koordinator/{koordinator}', [KoordinatorController::class, 'destroy']);

            Route::apiResource('participant-categories', ParticipantCategoryController::class)->except(['index', 'show']);
            Route::apiResource('visitor-categories', VisitorCategoryController::class)->except(['index', 'show']);
            Route::apiResource('training-sessions', TrainingSessionController::class)
                ->parameters(['training-sessions' => 'trainingSession'])
                ->except(['index', 'show']);
        });
    });

    // ---------------------------------------------------------------------
    // Clean Architecture Module Routes
    // ---------------------------------------------------------------------
    require_once __DIR__ . '/../app/Modules/SuperAdmin/Http/routes/api.php';
    require_once __DIR__ . '/../app/Modules/Operator/Http/routes/api.php';
    require_once __DIR__ . '/../app/Modules/Koordinator/Http/routes/api.php';
    require_once __DIR__ . '/../app/Modules/Instruktur/Http/routes/api.php';
    require_once __DIR__ . '/../app/Modules/Pengguna/Http/routes/api.php';
});
