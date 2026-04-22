<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\BankAccount;
use App\Models\BankTransaction;

class BankingController extends Controller
{
    public function index()
    {
        $banks = BankAccount::latest()->get();
        return view('finance.banking.index', compact('banks'));
    }

    public function create()
    {
        return view('finance.banking.create');
    }

    public function store(Request $request)
    {
        BankAccount::create($request->all());
        return redirect()->route('finance.banking.index')
            ->with('success','Bank added');
    }

    public function transactions($id)
    {
        $bank = BankAccount::findOrFail($id);
        $transactions = $bank->transactions;
        return view('finance.banking.transactions',compact('bank','transactions'));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|string',
            'type' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            $validated['transaction_date'] = Carbon::createFromFormat('Y-m-d', $validated['transaction_date'])->format('Y-m-d');
        } catch (\Exception $e) {
            $validated['transaction_date'] = Carbon::parse($validated['transaction_date'])->format('Y-m-d');
        }

        BankTransaction::create($validated);
        return back()->with('success','Transaction added');
    }

    public function reconcile(BankTransaction $txn)
    {
        $txn->update(['is_reconciled'=>1]);
        return back();
    }
}
