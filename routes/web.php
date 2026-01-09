<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\SafetyDashboardController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\WorkPermitDashboardController;

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

// Redirect old dashboard route
Route::get('dashboard', function () {
    return redirect()->route('dashboard.safety');
});

Route::post('/period/store', [PeriodController::class, 'store'])->name('period.store');

Route::get('/dashboard/work-permit', [WorkPermitDashboardController::class, 'index'])->name('dashboard.work-permit');
Route::post('/dashboard/work-permit/store',[WorkPermitDashboardController::class, 'store'])->name('dashboard.work-permit.store');

Route::get('/dashboard/work-permit/create',[WorkPermitDashboardController::class, 'create'])->name('dashboard.work-permit.create');
