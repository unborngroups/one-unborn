<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorInvoice;
use App\Models\Vendor;
use Carbon\Carbon;
use App\Helpers\TemplateHelper;

class VendorInvoiceController extends Controller
{
    public function index()
    {
        $invoices = VendorInvoice::with('vendor')->latest()->get();
        $permissions = TemplateHelper::getUserMenuPermissions('Purchases') ?? (object)[
            'can_menu' => true,
            'can_add' => true,
            'can_edit' => true,
            'can_delete' => true,
            'can_view' => true,
        ];
        return view('finance.purchases.vendor_invoices.index', compact('invoices', 'permissions'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('vendor_name')->get();
        return view('finance.purchases.vendor_invoices.create', compact('vendors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'invoice_no'    => 'required',
            'invoice_date'  => 'required|date',
            'total_amount'  => 'required|numeric',
            'status'        => 'required|string',
            'subtotal'      => 'nullable|numeric',
            'gst_amount'    => 'nullable|numeric',
        ]);

        $data['invoice_date'] = Carbon::parse($data['invoice_date'])->format('Y-m-d');

        VendorInvoice::create($data);

        return redirect()
            ->route('finance.vendor-invoices.index')
            ->with('success', 'Vendor invoice created successfully');
    }

    public function edit($id)
    {
        $invoice = VendorInvoice::findOrFail($id);
        $vendors = Vendor::orderBy('vendor_name')->get();
        return view('finance.purchases.vendor_invoices.edit', compact('invoice','vendors'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'invoice_no'    => 'required',
            'invoice_date'  => 'required|date',
            'total_amount'  => 'required|numeric',
            'status'        => 'required|string',
            'subtotal'      => 'nullable|numeric',
            'gst_amount'    => 'nullable|numeric',
        ]);

        $data['invoice_date'] = Carbon::parse($data['invoice_date'])->format('Y-m-d');

        $invoice = VendorInvoice::findOrFail($id);
        $invoice->update($data);

        return redirect()
            ->route('finance.vendor-invoices.index')
            ->with('success', 'Vendor invoice updated successfully');
    }

    public function destroy($id)
    {
        VendorInvoice::findOrFail($id)->delete();

        return redirect()
            ->route('finance.vendor-invoices.index')
            ->with('success', 'Vendor invoice deleted successfully');
    }
}
