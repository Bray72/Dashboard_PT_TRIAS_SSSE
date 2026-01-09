<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Period;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index(Request $request)
    {
        $companyId = $request->company_id;
        $year = $request->year
        ?? Period::orderBy('year', 'desc')->value('year')
        ?? date('Y');


        // List perusahaan (untuk tab / dropdown)
        $companies = Company::all();

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
            'lta' => []
        ];

        foreach ($periods as $period) {
            $stat = $statistics
                ->where('period_id', $period->id)
                ->first();

            $chartData['man_hours'][] = $stat->man_hours ?? 0;
            $chartData['employee'][]  = $stat->employee ?? 0;
            $chartData['lta'][]       = $stat->lta ?? 0;
        }

        return view('dashboard', compact(
            'companies',
            'chartData',
            'year',
            'companyId'
        ));
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

        // 2. CARI / BUAT PERIODE
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
            ->route('dashboard')
            ->with('success', 'Data berhasil disimpan');
    }

}
