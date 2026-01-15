<?php

namespace App\Http\Controllers;
use App\Models\NearMiss;
use App\Models\NearMissStatistic;
use App\Models\Period;
use App\Models\Department;
use App\Models\CompanyStatistic;
use Illuminate\Http\Request;

class NearMissController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->year ?? now()->year;
        $month = $request->month;

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
            'departments'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'           => 'required|date',
            'department_id'  => 'required|exists:departments,id',
            'location'       => 'required',
            'category'       => 'required',
            'severity'       => 'required',
            'likelihood'     => 'required',
            'description'    => 'required',
        ]);

        // auto period
        $date  = \Carbon\Carbon::parse($request->date);
        $period = Period::where('month', $date->month)
            ->where('year', $date->year)
            ->firstOrFail();

        // auto risk mapping
        $riskMatrix = [
            'Low' => ['Low' => 'Low', 'Medium' => 'Low', 'High' => 'Medium'],
            'Medium' => ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'],
            'High' => ['Low' => 'Medium', 'Medium' => 'High', 'High' => 'High'],
        ];

        $riskLevel = $riskMatrix[$request->severity][$request->likelihood];

        NearMiss::create([
            'company_id'    => auth()->user()->company_id ?? 1,
            'period_id'     => $period->id,
            'department_id' => $request->department_id,
            'date'          => $request->date,
            'location'      => $request->location,
            'category'      => $request->category,
            'severity'      => $request->severity,
            'likelihood'    => $request->likelihood,
            'risk_level'    => $riskLevel,
            'description'   => $request->description,
            'action_required' => $request->action_required,
            'status'        => 'Open',
        ]);

        return redirect()->route('near-miss.dashboard')
            ->with('success', 'Near Miss berhasil ditambahkan');
    }

    private function getMonthName($month)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return $months[$month - 1] ?? $month;
    }
}