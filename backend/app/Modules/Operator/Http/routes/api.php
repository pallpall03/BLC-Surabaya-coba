<?php

use App\Modules\Operator\Http\Controllers\OperatorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:operator,super_admin'])->prefix('operator')->group(function () {
    Route::get('/dashboard', [OperatorController::class, 'dashboard']);
    Route::get('/blcs', [OperatorController::class, 'blcs']);
    Route::get('/koordinators', [OperatorController::class, 'koordinators']);
    Route::get('/instrukturs', [OperatorController::class, 'instrukturs']);
});