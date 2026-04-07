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
    $sales->invoice_no = $this->generateInvoiceNumber($deliverable);
    $sales->invoice_date = $request->invoice_date;
    $sales->due_date = $request->due_date;
    $sales->client_name = $request->client_name;
    $sales->client_email = $request->client_email;
    $sales->client_phone = $request->client_phone;
    $sales->client_address = $request->client_address;
    $sales->client_gstin = $request->client_gstin;
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

private static $stateAbbr = [
    'Andhra Pradesh' => 'AP', 'Arunachal Pradesh' => 'AR', 'Assam' => 'AS', 'Bihar' => 'BR',
    'Chhattisgarh' => 'CG', 'Goa' => 'GA', 'Gujarat' => 'GJ', 'Haryana' => 'HR', 'Himachal Pradesh' => 'HP',
    'Jammu and Kashmir' => 'JK', 'Jharkhand' => 'JH', 'Karnataka' => 'KA', 'Kerala' => 'KL', 'Madhya Pradesh' => 'MP',
    'Maharashtra' => 'MH', 'Manipur' => 'MN', 'Meghalaya' => 'ML', 'Mizoram' => 'MZ', 'Nagaland' => 'NL',
    'Orissa' => 'OR', 'Punjab' => 'PB', 'Rajasthan' => 'RJ', 'Sikkim' => 'SK', 'Tamil Nadu' => 'TN', 'Tripura' => 'TR',
    'Uttarakhand' => 'UK', 'Uttar Pradesh' => 'UP', 'West Bengal' => 'WB', 'Telangana' => 'TS',
    'Andaman and Nicobar Islands' => 'AN', 'Chandigarh' => 'CH', 'Dadra and Nagar Haveli' => 'DH', 'Daman and Diu' => 'DD',
    'Delhi' => 'DL', 'Lakshadweep' => 'LD', 'Pondicherry' => 'PY',
];

private function generateInvoiceNumber(Deliverables $deliverable): string
{
    $company  = $deliverable->feasibility->company ?? null;
    $client   = $deliverable->feasibility->client  ?? null;

    // Company short name (up to 4 uppercase letters, no spaces/special chars)
    $companyShort = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $company->short_name ?? $company->company_name ?? 'CO'), 0, 4));

    // Client short name (up to 4 uppercase letters)
    $clientShort = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $client->short_name ?? $client->client_name ?? 'CL'), 0, 4));

    // State abbreviation from client state
    $state = trim($client->state ?? '');
    $stateCode = self::$stateAbbr[$state] ?? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $state), 0, 2)) ?: 'XX';

    // Financial year: e.g. Apr 2025 → "25-26"
    $now = now();
    $fyStart = $now->month >= 4 ? $now->year : $now->year - 1;
    $fyEnd   = $fyStart + 1;
    $year    = substr($fyStart, 2) . '-' . substr($fyEnd, 2); // e.g. "25-26"

    // Prefix for this combination
    $prefix = $companyShort . '/' . $clientShort . '/' . $stateCode . '/' . $year . '/';

    // Serial: count existing invoices with same prefix, padded 4 digits
    $count = SalesInvoice::where('invoice_no', 'like', $prefix . '%')->count();
    $serial = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

    return $prefix . $serial;
}

}
