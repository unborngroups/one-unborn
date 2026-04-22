<?php

namespace App\Http\Controllers;
use App\Models\TaxInvoiceSetting;

use Illuminate\Http\Request;

class TaxInvoiceSettingsController extends Controller
{
    public function index()
    {
        $setting = TaxInvoiceSetting::first();
        return view('settings.tax_invoice', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'invoice_prefix' => 'required|string|max:10',
            'invoice_start_no' => 'required|integer|min:1',
            'currency_symbol' => 'required|string|max:5',
            'currency_code' => 'required|string|max:10',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'billing_terms' => 'nullable|string|max:100',
        ]);

        TaxInvoiceSetting::updateOrCreate(['id' => 1], $data);

        return back()->with('success', 'Tax & Invoice settings updated successfully!');
    }

}
