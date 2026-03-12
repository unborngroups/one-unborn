<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesInvoice;
use App\Models\Client;
use App\Models\Items;
use Illuminate\Support\Facades\DB;
use App\Models\Deliverables;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = SalesInvoice::latest()->get();
        return view('finance.sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clients = Client::all();
        $items = Items::all();
        $deliverable = Deliverables::with([
            'feasibility.company',
            'feasibility.client',
            'purchase_order'
        ])->findOrFail($request->deliverable_id);

        return view('finance.sales.create', compact('clients', 'items', 'deliverable'));
    }

    /**
     * show the form for editing the specified resource.
     */
    public function show(string $id)
    {
        $sales = SalesInvoice::with([
        
            'company',
            'client',
            'deliverable.feasibility.company',
            'deliverable.feasibility.client',
            'deliverable.purchaseOrder',
      
         
        ])->findOrFail($id);
        $company = $sales->company;
        $client = $sales->client;
        $deliverables = $sales->deliverable;
        $feasibility = $deliverables->feasibility ?? null;

        // $deliverables = Deliverable::find($sales->deliverable_id);

        // If you need related data, add relationships to SalesInvoice model and eager load here
        return view('finance.sales.show', compact('sales', 'company', 'client', 'deliverables', 'feasibility'));
    }

    /**
     * edit the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $clients = Client::all();
        $items = Items::all();
        $sales = SalesInvoice::findOrFail($id);
       $deliverables = Deliverables::find($sales->deliverable_id);

        return view('finance.sales.edit', compact('sales', 'clients', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $deliverable = Deliverables::with([
        'feasibility.company',
        'feasibility.client'
    ])->findOrFail($request->deliverable_id);

    $sales = new SalesInvoice();
    $sales->company_id = $request->company_id;
    $sales->invoice_no = $request->invoice_no;
    $sales->invoice_date = $request->invoice_date;
    $sales->due_date = $request->due_date;
    $sales->customer_name = $request->customer_name;
    $sales->customer_email = $request->customer_email;
    $sales->customer_phone = $request->customer_phone;
    $sales->customer_address = $request->customer_address;
    $sales->customer_gstin = $request->customer_gstin;
    $sales->sub_total = $request->sub_total ?? 0;
    $sales->cgst_total = $request->cgst_total ?? 0;
    $sales->sgst_total = $request->sgst_total ?? 0;
    $sales->grand_total = $request->grand_total ?? 0;
    $sales->status = $request->status ?? 'draft';
    $sales->notes = $request->notes;
    $sales->terms = $request->terms;
    $sales->save();

    return redirect()->route('finance.sales.index')
        ->with('success', 'Sales Invoice Created Successfully');
}
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sales = SalesInvoice::findOrFail($id);
        $sales->update($request->all());

        return redirect()->route('finance.sales.index')
            ->with('success', 'Sales Invoice Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $sales = SalesInvoice::findOrFail($id);
         $sales->delete();

        return back()->with('success', 'Sales Invoice Deleted');
    }

    public function pdf($id)
{
    $sales = SalesInvoice::findOrFail($id);

    // Update this section to use only the sales invoice data as needed
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'finance.saless.pdf',
        compact('sales')
    );
    return $pdf->stream('sales-invoice-'.$sales->invoice_no.'.pdf');
}

public function print($id)
{
    $sales = SalesInvoice::with(['client','items.item'])
        ->where('type','sales')
        ->findOrFail($id);

        // $deliverables = Deliverable::find($sales->deliverable_id);

    return view('finance.sales.print', compact('sales'));
}

public function submit($id)
{
    $sales = SalesInvoice::where('type','sales')->findOrFail($id);

    $sales->update([
        'status' => 'submitted'
    ]);

    return redirect()
        ->route('finance.sales.show',$id)
        ->with('success','Invoice Submitted Successfully');
}

private function getCompanyCode($name)
{
    return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
}

private function getNextInvoiceNumber($ourCompany, $clientCompany)
{
    $ourCode = $this->getCompanyCode($ourCompany);
    $clientCode = $this->getCompanyCode($clientCompany);

    $prefix = $ourCode . '-' . $clientCode . '-';

    $lastInvoice = SalesInvoice::where('invoice_no','like',$prefix.'%')
                    ->orderBy('id','desc')
                    ->first();

    if ($lastInvoice) {
        $lastNumber = intval(substr($lastInvoice->invoice_no,-6));
        $nextNumber = str_pad($lastNumber + 1,6,'0',STR_PAD_LEFT);
    } else {
        $nextNumber = '000001';
    }

    return $prefix.$nextNumber;
}

}
