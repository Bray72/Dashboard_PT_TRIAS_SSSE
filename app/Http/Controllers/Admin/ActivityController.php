<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NearMiss;
use App\Models\CompanyStatistic;
use App\Models\PermitStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = collect();

        /**
         * 1. USER REGISTER
         */
        User::latest('created_at')
            ->take(10)
            ->get()
            ->each(function ($user) use ($activities) {
                $activities->push([
                    'message' => "{$user->name} register",
                    'time' => $user->created_at,
                    'type' => 'register',
                ]);
            });

        /**
         * 2. SAFETY METRIC INPUT
         */
        CompanyStatistic::latest('created_at')
            ->take(10)
            ->get()
            ->each(function ($item) use ($activities) {
                $activities->push([
                    'message' => "Input data Safety Metric",
                    'time' => $item->created_at,
                    'type' => 'safety_metric',
                ]);
            });

        /**
         * 3. WORK PERMIT INPUT
         */
        PermitStatistic::latest('created_at')
            ->take(10)
            ->get()
            ->each(function ($item) use ($activities) {
                $activities->push([
                    'message' => "Input data Work Permit",
                    'time' => $item->created_at,
                    'type' => 'work_permit',
                ]);
            });

        /**
         * 4. NEAR MISS INPUT
         */
        NearMiss::latest('created_at')
            ->take(10)
            ->get()
            ->each(function ($item) use ($activities) {
                $activities->push([
                    'message' => "Input data Near Miss",
                    'time' => $item->created_at,
                    'type' => 'near_miss',
                ]);
            });

        /**
         * SORT + LIMIT FINAL
         */
        $activities = $activities
            ->sortByDesc('time')
            ->take(15);

        return view('dashboard.activity', compact('activities'));
    }
}
