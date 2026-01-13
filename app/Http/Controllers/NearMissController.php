<?php

namespace App\Http\Controllers;

use App\Models\NearMiss;
use App\Models\Company;
use App\Models\Period;
use Illuminate\Http\Request;

class NearMissController extends Controller
{
    /* ================= DASHBOARD ================= */
    public function index(Request $request)
    {
        $companyId = $request->company_id;
        $year      = $request->year ?? date('Y');

        $nearMisses = NearMiss::with(['company', 'period'])
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereHas('period', fn ($q) => $q->where('year', $year))
            ->get();

        return view('near_miss.index', [
            'nearMisses' => $nearMisses,
            'companies'  => Company::all(),
            'year'       => $year,

            /* SUMMARY */
            'total'      => $nearMisses->count(),
            'highRisk'   => $nearMisses->where('risk_level', 'High')->count(),
            'open'       => $nearMisses->where('status', 'Open')->count(),
            'closed'     => $nearMisses->where('status', 'Closed')->count(),
        ]);
    }

    /* ================= FORM ================= */
    public function create()
    {
        return view('near_miss.create', [
            'companies' => Company::all(),
            'periods'   => Period::orderBy('year', 'desc')->get()
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'period_id'  => 'required',
            'date'       => 'required|date',
            'risk_level' => 'required',
            'status'     => 'required'
        ]);

        NearMiss::create($request->all());

        return redirect()
            ->route('near-miss.index')
            ->with('success', 'Near Miss berhasil disimpan');
    }
}
