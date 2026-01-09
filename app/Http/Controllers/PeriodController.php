<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'year'  => 'required|integer|min:2000',
            'month' => 'nullable|string|max:20',
        ]);

        Period::create([
            'year'  => $request->year,
            'month' => $request->month
        ]);

        return redirect()
            ->route('dashboard.safety')
            ->with('success', 'Period berhasil ditambahkan');
    }
}
