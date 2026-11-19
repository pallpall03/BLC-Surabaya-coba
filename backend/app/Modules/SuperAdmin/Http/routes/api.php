<?php

use App\Modules\SuperAdmin\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
});