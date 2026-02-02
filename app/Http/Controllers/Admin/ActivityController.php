<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NearMiss;
use App\Models\CompanyStatistic;
use App\Models\PermitStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function index()
    {
        // 1️⃣ Near Miss activity
        $nearMissActivities = DB::table('')
            ->select(
                DB::raw("'Input data Near Miss' as activity"),
                'created_at'
            )
            ->latest()
            ->limit(10);

        // 2️⃣ Safety Metric activity (contoh)
        $safetyMetricActivities = DB::table('company_statistics')
            ->select(
                DB::raw("'Input data Safety Metric' as activity"),
                'created_at'
            )
            ->latest()
            ->limit(10);

        // 3️⃣ Gabungkan semua activity
        $activities = $nearMissActivities
            ->unionAll($safetyMetricActivities)
            ->orderBy('created_at', 'desc')
            ->get();

        // 4️⃣ Kirim ke view
        return view('dashboard.activity', [
            'activities' => $activities
        ]);
    }
}
