<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Period;
use Illuminate\Http\Request;

class SafetyDashboardController extends Controller
{
    /**
     * Display safety dashboard with charts
     * Shows monthly safety metrics (SR, FR, IR) for selected company and year
     */
    public function index(Request $request)
    {
        $year = $request->year ?? Period::orderBy('year', 'desc')->value('year') ?? date('Y');
        
        // Get all companies for dropdown
        $companies = Company::all();
        
        // Set company ID - from request or default to first company
        $companyId = $request->company_id ?? $companies->first()?->id;
        
        $month = $request->get('month'); 
        $months = $month ? [$month] : range(1, 12);
        $gaugeMonth = $request->get('gauge_month');

        // Now call getAllMonthlyMetrics with the correct companyId
        $allMetrics = $this->getAllMonthlyMetrics($year, $companyId);

        $gaugeFR = [];
        $gaugeSR = [];
        $gaugeIR = [];

        if ($gaugeMonth) {
            // Get single month gauge data
            $gaugeFR[$gaugeMonth] = $allMetrics[$gaugeMonth]['fr'] ?? 0;
            $gaugeSR[$gaugeMonth] = $allMetrics[$gaugeMonth]['sr'] ?? 0;
            $gaugeIR[$gaugeMonth] = $allMetrics[$gaugeMonth]['ir'] ?? 0;
        } else {
            // Get all months gauge data
            for ($m = 1; $m <= 12; $m++) {
                $gaugeFR[$m] = $allMetrics[$m]['fr'] ?? 0;
                $gaugeSR[$m] = $allMetrics[$m]['sr'] ?? 0;
                $gaugeIR[$m] = $allMetrics[$m]['ir'] ?? 0;
            }
        }

        $monthlyFR = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyFR[] = $allMetrics[$m]['fr'] ?? 0;
        }

        // Get periods for the selected year
        $periods = Period::where('year', $year)
            ->orderBy('month')
            ->get();

        // Get statistics for the selected company and year
        $statistics = CompanyStatistic::with(['company', 'period'])
            ->where('company_id', $companyId)
            ->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get();

        // Prepare chart data
        $chartData = $this->prepareChartData($periods, $statistics);

        $monthNames = $this->getMonthNames();

        $monthlySummary = $this->getMonthlySummary($companyId, $year, $gaugeMonth);

        return view('dashboard.index', compact(
            'companies',
            'chartData',
            'year',
            'companyId',
            'periods',
            'statistics',
            'monthNames',
            'monthlyFR',
            'month',
            'gaugeMonth',
            'gaugeFR',
            'gaugeSR',
            'gaugeIR',
            'monthlySummary' // pass summary data to view
        ));
    }

    /**
     * Store safety metrics data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'         => 'required|exists:companies,id',
            'period_id'          => 'required|exists:periods,id',
            'man_hours'          => 'required|integer|min:0',
            'employee'           => 'required|integer|min:0',
            'lta'                => 'required|integer|min:0',
            'lost_work_days'     => 'required|integer|min:0',
            'lost_time'          => 'required|integer|min:0',
            'kecelakaan_kerja'   => 'required|integer|min:0',
        ]);

        CompanyStatistic::updateOrCreate(
            [
                'company_id' => $validated['company_id'],
                'period_id'  => $validated['period_id'],
            ],
            $validated
        );

        return redirect()->back()->with('success', 'Data keselamatan kerja berhasil disimpan!');
    }

    /**
     * Prepare data for chart visualization
     */
    private function prepareChartData($periods, $statistics)
    {
        $chartData = [
            'labels'     => [],
            'man_hours'  => [],
            'employee'   => [],
            'sr'         => [],
            'fr'         => [],
            'ir'         => [],
        ];

        foreach ($periods as $period) {
            $stat = $statistics->where('period_id', $period->id)->first();

            $chartData['labels'][] = $this->getMonthName($period->month);
            $chartData['man_hours'][] = $stat?->man_hours ?? 0;
            $chartData['employee'][] = $stat?->employee ?? 0;

            // Calculate safety metrics
            if ($stat) {
                $chartData['sr'][] = $stat->man_hours > 0 ? ($stat->lost_time * 1000000) / $stat->man_hours : 0;
                $chartData['fr'][] = $stat->man_hours > 0 ? ($stat->lost_work_days * 1000000) / $stat->man_hours : 0;
                $chartData['ir'][] = $stat->employee > 0 ? ($stat->kecelakaan_kerja * 100) / $stat->employee : 0;
            } else {
                $chartData['sr'][] = 0;
                $chartData['fr'][] = 0;
                $chartData['ir'][] = 0;
            }
        }

        return $chartData;
    }

    /**
     * Convert month number to name
     */
    private function getMonthName($month)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return $months[$month - 1] ?? $month;
    }

    /**
     * Get all month names array for view
     */
    private function getMonthNames()
    {
        return [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];
    }

    // Returns array with FR, SR, IR for each month
    private function getAllMonthlyMetrics($year, $companyId = null)
    {
        $statistics = CompanyStatistic::select(
                'company_statistics.*'
            )
            ->join('periods', 'company_statistics.period_id', '=', 'periods.id')
            ->where('periods.year', $year);

        if ($companyId) {
            $statistics = $statistics->where('company_statistics.company_id', $companyId);
        }

        $statistics = $statistics->get()
            ->groupBy(function ($item) {
                return $item->period->month;
            });

        $metrics = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthStats = $statistics->get($month, collect());

            $manHours = $monthStats->sum('man_hours');
            $employees = $monthStats->sum('employee');
            $lta = $monthStats->sum('lta');
            $lostTime = $monthStats->sum('lost_time');
            $accidents = $monthStats->sum('kecelakaan_kerja');

            $metrics[$month] = [
                'fr' => $manHours > 0 ? round(($lta / $manHours) * 1_000_000, 2) : 0,
                'sr' => $manHours > 0 ? round(($lostTime * 1_000_000) / $manHours, 2) : 0,
                'ir' => $employees > 0 ? round(($accidents * 100) / $employees, 2) : 0,
            ];
        }

        return $metrics;
    }

    private function getMonthlySummary($companyId, $year, $month = null)
    {
        $query = CompanyStatistic::with('period')
            ->where('company_id', $companyId)
            ->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            });

        // If specific month is selected, get only that month's data
        if ($month) {
            $query->whereHas('period', function ($q) use ($month) {
                $q->where('month', $month);
            });
        }

        $stats = $query->get();

        return [
            'man_hours' => $stats->sum('man_hours'),
            'employee' => $stats->sum('employee'),
            'lta' => $stats->sum('lta'),
            'lost_work_days' => $stats->sum('lost_work_days'),
            'lost_time' => $stats->sum('lost_time'),
            'kecelakaan_kerja' => $stats->sum('kecelakaan_kerja'),
        ];
    }

    // ========== LEGACY ROUTES (untuk backward compatibility) ==========

    public function indexLegacyA(Request $request)
    {
        return $this->index($request);
    }

    public function storeLegacyA(Request $request)
    {
        return $this->store($request);
    }

    public function indexLegacyB(Request $request)
    {
        return $this->index($request);
    }

    public function storeLegacyB(Request $request)
    {
        return $this->store($request);
    }

    public function indexLegacyC(Request $request)
    {
        return $this->index($request);
    }

    public function storeLegacyC(Request $request)
    {
        return $this->store($request);
    }
}
