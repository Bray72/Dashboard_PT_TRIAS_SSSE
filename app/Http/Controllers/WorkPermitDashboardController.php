<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermitStatistic;
use App\Models\Period;
use Illuminate\Support\Facades\DB;
use App\Models\PermitType;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Company;

class WorkPermitDashboardController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::orderBy('name')->get();
        $month = (int) ($request->month ?? 1);
        $year  = (int) ($request->year ?? 2026);
        $companyId = $request->company_id;
        $permitTypes = PermitType::orderBy('name')->get();

        // Nov'25 (bulan spesifik)
        $monthly = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.month', $month)
            ->where('periods.year', $year)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD tahun berjalan
        $ytdCurrent = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year)
            ->whereBetween('periods.month', [1, $month])
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD tahun lalu
        $ytdPrevious = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year - 1)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        $monthlyTrends = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
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
            'chartData',
            'companies'
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

    public function export(Request $request)
    {
        $month = (int) ($request->month ?? 1);
        $year = (int) ($request->year ?? 2026);
        $companyId = $request->company_id;

        $permitTypes = PermitType::orderBy('name')->get();

        // Monthly data
        $monthly = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.month', $month)
            ->where('periods.year', $year)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD current year
        $ytdCurrent = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year)
            ->whereBetween('periods.month', [1, $month])
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD previous year
        $ytdPrevious = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year - 1)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // Prepare export data
        $exportData = [];
        $exportData[] = ['Work Permit Report', 'Month: ' . $month, 'Year: ' . $year];
        $exportData[] = [];
        $exportData[] = [
            'Safety Work Permit Type',
            'Monthly (' . date('M', mktime(0, 0, 0, $month, 1)) . ')',
            'YTD ' . $year,
            'YTD ' . ($year - 1)
        ];

        foreach ($permitTypes as $type) {
            $exportData[] = [
                $type->name,
                $monthly[$type->id]->total ?? 0,
                $ytdCurrent[$type->id]->total ?? 0,
                $ytdPrevious[$type->id]->total ?? 0,
            ];
        }

        // Add totals
        $totalMonthly = $monthly->sum('total') ?? 0;
        $totalYtdCurrent = $ytdCurrent->sum('total') ?? 0;
        $totalYtdPrevious = $ytdPrevious->sum('total') ?? 0;

        $exportData[] = [];
        $exportData[] = ['TOTAL', $totalMonthly, $totalYtdCurrent, $totalYtdPrevious];

        $filename = 'work_permit_report_' . $month . '_' . $year . '_' . date('Ymd_His');

        return $this->exportToCSV($exportData, $filename);
    }

    public function exportPDF(Request $request)
    {
        $month = (int) ($request->month ?? 1);
        $year = (int) ($request->year ?? 2026);
        $companyId = $request->company_id;

        $permitTypes = PermitType::orderBy('name')->get();

        // Monthly data
        $monthly = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.month', $month)
            ->where('periods.year', $year)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD current year
        $ytdCurrent = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year)
            ->whereBetween('periods.month', [1, $month])
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // YTD previous year
        $ytdPrevious = PermitStatistic::join('periods', 'periods.id', '=', 'permit_statistics.period_id')
            ->where('periods.year', $year - 1)
            ->when($companyId, function($q) use ($companyId) {
                return $q->where('permit_statistics.company_id', $companyId);
            })
            ->select('permit_statistics.permit_type_id', DB::raw('SUM(total) as total'))
            ->groupBy('permit_statistics.permit_type_id')
            ->get()
            ->keyBy('permit_type_id');

        // Prepare table data
        $tableData = [];
        foreach ($permitTypes as $type) {
            $tableData[] = [
                'name' => $type->name,
                'monthly' => $monthly[$type->id]->total ?? 0,
                'ytd_current' => $ytdCurrent[$type->id]->total ?? 0,
                'ytd_previous' => $ytdPrevious[$type->id]->total ?? 0,
            ];
        }

        // Calculate totals
        $totalMonthly = array_sum(array_column($tableData, 'monthly'));
        $totalYtdCurrent = array_sum(array_column($tableData, 'ytd_current'));
        $totalYtdPrevious = array_sum(array_column($tableData, 'ytd_previous'));

        $data = [
            'month' => $month,
            'year' => $year,
            'monthName' => date('F', mktime(0, 0, 0, $month, 1)),
            'tableData' => $tableData,
            'totalMonthly' => $totalMonthly,
            'totalYtdCurrent' => $totalYtdCurrent,
            'totalYtdPrevious' => $totalYtdPrevious,
            'generatedDate' => date('d-m-Y H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.work-permit', $data);
        $pdf->setPaper('a4', 'landscape');
        
        // Enable external images
        $pdf->getOptions()->setIsRemoteEnabled(true);

        $filename = 'work_permit_report_' . $month . '_' . $year . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    private function exportToCSV($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename.csv\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ["\xEF\xBB\xBF"]);

            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
