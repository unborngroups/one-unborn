<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TemplateHelper;
use App\Models\DebitNote;
use App\Models\VendorInvoice;
use Carbon\Carbon;

class DebitNoteController extends Controller
{
    public function index()
    {
        $debitNotes = DebitNote::with('vendorInvoice.vendor')->latest()->get();
        $permissions = TemplateHelper::getUserMenuPermissions('Purchases') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
        return view('finance.purchases.debit_notes.index', compact('debitNotes', 'permissions'));
    }

    public function create()
    {
        $vendorInvoices = VendorInvoice::with('vendor')->latest()->get();

        return view('finance.purchases.debit_notes.create', compact('vendorInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_invoice_id' => 'required|exists:vendor_invoices,id',
            'debit_note_no'     => 'required',
            'date'              => 'required|date',
            'amount'            => 'required|numeric|between:0,9999999999999999.99',
            'reason'            => 'nullable|string',
        ]);

        $date = $validated['date'];
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
            $carbonDate = Carbon::createFromFormat('d-m-Y', $date);
        } else {
            $carbonDate = Carbon::parse($date);
        }
        $validated['date'] = $carbonDate->format('Y-m-d');

        DebitNote::create($validated);

        return redirect()
            ->route('finance.debit-notes.index')
            ->with('success', 'Debit note created successfully');
    }

    public function destroy($id)
    {
        DebitNote::findOrFail($id)->delete();

        return redirect()
            ->route('finance.debit-notes.index')
            ->with('success', 'Debit note deleted successfully');
    }
}
