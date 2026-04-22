<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\BankTransaction;

class ReportController extends Controller
{
     public function sales()
    {
        $sales = Invoice::where('type','sales')->get();
        return view('finance.reports.sales', compact('sales'));
    }

    public function purchase()
    {
        $purchases = Invoice::where('type','purchase')->get();
        return view('finance.reports.purchase', compact('purchases'));
    }

    public function gst()
    {
        $invoices = Invoice::all();
        return view('finance.reports.gst', compact('invoices'));
    }
    
}
