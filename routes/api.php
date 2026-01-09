<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SafetyMetricsController;
use App\Models\Company;
use App\Models\Period;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ========== SAFETY METRICS API ROUTES ==========
Route::prefix('safety')->group(function () {
    // Get all companies
    Route::get('/companies', function () {
        return response()->json(Company::all());
    });

    // Get periods by year
    Route::get('/periods', function (Request $request) {
        $year = $request->query('year') ?? date('Y');
        return response()->json(Period::where('year', $year)->orderBy('month')->get());
    });

    // Get metrics with calculations (SR, FR, IR)
    Route::get('/metrics', [SafetyMetricsController::class, 'getMetrics']);
    
    // Get summary metrics for selected period
    Route::get('/metrics/summary', [SafetyMetricsController::class, 'summary']);
});
