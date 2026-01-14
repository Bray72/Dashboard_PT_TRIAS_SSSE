<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NearMissStatistic;
use App\Models\NearMiss;
use App\Models\Department;
use App\Models\Period;

class NearMissController extends Controller
{
    public function index(Request $request)
    {
        $companyId = 1;

        $periods = Period::orderBy('year','desc')
            ->orderBy('month','desc')->get();

        $departments = Department::orderBy('name')->get();

        $periodId = $request->get('period_id') ?? $periods->first()->id;

        $stat = NearMissStatistic::where('company_id',$companyId)
            ->where('period_id',$periodId)
            ->first();

        return view('near-miss.dashboard', compact(
            'periods','periodId','stat','departments'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'department_id' => 'required',
            'severity' => 'required',
            'likelihood' => 'required',
            'risk_level' => 'required'
        ]);

        $companyId = 1;
        $periodId  = $request->period_id;

        NearMiss::create([
            'company_id'    => $companyId,
            'period_id'     => $periodId,
            'department_id' => $request->department_id,
            'date'          => $request->date,
            'severity'      => $request->severity,
            'likelihood'    => $request->likelihood,
            'risk_level'    => $request->risk_level,
            'status'        => 'open'
        ]);

        // 🔥 auto agregasi (WAJIB)
        $this->generateStatistic($companyId, $periodId);

        return back()->with('success','Near miss berhasil disimpan & statistik diperbarui');
    }
}
