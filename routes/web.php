<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\SafetyDashboardController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\WorkPermitDashboardController;
use App\Http\Controllers\NearMissController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ========== AUTHENTICATION ROUTES ==========
// Guest routes (only accessible when NOT logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Logout route (only accessible when logged in)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ========== PROTECTED DASHBOARD ROUTES (Require Authentication) ==========
Route::middleware('auth')->group(function () {
    // Safety Dashboard Routes
    Route::prefix('dashboard')->group(function () {
        // Main Safety Dashboard (Monthly view with charts)
        Route::get('safety', [SafetyDashboardController::class, 'index'])
            ->name('dashboard.safety');
        Route::post('safety/store', [SafetyDashboardController::class, 'store'])
            ->name('dashboard.safety.store');
        Route::get('safety/export', [SafetyDashboardController::class, 'export'])
            ->name('dashboard.safety.export');
        Route::get('safety/export-pdf', [SafetyDashboardController::class, 'exportPDF'])
            ->name('dashboard.safety.export-pdf');
    });

    Route::post('/period/store', [PeriodController::class, 'store'])->name('period.store');

    // Work Permit Dashboard Routes
    Route::get('/dashboard/work-permit', [WorkPermitDashboardController::class, 'index'])->name('dashboard.work-permit');
    Route::post('/dashboard/work-permit/store',[WorkPermitDashboardController::class, 'store'])->name('dashboard.work-permit.store');
    Route::get('/dashboard/work-permit/create',[WorkPermitDashboardController::class, 'create'])->name('dashboard.work-permit.create');
    Route::get('/dashboard/work-permit/export', [WorkPermitDashboardController::class, 'export'])->name('dashboard.work-permit.export');
    Route::get('/dashboard/work-permit/export-pdf', [WorkPermitDashboardController::class, 'exportPDF'])->name('dashboard.work-permit.export-pdf');

    // Near Miss Dashboard Routes
    Route::get('/near-miss', [NearMissController::class,'index'])->name('near-miss.dashboard');
    Route::post('/near-miss/store', [NearMissController::class,'store'])->name('near-miss.store');
    Route::put('/near-miss/{id}/status', [NearMissController::class,'updateStatus'])->name('near-miss.updateStatus');

    // Import Routes
    Route::prefix('import')->name('import.')->group(function () {
        // Safety Metrics Import
        Route::get('/safety-metrics', [ImportController::class, 'showSafetyMetricsImport'])->name('safety-metrics');
        Route::post('/safety-metrics/process', [ImportController::class, 'importSafetyMetrics'])->name('safety-metrics.process');
        Route::get('/safety-metrics/sample', [ImportController::class, 'downloadSafetyMetricsSample'])->name('safety-metrics.sample');

        // Work Permit Import
        Route::get('/work-permit', [ImportController::class, 'showWorkPermitImport'])->name('work-permit');
        Route::post('/work-permit/process', [ImportController::class, 'importWorkPermit'])->name('work-permit.process');
        Route::get('/work-permit/sample', [ImportController::class, 'downloadWorkPermitSample'])->name('work-permit.sample');

        // Near Miss Import
        Route::get('/near-miss', [ImportController::class, 'showNearMissImport'])->name('near-miss');
        Route::post('/near-miss/process', [ImportController::class, 'importNearMiss'])->name('near-miss.process');
        Route::get('/near-miss/sample', [ImportController::class, 'downloadNearMissSample'])->name('near-miss.sample');
    });
});
