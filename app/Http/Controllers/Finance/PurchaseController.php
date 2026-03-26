<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;
use App\Models\Items;
use App\Models\Deliverables;
use Smalot\PdfParser\Parser;

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
    $deliverables = Deliverables::all();
    $items = Items::all();

        return view('finance.purchases.create', compact('vendors', 'deliverables', 'items'));
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
    $deliverables = Deliverables::all();

    return view('finance.purchases.edit', compact('purchase','vendors','items','deliverables'));
}

public function store(Request $request)
{
    // ✅ Validation
    $request->validate([
        'vendor_id' => 'required|exists:vendors,id',
        'invoice_number' => 'required|string|max:255',
        'invoice_date' => 'nullable|date',
        'total_amount' => 'required|numeric',
        'po_invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    // ✅ Upload file
    $fileName = $this->uploadImage($request, 'po_invoice_file', 'poinvoice_files');

    $deliverable = Deliverables::find($request->deliverable_id);

$po = null;

if ($deliverable && $deliverable->purchase_order_id) {
    $po = PurchaseOrder::find($deliverable->purchase_order_id);
}

    $status = 'ok';

    if ($po) {
        $poTotal = (
            ($po->arc_per_link ?? 0) +
            ($po->otc_per_link ?? 0) +
            ($po->static_ip_cost_per_link ?? 0)
        ) * ($po->no_of_links ?? 1);

        if ($request->total_amount > $poTotal) {
            $status = 'higher';
        } elseif ($request->total_amount < $poTotal) {
            $status = 'lower';
        }
    }

    // ✅ Create Purchase Invoice
    $invoice = PurchaseInvoice::create([
        'type' => 'purchase',
        'vendor_id' => $request->vendor_id,
        'deliverable_id' => $request->deliverable_id,
        'invoice_no' => $request->invoice_number,
        'invoice_date' => $request->invoice_date,
        'total_amount' => $request->total_amount,
        'po_invoice_file' => $fileName,
        'status' => $status, // optional but useful
    ]);

    // ✅ Save Items (Router / Cable etc.)
    if ($request->items && is_array($request->items)) {

        $itemsData = [];

foreach ($request->items as $item) {
    $qty = $item['quantity'] ?? 0;
    $price = $item['price'] ?? 0;

    $itemsData[] = [
        'item_id' => $item['item_id'],
        'quantity' => $qty,
        'price' => $price,
        'total' => $qty * $price,
    ];
}

$invoice->items()->createMany($itemsData);

    }

    return redirect()
        ->route('finance.purchases.index')
        ->with('success', 'Purchase Invoice Created Successfully');
}

/**
 * UploadImage path
 */
      private function uploadImage($request, $field, $folder)
    {
        if ($request->hasFile($field)) {
            $file = $request->file($field);
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("images/{$folder}"), $filename);
            return $filename;
        }
        return null;
    }

    /**
    *update path
    */

    public function update(Request $request, $id)
{
    $purchase = PurchaseInvoice::findOrFail($id);

    $request->validate([
        'vendor_id' => 'required|exists:vendors,id',
        'invoice_number' => 'required',
        'invoice_date' => 'nullable|date',
        'total_amount' => 'required|numeric',
        'po_invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
    ]);

    // ✅ Update file if new uploaded
    $fileName = $this->updateImage(
        $request,
        $purchase->po_invoice_file,
        'po_invoice_file',
        'poinvoice_files'
    );

    // ✅ Get PO via Deliverable
    $deliverable = Deliverables::find($request->deliverable_id);

    $po = null;

    if ($deliverable && $deliverable->purchase_order_id) {
        $po = PurchaseOrder::find($deliverable->purchase_order_id);
    }

    $status = 'ok';

    if ($po) {
        $poTotal = (
            ($po->arc_per_link ?? 0) +
            ($po->otc_per_link ?? 0) +
            ($po->static_ip_cost_per_link ?? 0)
        ) * ($po->no_of_links ?? 1);

        if ($request->total_amount > $poTotal) {
            $status = 'higher';
        } elseif ($request->total_amount < $poTotal) {
            $status = 'lower';
        }
    }

    // ✅ Final update
    $purchase->update([
        'vendor_id' => $request->vendor_id,
        'deliverable_id' => $request->deliverable_id,
        'invoice_no' => $request->invoice_number,
        'invoice_date' => $request->invoice_date,
        'total_amount' => $request->total_amount,
        'po_invoice_file' => $fileName,
        'status' => $status,
    ]);

    return redirect()
        ->route('finance.purchases.index')
        ->with('success', 'Purchase Invoice Updated Successfully');
}

private function updateImage($request, $oldFile, $field, $folder)
    {
        if ($request->hasFile($field)) {
            $oldPath = public_path("images/{$folder}/{$oldFile}");
            if ($oldFile && file_exists($oldPath)) {
                unlink($oldPath);
            }

            $file = $request->file($field);
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path("images/{$folder}"), $filename);
            return $filename;
        }
        return $oldFile;
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
        // ->where('type','purchase')
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

// public function getPoData($vendorId)
// {
//     $vendor = Vendor::find($vendorId);

//     $po = PurchaseOrder::where('vendor_id', $vendor->id)
//             ->latest()
//             ->first();

//     if (!$po) {
//         return response()->json([
//             'arc_total' => 0,
//             'otc_total' => 0,
//             'static_total' => 0,
//         ]);
//     }

//     return response()->json([
//         'arc_total' => $po->arc_per_link * $po->no_of_links,
//         'otc_total' => $po->otc_per_link * $po->no_of_links,
//         'static_total' => $po->static_ip_cost_per_link * $po->no_of_links,
//     ]);
// }

public function getPoData($deliverableId)
{
    $deliverable = Deliverables::find($deliverableId);

    $po = PurchaseOrder::find($deliverable->purchase_order_id);

    if (!$po) {
        return response()->json([
            'arc_total' => 0,
            'otc_total' => 0,
            'static_total' => 0,
        ]);
    }

    return response()->json([
        'arc_total' => $po->arc_per_link * $po->no_of_links,
        'otc_total' => $po->otc_per_link * $po->no_of_links,
        'static_total' => $po->static_ip_cost_per_link * $po->no_of_links,
    ]);
}

public function parseInvoice(Request $request)
{
    if (!$request->hasFile('file')) {
        return response()->json(['error' => 'No file'], 400);
    }

    $file = $request->file('file');

    $response = Http::attach(
        'file',
        file_get_contents($file->getRealPath()),
        $file->getClientOriginalName()
    )->post('https://api.ocr.space/parse/image', [
        'apikey' => env('OCR_API_KEY'),
        'language' => 'eng',
    ]);

    $result = $response->json();

    // ✅ OCR error check
    if (!empty($result['IsErroredOnProcessing']) && $result['IsErroredOnProcessing']) {
        return response()->json([
            'error' => 'OCR failed',
            'message' => $result['ErrorMessage'] ?? 'Unknown error'
        ], 500);
    }

    // ✅ Extract text
    $text = $result['ParsedResults'][0]['ParsedText'] ?? '';

    if (!$text) {
        return response()->json(['error' => 'Parsing failed'], 400);
    }

    // 🔍 Extract values
    $arc = $this->extractAmount($text, 'ARC');
    $otc = $this->extractAmount($text, 'OTC');
    $static = $this->extractAmount($text, 'Static');
    $router = $this->extractAmount($text, 'Router');

    return response()->json([
        'arc' => $arc,
        'otc' => $otc,
        'static' => $static,
        'router' => $router,
    ]);
}

// 🔧 Helper function
private function extractAmount($text, $keyword)
{
    $pattern = '/' . preg_quote($keyword, '/') . '[\s:₹]*([\d,]+(\.\d+)?)/i';

    preg_match($pattern, $text, $matches);

    if (isset($matches[1])) {
        return (float) str_replace(',', '', $matches[1]);
    }

    return 0;
}

}
