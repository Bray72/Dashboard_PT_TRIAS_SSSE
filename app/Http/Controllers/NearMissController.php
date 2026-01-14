<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NearMissStatistic;
use App\Models\NearMiss;
use App\Models\Period;

class NearMissController extends Controller
{
    public function index(Request $request)
    {
        $companyId = 1;

        $periods = Period::orderBy('year','desc')
            ->orderBy('month','desc')
            ->get();

        $periodId = $request->get('period_id') ?? $periods->first()->id;

        $stat = NearMissStatistic::where('company_id',$companyId)
            ->where('period_id',$periodId)
            ->first();

        return view('near_miss.index', compact(
            'periods','periodId','stat'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'severity' => 'required',
            'likelihood' => 'required',
            'risk_level' => 'required'
        ]);

        NearMiss::create([
            'company_id' => 1,
            'period_id' => $request->period_id,
            'date' => $request->date,
            'severity' => $request->severity,
            'likelihood' => $request->likelihood,
            'risk_level' => $request->risk_level,
            'status' => 'open'
        ]);

        return redirect()->back()->with('success','Near miss berhasil ditambahkan');
    }
}
