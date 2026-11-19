<?php

use App\Modules\Pengguna\Http\Controllers\PenggunaController;
use Illuminate\Support\Facades\Route;

// Public routes for landing page
Route::prefix('pengguna')->group(function () {
    Route::get('/blcs', [PenggunaController::class, 'blcs']);
    Route::get('/training-sessions', [PenggunaController::class, 'trainingSessions']);
});

// Authenticated routes
Route::middleware(['auth:sanctum', 'role:pengguna'])->prefix('pengguna')->group(function () {
    Route::get('/dashboard', [PenggunaController::class, 'dashboard']);
    Route::get('/profile', [PenggunaController::class, 'profile']);
    Route::post('/register-training', [PenggunaController::class, 'registerTraining']);
    Route::post('/register-visit', [PenggunaController::class, 'registerVisit']);
});