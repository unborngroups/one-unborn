<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceGstSetting;
use Illuminate\Http\Request;

class FinanceGstController extends Controller
{
    public function index()
    {
        $gst = FinanceGstSetting::first();

        if (!$gst) {
            $gst = FinanceGstSetting::create([]);
        }

        return view('finance.settings.gst', compact('gst'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gst_number' => 'nullable|string|max:20',
            'state_code' => 'nullable|string|max:10',
            'cgst_rate'  => 'required|numeric|min:0',
            'sgst_rate'  => 'required|numeric|min:0',
            'igst_rate'  => 'required|numeric|min:0',
        ]);

        $gst = FinanceGstSetting::first();

        $gst->update([
            'gst_enabled'       => $request->has('gst_enabled'),
            'gst_number'        => $request->gst_number,
            'state_code'        => $request->state_code,
            'cgst_rate'         => $request->cgst_rate,
            'sgst_rate'         => $request->sgst_rate,
            'igst_rate'         => $request->igst_rate,
            'calculation_type'  => $request->calculation_type,
        ]);

        return redirect()->back()->with('success', 'GST Settings Updated Successfully');
    }
}
