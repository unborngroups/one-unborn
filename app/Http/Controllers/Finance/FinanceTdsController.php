<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceTdsSetting;
use Illuminate\Http\Request;

class FinanceTdsController extends Controller
{
    public function index()
    {
        $tds = FinanceTdsSetting::first();

        if (!$tds) {
            $tds = FinanceTdsSetting::create([]);
        }

        return view('finance.settings.tds', compact('tds'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'section'          => 'nullable|string|max:20',
            'tds_rate'         => 'required|numeric|min:0',
            'threshold_amount' => 'required|numeric|min:0',
        ]);

        $tds = FinanceTdsSetting::first();

        $tds->update([
            'tds_enabled'      => $request->has('tds_enabled'),
            'section'          => $request->section,
            'tds_rate'         => $request->tds_rate,
            'threshold_amount' => $request->threshold_amount,
            'deduction_on'     => $request->deduction_on,
        ]);

        return redirect()->back()->with('success', 'TDS Settings Updated Successfully');
    }
}
