<?php

namespace App\Http\Controllers;
use App\Models\NearMiss;
use App\Models\NearMissStatistic;
use App\Models\Period;
use App\Models\Company;
use App\Models\Department;
use App\Models\CompanyStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NearMissController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->year ?? now()->year;
        $month = $request->month;
        $companyId = $request->company_id;
        $companies = Company::all();
        $periodQuery = Period::where('year', $year);
        if ($month) {
            $periodQuery->where('month', $month);
        }

        $periodIds = $periodQuery->pluck('id');

        // Build base query with optional company filter
        $baseQuery = NearMiss::whereIn('period_id', $periodIds);
        if ($companyId) {
            $baseQuery->where('company_id', $companyId);
        }

        // Total near miss count
        $totalNearMiss = $baseQuery->clone()->count();

        // Get man hours for near miss rate calculation
        $manHoursQuery = CompanyStatistic::whereIn('period_id', $periodIds);
        if ($companyId) {
            $manHoursQuery->where('company_id', $companyId);
        }
        $manHours = $manHoursQuery->sum('man_hours');

        // Calculate near miss rate: total near miss / man hours
        $nearMissRate = $manHours > 0 ? $totalNearMiss / $manHours * 100000 : 0;

        // Risk level distribution
        $risk = $baseQuery->clone()
            ->groupBy('risk_level')
            ->selectRaw('risk_level, COUNT(*) as total')
            ->pluck('total', 'risk_level');

        $nearMissPerCompanyQuery = DB::table('near_misses')
            ->join('companies', 'near_misses.company_id', '=', 'companies.id')
            ->whereIn('near_misses.period_id', $periodIds);
        if ($companyId) {
            $nearMissPerCompanyQuery->where('near_misses.company_id', $companyId);
        }
        $nearMissPerCompany = $nearMissPerCompanyQuery
            ->select('companies.name as company_name', DB::raw('COUNT(near_misses.id) as total'))
            ->groupBy('companies.name')
            ->orderBy('companies.name')
            ->get();
        
        // Severity distribution
        $severity = $baseQuery->clone()
            ->groupBy('severity')
            ->selectRaw('severity, COUNT(*) as total')
            ->pluck('total', 'severity');

        // Likelihood distribution
        $likelihood = $baseQuery->clone()
            ->groupBy('likelihood')
            ->selectRaw('likelihood, COUNT(*) as total')
            ->pluck('total', 'likelihood');

        // Category distribution
        $category = $baseQuery->clone()
            ->groupBy('category')
            ->selectRaw('category, COUNT(*) as total')
            ->pluck('total', 'category');

        // Department distribution
        $departmentStats = $baseQuery->clone()
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
        $status = $baseQuery->clone()
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as total')
            ->pluck('total', 'status');

        // Monthly trend
        $monthlyTrend = Period::whereIn('id', $periodIds)
            ->selectRaw('month, EXTRACT(MONTH FROM created_at) as month_num')
            ->orderBy('month')
            ->get()
            ->map(function($period) {
                $countQuery = NearMiss::whereIn('period_id', [$period->id]);
                if ($companyId) {
                    $countQuery->where('company_id', $companyId);
                }
                $count = $countQuery->count();
                return [
                    'month' => $this->getMonthName($period->month),
                    'count' => $count
                ];
            });

        $nearMissesQuery = NearMiss::whereIn('period_id', $periodIds);
        if ($companyId) {
            $nearMissesQuery->where('company_id', $companyId);
        }
        $nearMisses = $nearMissesQuery
            ->with(['department', 'period'])
            ->orderBy('date', 'desc')
            ->paginate(10);

        $departments = Department::orderBy('name')->get();

        return view('near_miss.index', compact(
            'year',
            'month',
            'companyId',
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
            'companies',
            'nearMissPerCompany'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'     => 'required|integer|exists:companies,id',
            'date'           => 'required|date',
            'department_id'  => 'required|integer|exists:departments,id',
            'location'       => 'required|string',
            'category'       => 'required|string',
            'severity'       => 'required|in:Low,Medium,High',
            'likelihood'     => 'required|in:Low,Medium,High',
            'description'    => 'required|string',
            'action_required'=> 'nullable|string',
        ]);

        // period otomatis
        $date = \Carbon\Carbon::parse($validated['date']);

        $period = Period::firstOrCreate([
            'month' => $date->month,
            'year'  => $date->year
        ]);

        // risk mapping
        $riskMatrix = [
            'Low' => ['Low' => 'Low', 'Medium' => 'Low', 'High' => 'Medium'],
            'Medium' => ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'],
            'High' => ['Low' => 'Medium', 'Medium' => 'High', 'High' => 'High'],
        ];

        $riskLevel = $riskMatrix[$validated['severity']][$validated['likelihood']] ?? 'Low';

        NearMiss::create([
            'company_id'      => $validated['company_id'],     // ✅ FIX UTAMA
            'period_id'       => $period->id,
            'department_id'   => $validated['department_id'],
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

        return redirect()->back()->with('success', 'Near Miss berhasil ditambahkan!');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Open,Closed'
        ]);

        $nearMiss = NearMiss::findOrFail($id);
        $nearMiss->update([
            'status' => $request->status
        ]);

        return redirect()->route('near-miss.dashboard')
            ->with('success', 'Status Near Miss berhasil diperbarui');
    }

    private function getMonthName($month)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return $months[$month - 1] ?? $month;
    }
}
