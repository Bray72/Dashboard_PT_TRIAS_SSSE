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

        return view('near_miss.index', compact(
            'periods','periodId','stat','departments'
        ));
    }

    /* SIMPAN DEPARTMENT */
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'department_name' => 'required|unique:departments,name'
        ]);

        Department::create([
            'name' => $request->department_name
        ]);

        return back()->with('success_department','Department berhasil ditambahkan');
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

    private function generateStatistic($companyId, $periodId)
    {
        DB::statement("
            INSERT INTO near_miss_statistics (
                company_id, period_id,
                total_near_miss,
                high_risk, medium_risk, low_risk,
                high_severity, medium_severity, low_severity,
                high_likelihood, medium_likelihood, low_likelihood,
                open, closed,
                near_miss_rate,
                created_at, updated_at
            )
            SELECT
                nm.company_id,
                nm.period_id,
                COUNT(*) AS total_near_miss,

                COUNT(*) FILTER (WHERE nm.risk_level='high'),
                COUNT(*) FILTER (WHERE nm.risk_level='medium'),
                COUNT(*) FILTER (WHERE nm.risk_level='low'),

                COUNT(*) FILTER (WHERE nm.severity='high'),
                COUNT(*) FILTER (WHERE nm.severity='medium'),
                COUNT(*) FILTER (WHERE nm.severity='low'),

                COUNT(*) FILTER (WHERE nm.likelihood='high'),
                COUNT(*) FILTER (WHERE nm.likelihood='medium'),
                COUNT(*) FILTER (WHERE nm.likelihood='low'),

                COUNT(*) FILTER (WHERE nm.status='open'),
                COUNT(*) FILTER (WHERE nm.status='closed'),

                CASE
                    WHEN cs.man_hours > 0
                    THEN ROUND(COUNT(*)::decimal / cs.man_hours, 6)
                    ELSE 0
                END,

                NOW(), NOW()

            FROM near_misses nm
            JOIN company_statistics cs
            ON cs.company_id = nm.company_id
            AND cs.period_id  = nm.period_id
            WHERE nm.company_id = {$companyId}
            AND nm.period_id  = {$periodId}
            GROUP BY nm.company_id, nm.period_id, cs.man_hours

            ON CONFLICT (company_id, period_id)
            DO UPDATE SET
                total_near_miss = EXCLUDED.total_near_miss,
                high_risk = EXCLUDED.high_risk,
                medium_risk = EXCLUDED.medium_risk,
                low_risk = EXCLUDED.low_risk,
                high_severity = EXCLUDED.high_severity,
                medium_severity = EXCLUDED.medium_severity,
                low_severity = EXCLUDED.low_severity,
                high_likelihood = EXCLUDED.high_likelihood,
                medium_likelihood = EXCLUDED.medium_likelihood,
                low_likelihood = EXCLUDED.low_likelihood,
                open = EXCLUDED.open,
                closed = EXCLUDED.closed,
                near_miss_rate = EXCLUDED.near_miss_rate,
                updated_at = NOW();
        ");
    }
}
