<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Period;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SafetyDashboardController extends Controller
{
     public function index(Request $request)
    {
        $companyId = $request->company_id;
        $year = $request->year ?? Period::orderBy('year', 'desc')->value('year') ?? date('Y');
        $gaugeMonth = $request->gauge_month ?? null;

        // List perusahaan (untuk tab / dropdown)
        $companies = Company::all();
        $displayMonth = $gaugeMonth; 

        $companyId = $request->company_id 
        ?? $companies->first()?->id;

        // Ambil periode dalam 1 tahun
        $periods = Period::where('year', $year)
            ->orderBy('month')
            ->get();

        // Ambil data statistik
        $statistics = CompanyStatistic::with(['company', 'period'])
            ->when($companyId, function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get();

        // Data untuk chart
        $chartData = [
            'labels' => $periods->pluck('month'),
            'man_hours' => [],
            'employee' => [],
            'lta' => [],
            'sr' => [],
            'fr' => [],
            'ir' => []
        ];

        foreach ($periods as $period) {
            $stat = $statistics
                ->where('period_id', $period->id)
                ->first();

            $chartData['man_hours'][] = $stat->man_hours ?? 0;
            $chartData['employee'][]  = $stat->employee ?? 0;
            $chartData['lta'][]       = $stat->lta ?? 0;
            
            // Calculate rates if statistics exist
            if ($stat) {
                $chartData['sr'][] = $this->calculateSR($stat);
                $chartData['fr'][] = $this->calculateFR($stat);
                $chartData['ir'][] = $this->calculateIR($stat);
            } else {
                $chartData['sr'][] = 0;
                $chartData['fr'][] = 0;
                $chartData['ir'][] = 0;
            }
        }

        // Get gauge data for selected month
        $gaugeSR = $this->getGaugeData('severity_rate', $year, $companyId);
        $gaugeFR = $this->getGaugeData('frequency_rate', $year, $companyId);
        $gaugeIR = $this->getGaugeData('incident_rate', $year, $companyId);
        
        // Get summary data for selected month
        if ($gaugeMonth) {
            // kalau pilih bulan tertentu
            $monthlySummary = $this->getMonthlySummary($companyId, $year, $gaugeMonth);

            // gauge hanya 1 bulan
            $gaugeSR = [$gaugeMonth => $gaugeSR[$gaugeMonth] ?? 0];
            $gaugeFR = [$gaugeMonth => $gaugeFR[$gaugeMonth] ?? 0];
            $gaugeIR = [$gaugeMonth => $gaugeIR[$gaugeMonth] ?? 0];

        } else {
            // kalau All Month -> summary total 1 tahun
            $monthlySummary = [
                'man_hours' => $statistics->sum('man_hours'),
                'employee' => $statistics->sum('employee'),
                'lta' => $statistics->sum('lta'),
                'lost_work_days' => $statistics->sum('lost_work_days'),
                'lost_time' => $statistics->sum('lost_time'),
                'kecelakaan_kerja' => $statistics->sum('kecelakaan_kerja'),
            ];
        }

        // Month names for display
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];

        // Monthly FR data for chart
        $monthlyFR = [];
        foreach ($periods as $period) {
            $stat = $statistics->where('period_id', $period->id)->first();
            if ($stat) {
                $monthlyFR[$period->month] = $this->calculateFR($stat);
            }
        }

        $date = new \DateTime($year . '-' . str_pad($displayMonth, 2, '0', STR_PAD_LEFT) . '-01');

        return view('dashboard/index', compact(
            'companies',
            'chartData',
            'year',
            'companyId',
            'periods',
            'monthNames',
            'date',
            'gaugeMonth',
            'gaugeSR',
            'gaugeFR',
            'gaugeIR',
            'monthlySummary',
            'monthlyFR',
            'displayMonth'
        ));
    }

    private function getGaugeData($type, $year, $companyId)
    {
        $periods = Period::where('year', $year)->orderBy('month')->get();
        $statistics = CompanyStatistic::where('company_id', $companyId)
            ->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            })
            ->get();

        $data = [];
        // Loop melalui semua 12 bulan
        for ($month = 1; $month <= 12; $month++) {
            $period = $periods->where('month', $month)->first();
            $stat = $period ? $statistics->where('period_id', $period->id)->first() : null;
            
            $data[$month] = $stat ? match($type) {
                'severity_rate' => $this->calculateSR($stat),
                'frequency_rate' => $this->calculateFR($stat),
                'incident_rate' => $this->calculateIR($stat),
            } : 0;
        }
        return $data;
    }

    private function getMonthlySummary($companyId, $year, $month)
    {
        $period = Period::where('year', $year)->where('month', $month)->first();
        
        if (!$period) {
            return [
                'man_hours' => 0,
                'employee' => 0,
                'lta' => 0,
                'lost_work_days' => 0,
                'lost_time' => 0,
                'kecelakaan_kerja' => 0
            ];
        }

        $stat = CompanyStatistic::where('company_id', $companyId)
            ->where('period_id', $period->id)
            ->first();

        return $stat ? $stat->toArray() : [
            'man_hours' => 0,
            'employee' => 0,
            'lta' => 0,
            'lost_work_days' => 0,
            'lost_time' => 0,
            'kecelakaan_kerja' => 0
        ];
    }

    private function calculateSR($stat)
    {
        return $stat->man_hours > 0 
            ? round(($stat->lost_time * 1000000) / $stat->man_hours, 2)
            : 0;
    }

    private function calculateFR($stat)
    {
        return $stat->man_hours > 0 
            ? round(($stat->lost_work_days * 1000000) / $stat->man_hours, 2)
            : 0;
    }

    private function calculateIR($stat)
    {
        return $stat->employee > 0 
            ? round(($stat->kecelakaan_kerja * 100) / $stat->employee, 2)
            : 0;
    }

    public function store(Request $request)
    {
        // 1. VALIDASI
        $request->validate([
            'company_id'      => 'required|exists:companies,id',
            'month'           => 'required|integer|min:1|max:12',
            'year'            => 'required|integer|min:2000',
            'man_hours'       => 'required|integer|min:0',
            'employee'  => 'required|integer|min:0',
            'lta'       => 'required|integer|min:0',
            'lost_work_days'       => 'required|integer|min:0',
            'lost_time'       => 'required|integer|min:0',
            'kecelakaan_kerja'       => 'required|integer|min:0',
        ]);

        // 2. CARI / BUAT PERIODE OTOMATIS JIKA BELUM ADA
        $period = \App\Models\Period::firstOrCreate(
            [
                'month' => $request->month,
                'year'  => $request->year
            ]
        );

        // 3. SIMPAN / UPDATE STATISTIK
        \App\Models\CompanyStatistic::updateOrCreate(
            [
                'company_id' => $request->company_id,
                'period_id'  => $period->id
            ],
            [
                'man_hours'      => $request->man_hours,
                'employee' => $request->employee,
                'lta'      => $request->lta,
                'lost_work_days'      => $request->lost_work_days,
                'lost_time'      => $request->lost_time,
                'kecelakaan_kerja'      => $request->kecelakaan_kerja,
            ]
        );

        return redirect()
            ->route('dashboard', ['company_id' => $request->company_id])
            ->with('success', 'Data berhasil disimpan');
    }

}
