<?php

namespace App\Http\Controllers;
use App\Models\NearMiss;
use App\Models\NearMissStatistic;
use App\Models\Period;
use App\Models\Company;
use App\Models\Department;
use App\Models\CompanyStatistic;
use Illuminate\Http\Request;

class NearMissController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->year ?? now()->year;
        $month = $request->month;
         $companies = Company::all();
        $periodQuery = Period::where('year', $year);
        if ($month) {
            $periodQuery->where('month', $month);
        }

        $periodIds = $periodQuery->pluck('id');

        // Total near miss count
        $totalNearMiss = NearMiss::whereIn('period_id', $periodIds)->count();

        // Get man hours for near miss rate calculation
        $manHours = CompanyStatistic::whereIn('period_id', $periodIds)
            ->sum('man_hours');

        // Calculate near miss rate: total near miss / man hours
        $nearMissRate = $manHours > 0 ? $totalNearMiss / $manHours : 0;

        // Risk level distribution
        $risk = NearMiss::whereIn('period_id', $periodIds)
            ->groupBy('risk_level')
            ->selectRaw('risk_level, COUNT(*) as total')
            ->pluck('total', 'risk_level');

        // Severity distribution
        $severity = NearMiss::whereIn('period_id', $periodIds)
            ->groupBy('severity')
            ->selectRaw('severity, COUNT(*) as total')
            ->pluck('total', 'severity');

        // Likelihood distribution
        $likelihood = NearMiss::whereIn('period_id', $periodIds)
            ->groupBy('likelihood')
            ->selectRaw('likelihood, COUNT(*) as total')
            ->pluck('total', 'likelihood');

        // Category distribution
        $category = NearMiss::whereIn('period_id', $periodIds)
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as total')
            ->pluck('total', 'category');

        // Department distribution
        $departmentStats = NearMiss::whereIn('period_id', $periodIds)
            ->groupBy('department_id')
            ->selectRaw('department_id, COUNT(*) as total')
            ->with('department')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->department->name ?? 'Unknown',
                    'total' => $item->total
                ];
            });

        // Status distribution
        $status = NearMiss::whereIn('period_id', $periodIds)
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as total')
            ->pluck('total', 'status');

        // Monthly trend
        $monthlyTrend = Period::whereIn('id', $periodIds)
            ->selectRaw('month, EXTRACT(MONTH FROM created_at) as month_num')
            ->orderBy('month')
            ->get()
            ->map(function($period) {
                $count = NearMiss::whereIn('period_id', [$period->id])->count();
                return [
                    'month' => $this->getMonthName($period->month),
                    'count' => $count
                ];
            });

        $nearMisses = NearMiss::whereIn('period_id', $periodIds)
            ->with(['department', 'period'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        $departments = Department::orderBy('name')->get();

        return view('near_miss.index', compact(
            'year',
            'month',
            'totalNearMiss',
            'nearMissRate',
            'manHours',
            'risk',
            'severity',
            'likelihood',
            'category',
            'departmentStats',
            'status',
            'monthlyTrend',
            'departments',
            'nearMisses',
            'companies'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'           => 'required|date',
            'department_id'  => 'required|integer|exists:departments,id',
            'location'       => 'required|string',
            'category'       => 'required|string',
            'severity'       => 'required|in:Low,Medium,High',
            'likelihood'     => 'required|in:Low,Medium,High',
            'description'    => 'required|string',
            'action_required'=> 'nullable|string',
        ]);

        // auto period (buat period kalau belum ada)
        $date = \Carbon\Carbon::parse($validated['date']);

        $period = Period::firstOrCreate(
            ['month' => $date->month, 'year' => $date->year],
            ['name' => $date->format('F Y')] // opsional kalau tabel period ada kolom name
        );

        // auto risk mapping
        $riskMatrix = [
            'Low' => ['Low' => 'Low', 'Medium' => 'Low', 'High' => 'Medium'],
            'Medium' => ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'],
            'High' => ['Low' => 'Medium', 'Medium' => 'High', 'High' => 'High'],
        ];

        $riskLevel = $riskMatrix[$validated['severity']][$validated['likelihood']] ?? 'Low';

        NearMiss::create([
            'company_id'      => auth()->user()->company_id ?? 1,
            'period_id'       => $period->id,
            'department_id'   => $validated['department_id'], // ✅ fix utama
            'date'            => $validated['date'],
            'location'        => $validated['location'],
            'category'        => $validated['category'],
            'severity'        => $validated['severity'],
            'likelihood'      => $validated['likelihood'],
            'risk_level'      => $riskLevel,
            'description'     => $validated['description'],
            'action_required' => $validated['action_required'] ?? null,
            'status'          => 'Open',
        ]);

        return redirect()->route('near-miss.dashboard')
            ->with('success', 'Near Miss berhasil ditambahkan');
    }
}
