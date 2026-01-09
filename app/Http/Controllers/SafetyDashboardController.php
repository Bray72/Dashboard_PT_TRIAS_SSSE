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
        $companyId = $request->company_id;
        $year = $request->year ?? Period::orderBy('year', 'desc')->value('year') ?? date('Y');

        // Get all companies for dropdown
        $companies = Company::all();
        
        // Set default company if not selected
        $companyId = $companyId ?? $companies->first()?->id;

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

        return view('dashboard.safety.index', compact(
            'companies',
            'chartData',
            'year',
            'companyId',
            'periods',
            'statistics',
            'monthNames'
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
