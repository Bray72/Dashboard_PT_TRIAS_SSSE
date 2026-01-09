<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Period;
use Illuminate\Http\Request;

class SafetyMetricsController extends Controller
{
    public function getMetrics(Request $request)
    {
        $companyId = $request->query('company_id');
        $year = $request->query('year') ?? date('Y');

        // Get all companies
        $companies = Company::all();

        // Get periods in the year
        $periods = Period::where('year', $year)
            ->orderBy('month')
            ->get();

        // Get statistics
        $statistics = CompanyStatistic::with(['company', 'period'])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get();

        // Calculate metrics
        $metrics = [];
        foreach ($periods as $period) {
            $stat = $statistics
                ->where('period_id', $period->id)
                ->first();

            if ($stat) {
                $metrics[] = [
                    'period_id' => $period->id,
                    'month' => $period->month,
                    'year' => $period->year,
                    'man_hours' => $stat->man_hours,
                    'employee' => $stat->employee,
                    'lta' => $stat->lta,
                    'lost_work_days' => $stat->lost_work_days,
                    'lost_time' => $stat->lost_time,
                    'kecelakaan_kerja' => $stat->kecelakaan_kerja,
                    // Formula: SR = (lost_time × 1000000) / total_man_hours
                    'sr' => $stat->man_hours > 0 ? ($stat->lost_time * 1000000) / $stat->man_hours : 0,
                    // Formula: FR = (lost_work_days × 1000000) / total_man_hours
                    'fr' => $stat->man_hours > 0 ? ($stat->lost_work_days * 1000000) / $stat->man_hours : 0,
                    // Formula: IR = (total_kecelakaan_kerja × 100) / total_pekerja
                    'ir' => $stat->employee > 0 ? ($stat->kecelakaan_kerja * 100) / $stat->employee : 0,
                ];
            }
        }

        return response()->json([
            'data' => $metrics,
            'companies' => $companies,
            'year' => $year,
            'company_id' => $companyId ?? $companies->first()?->id
        ]);
    }

    public function summary(Request $request)
    {
        $companyId = $request->query('company_id');
        $year = $request->query('year') ?? date('Y');

        $statistics = CompanyStatistic::with(['company', 'period'])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get();

        // Calculate total metrics for the year
        $totalManHours = $statistics->sum('man_hours');
        $totalLostTime = $statistics->sum('lost_time');
        $totalLostDays = $statistics->sum('lost_work_days');
        $totalEmployee = $statistics->first()?->employee ?? 0;
        $totalKecelakaan = $statistics->sum('kecelakaan_kerja');

        return response()->json([
            'sr' => $totalManHours > 0 ? ($totalLostTime * 1000000) / $totalManHours : 0,
            'fr' => $totalManHours > 0 ? ($totalLostDays * 1000000) / $totalManHours : 0,
            'ir' => $totalEmployee > 0 ? ($totalKecelakaan * 100) / $totalEmployee : 0,
            'total_man_hours' => $totalManHours,
            'total_lost_time' => $totalLostTime,
            'total_lost_days' => $totalLostDays,
            'total_employee' => $totalEmployee,
            'total_kecelakaan' => $totalKecelakaan,
        ]);
    }
}
