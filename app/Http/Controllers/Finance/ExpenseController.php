<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;
use App\Helpers\TemplateHelper;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->get();
        $permissions = TemplateHelper::getUserMenuPermissions('Purchases') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
        return view('finance.purchases.expenses.index', compact('expenses', 'permissions'));
    }

    public function create()
    {
        return view('finance.purchases.expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_type' => 'required',
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'amount'       => 'required|numeric',
        ]);

        $data['expense_date'] = Carbon::createFromFormat('Y-m-d', $data['expense_date'])->format('Y-m-d');

        Expense::create($data);

        return redirect()
            ->route('finance.expenses.index')
            ->with('success', 'Expense added successfully');
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();

        return redirect()
            ->route('finance.expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
