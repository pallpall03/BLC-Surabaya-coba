<?php

use App\Modules\Instruktur\Http\Controllers\InstrukturController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:instruktur,koordinator,operator,super_admin'])->prefix('instruktur')->group(function () {
    Route::get('/dashboard', [InstrukturController::class, 'dashboard']);
    Route::get('/classes', [InstrukturController::class, 'classes']);
    Route::get('/classes/{classId}/participants', [InstrukturController::class, 'classParticipants']);
    Route::get('/classes/{classId}/attendance', [InstrukturController::class, 'classAttendance']);
});