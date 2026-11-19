<?php

use App\Modules\Koordinator\Http\Controllers\KoordinatorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:koordinator,operator,super_admin'])->prefix('koordinator')->group(function () {
    Route::get('/dashboard', [KoordinatorController::class, 'dashboard']);
    Route::get('/blcs', [KoordinatorController::class, 'blcs']);
    Route::get('/blcs/{blcId}/instrukturs', [KoordinatorController::class, 'blcInstrukturs']);
    Route::get('/blcs/{blcId}/participants', [KoordinatorController::class, 'blcParticipants']);
    Route::get('/blcs/{blcId}/visits', [KoordinatorController::class, 'blcVisits']);
    Route::get('/blcs/{blcId}/training-sessions', [KoordinatorController::class, 'blcTrainingSessions']);
});