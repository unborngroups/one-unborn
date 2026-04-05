<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecurringInvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Placeholder: Fetch recurring invoices (implement logic as needed)
        $recurringInvoices = [];
        return view('finance.sales.recurring_invoice.index', compact('recurringInvoices'));
    }
}
