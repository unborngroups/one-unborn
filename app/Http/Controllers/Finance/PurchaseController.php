<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseInvoice;
use App\Models\Items;
use App\Models\Deliverables;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = PurchaseInvoice::latest()->get();
        return view('finance.purchases.index', compact('purchases'));
    }

    public function create(Request $request)
    {
        $vendors = Vendor::all();
        $items = Items::all();

        $deliverable = Deliverables::with([
            'feasibility.company',
            'feasibility.client',
            'purchase_order'
        ])->findOrFail($request->deliverable_id);

        return view('finance.purchases.create', compact('vendors', 'items', 'deliverable'));
    }

    
public function show($id)
{
    $purchase = PurchaseInvoice::findOrFail($id);
    $deliverables = Deliverables::find($purchase->deliverable_id);
    // You can add more related data as needed
    return view('finance.purchases.show', compact('purchase', 'deliverables'));
}

public function edit($id)
{
    $purchase = PurchaseInvoice::findOrFail($id);
    $vendors = Vendor::all();
    $items = Items::all();
    $deliverables = Deliverables::find($purchase->deliverable_id);
    return view('finance.purchases.edit', compact('purchase','vendors','items','deliverables'));
}

    public function store(Request $request)
    {

                $deliverable = Deliverables::with(['feasibility.company', 'feasibility.client'])->findOrFail($request->deliverable_id);

            $purchase = new PurchaseInvoice();
            $purchase->company_id = $request->company_id;
            $purchase->invoice_no = $request->invoice_no;
            $purchase->invoice_date = $request->invoice_date;
            $purchase->due_date = $request->due_date;
            $purchase->vendor_name = $request->vendor_name;
            $purchase->vendor_email = $request->vendor_email;
            $purchase->vendor_phone = $request->vendor_phone;
            $purchase->vendor_address = $request->vendor_address;
            $purchase->vendor_gstin = $request->vendor_gstin;
            $purchase->sub_total = $request->sub_total ?? 0;
            $purchase->cgst_total = $request->cgst_total ?? 0;
            $purchase->sgst_total = $request->sgst_total ?? 0;
            $purchase->grand_total = $request->grand_total ?? 0;
            $purchase->status = $request->status ?? 'draft';
            $purchase->notes = $request->notes;
            $purchase->terms = $request->terms;
            $purchase->save();

        // link delivery
        $deliverable->invoice_id = $purchase->id;
        $deliverable->save();

        // Store the items
        DB::transaction(function () use ($request) {

            $invoice = PurchaseInvoice::create([
                'type' => 'purchase',
                'vendor_id' => $request->vendor_id,
                // 'customer_name' => $request->customer_name,
                'invoice_no' => $request->invoice_number, // map it
                'invoice_date' => $request->invoice_date,
                'total_amount' => $request->total_amount,
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }
        });

        return redirect()->route('finance.purchases.index')
            ->with('success', 'Purchase Invoice Created');
    }

    public function update(Request $request, $id)
{
    $purchase = PurchaseInvoice::findOrFail($id);

    $request->validate([
        'vendor_id' => 'required',
        'invoice_number' => 'required',
        'total_amount' => 'required|numeric'
    ]);

    $purchase->update([
        'vendor_id' => $request->vendor_id,
        'invoice_number' => $request->invoice_number,
        'invoice_date' => $request->invoice_date,
        'total_amount' => $request->total_amount,
    ]);

    return redirect()
        ->route('finance.purchases.index')
        ->with('success', 'Purchase Invoice Updated Successfully');
}

    public function destroy($id)
    {
        $purchase = PurchaseInvoice::findOrFail($id);
        $purchase->delete();
        return back()->with('success', 'Purchase Deleted');
    }
   

    public function pdf($id)
{
    $purchase = PurchaseInvoice::findOrFail($id);

    $invoice = PurchaseInvoice::with([
        'company',
        'vendor',
        'deliverable.feasibility.company',
        'deliverable.feasibility.vendor',
        'deliverable.purchaseOrder',
        // 'item',
    ])->findOrFail($id);

    $company = $invoice->company;
    $vendor = $invoice->vendor;
    // $item = $invoice->item;
    $deliverables = $invoice->deliverable;
    $feasibility = $deliverables->feasibility ?? null;

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'finance.purchases.pdf',
        compact(
            'purchase',
            'invoice',
            'company',
            'vendor',
            'deliverables',
            'feasibility',
            // 'item'
        )
    );

    
    return $pdf->stream('purchase-invoice-'.$purchase->invoice_no.'.pdf');
}

public function print($id)
{
    $purchase = PurchaseInvoice::with(['vendor','items.item'])
        ->where('type','purchase')
        ->findOrFail($id);

        // $deliverables = Deliverable::find($purchase->deliverable_id);

    return view('finance.purchases.print', compact('purchase'));
}

public function submit($id)
{
    $purchase = PurchaseInvoice::where('type','purchase')->findOrFail($id);

    $purchase->update([
        'status' => 'submitted'
    ]);

    return redirect()
        ->route('finance.purchases.show',$id)
        ->with('success','Invoice Submitted Successfully');
}

}
