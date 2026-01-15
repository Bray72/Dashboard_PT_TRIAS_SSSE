<?php

namespace App\Http\Controllers;
use App\Models\NearMiss;
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

        $totalNearMiss = NearMiss::whereIn('period_id', $periodIds)->count();

        $manHours = CompanyStatistic::whereIn('period_id', $periodIds)
            ->sum('man_hours');

        $nearMissRate = $manHours > 0 ? $totalNearMiss / $manHours : 0;

        $risk = NearMiss::selectRaw('risk_level, COUNT(*) as total')
            ->whereIn('period_id', $periodIds)
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        $departments = Department::orderBy('name')->get();

        return view('near_miss.index', compact(
            'year',
            'month',
            'totalNearMiss',
            'nearMissRate',
            'risk',
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
}
