<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\VendorInvoice;
use App\Models\Expense;
use App\Models\BankTransaction;

class ReportController extends Controller
{
    public function index()
    {
        return view('finance.reports.index');
    }

    // Profit & Loss
    public function profitLoss()
    {
        $totalIncome = 0; // later sales module hook
        $totalExpense = Expense::sum('amount');

        $profit = $totalIncome - $totalExpense;

        return view('finance.reports.profit_loss', compact(
            'totalIncome',
            'totalExpense',
            'profit'
        ));
    }

    // Balance Sheet
    public function balanceSheet()
    {
        $assets = BankTransaction::where('type','credit')->sum('amount');
        $liabilities = VendorInvoice::sum('total_amount');

        $equity = $assets - $liabilities;

        return view('finance.reports.balance_sheet', compact(
            'assets',
            'liabilities',
            'equity'
        ));
    }

    // Cash Flow
    public function cashFlow()
    {
        $cashIn = BankTransaction::where('type','credit')->sum('amount');
        $cashOut = BankTransaction::where('type','debit')->sum('amount');

        $netCash = $cashIn - $cashOut;

        return view('finance.reports.cash_flow', compact(
            'cashIn',
            'cashOut',
            'netCash'
        ));
    }
}
