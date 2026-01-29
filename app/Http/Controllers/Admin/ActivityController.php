<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NearMiss;
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
            ->take(5)
            ->get()
            ->each(function ($user) use ($activities) {
                $activities->push([
                    'message' => "{$user->name} register",
                    'time' => $user->created_at,
                ]);
            });

        /**
         * 2. USER INPUT DATA (contoh: Near Miss)
         */
        NearMiss::latest('created_at')
            ->take(5)
            ->get()
            ->each(function ($item) use ($activities) {
                $activities->push([
                    'message' => "Input data Near Miss",
                    'time' => $item->created_at,
                ]);
            });

        /**
         * SORT + LIMIT FINAL
         */
        $activities = $activities
            ->sortByDesc('time')
            ->take(10);

        return view('dashboard.activity', compact('activities'));
    }
}
