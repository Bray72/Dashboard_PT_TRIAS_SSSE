<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\SafetyDashboardController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\WorkPermitDashboardController;
use App\Http\Controllers\NearMissController;

Route::get('/', function () {
    return view('welcome');
});

// ========== SAFETY DASHBOARD ROUTES ==========
Route::prefix('dashboard')->group(function () {
    // Main Safety Dashboard (Monthly view with charts)
    Route::get('safety', [SafetyDashboardController::class, 'index'])
        ->name('dashboard.safety');
    Route::post('safety/store', [SafetyDashboardController::class, 'store'])
        ->name('dashboard.safety.store');
});

Route::post('/period/store', [PeriodController::class, 'store'])->name('period.store');

Route::get('/dashboard/work-permit', [WorkPermitDashboardController::class, 'index'])->name('dashboard.work-permit');
Route::post('/dashboard/work-permit/store',[WorkPermitDashboardController::class, 'store'])->name('dashboard.work-permit.store');

Route::get('/dashboard/work-permit/create',[WorkPermitDashboardController::class, 'create'])->name('dashboard.work-permit.create');

Route::prefix('near-miss')->group(function () {
    Route::get('/', [NearMissController::class, 'index'])->name('near-miss.index');
    Route::get('/create', [NearMissController::class, 'create'])->name('near-miss.create');
    Route::post('/store', [NearMissController::class, 'store'])->name('near-miss.store');
});
