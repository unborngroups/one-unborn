<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\FinanceApproval;
use App\Models\FinanceAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('group')->latest()->get();
        return view('finance.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $groups = AccountGroup::all();
        return view('finance.accounts.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_group_id' => 'required',
            'account_name'     => 'required',
            'balance_type'     => 'required',
            'opening_balance'  => 'required|numeric',
        ]);

        $account = Account::create([
            ...$validated,
            'status' => 'draft',
            'maker_id' => Auth::id(),
        ]);

        $this->logAudit($account, 'create', [], $account->toArray());

        return redirect()->route('finance.accounts.index')
            ->with('success', 'Account created successfully and marked as draft');
    }

    public function edit($id)
{
    $account = Account::findOrFail($id);
    $groups = AccountGroup::all();

    return view('finance.accounts.edit', compact('account', 'groups'));
}


    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'account_group_id' => 'required',
            'account_name'     => 'required|string|max:255',
            'account_code'     => 'nullable|string|max:50',
            'balance_type'     => 'required|in:Debit,Credit',
            'opening_balance'  => 'required|numeric',
            'is_active'        => 'sometimes|boolean',
        ]);

        $oldValues = $account->getOriginal();

        if ($account->is_locked) {
    return back()->with('error', 'Approved accounts cannot be modified.');
}


        $this->logAudit($account, 'update', $oldValues, $account->getChanges());

        return redirect()->route('finance.accounts.index')
            ->with('success','Account updated successfully');
    }

    public function toggle(Account $account)
    {
        if ($account->status !== 'approved') {
    return back()->with('error', 'Only approved accounts can be toggled.');
}
    }

    public function submitForApproval(Account $account)
    {
        if ($account->status !== 'draft') {
            return back()->with('error', 'Only draft accounts can be submitted.');
        }

        $account->update([
            'status' => 'pending_checker',
            'maker_id' => Auth::id(),
            'maker_submitted_at' => now(),
        ]);

        FinanceApproval::create([
            'model_type' => Account::class,
            'model_id' => $account->id,
            'action' => 'account_create',
            'maker_id' => Auth::id(),
        ]);

        $this->logAudit($account, 'submit_for_approval', ['status' => 'draft'], ['status' => 'pending_checker']);

        return back()->with('success', 'Account submitted for checker approval.');
    }

    public function approve(Request $request, Account $account)
    {
        if ($account->status !== 'pending_checker') {
            return back()->with('error', 'Only accounts pending approval can be approved.');
        }

        $old = $account->getOriginal();

        $account->update([
            'status' => 'approved',
            'checker_id' => Auth::id(),
            'checker_approved_at' => now(),
            'locked_at' => now(),
            'is_locked' => true,
        ]);

            $approval = FinanceApproval::where([
                ['model_type', Account::class],
                ['model_id', $account->id],
            ])->latest()->first();

            if ($approval) {
                $approval->update([
                    'checker_id' => Auth::id(),
                    'status' => 'approved',
                    'remarks' => $request->input('remarks'),
                ]);
            }

        $this->logAudit($account, 'approve', $old, $account->getChanges());

        return back()->with('success', 'Account approved and locked.');
    }

    public function reject(Request $request, Account $account)
    {
        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        if ($account->status !== 'pending_checker') {
            return back()->with('error', 'Only accounts pending approval can be rejected.');
        }

        $old = $account->getOriginal();

        $account->update([
            'status' => 'rejected',
        ]);

            $approval = FinanceApproval::where([
                ['model_type', Account::class],
                ['model_id', $account->id],
            ])->latest()->first();

            if ($approval) {
                $approval->update([
                    'checker_id' => Auth::id(),
                    'status' => 'rejected',
                    'remarks' => $request->remarks,
                ]);
            }

        $this->logAudit($account, 'reject', $old, ['status' => 'rejected']);

        return back()->with('success', 'Account rejected and returned to maker.');
    }

    private function logAudit(Account $account, string $action, ?array $old, ?array $new): void
    {
        FinanceAuditLog::create([
            'model_type' => Account::class,
            'model_id' => $account->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
        ]);
    }
}
