<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermitStatistic;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use App\Models\PermitType;

class WorkPermitDashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) ($request->month ?? 1);
        $year  = (int) ($request->year ?? 2026);

        $permitTypes = PermitType::orderBy('name')->get();

        // Nov'25 (bulan spesifik)
        $monthly = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.month', $month)
            ->where('periods.year', $year)
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD tahun berjalan
        $ytdCurrent = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year)
            ->whereBetween('periods.month', [1, $month])
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD tahun lalu
        $ytdPrevious = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year - 1)
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        $monthlyTrends = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year)
            ->select('permit_statistics.permit_type_id', 'periods.month', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id', 'periods.month')
            ->orderBy('periods.month')
            ->get();

        // Transform data for chart
        $chartData = [];
        foreach ($permitTypes as $type) {
            $chartData[$type->id] = array_fill(1, 12, 0);
        }
        
        foreach ($monthlyTrends as $trend) {
            $chartData[$trend->permit_type_id][$trend->month] = $trend->total;
        }

        return view('dashboard.Work-Permit', compact(
            'permitTypes',
            'monthly',
            'ytdCurrent',
            'ytdPrevious',
            'month',
            'year',
            'chartData'
        ));
    }

    public function store(Request $request)
    {
        // =========================
        // VALIDASI
        // =========================
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'month'      => 'required|integer|min:1|max:12',
            'year'       => 'required|integer|min:2000',
            'permits'    => 'required|array',
        ]);

        DB::transaction(function () use ($request) {

            // =========================
            // AMBIL / BUAT PERIOD
            // =========================
            $period = Period::firstOrCreate([
                'month' => $request->month,
                'year'  => $request->year,
            ]);

            // =========================
            // SIMPAN PER JENIS PERMIT
            // =========================
            foreach ($request->permits as $permitTypeId => $total) {

                PermitStatistic::updateOrCreate(
                    [
                        'company_id'     => $request->company_id,
                        'period_id'      => $period->id,
                        'permit_type_id'=> $permitTypeId,
                    ],
                    [
                        'total' => (int) $total,
                    ]
                );
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Data Work Permit berhasil disimpan');
    }
}
