<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\VendorLearningLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function indexByStatus($status)
    {
        $invoices = PurchaseInvoice::where('status', $status)
            ->with('vendor', 'createdBy')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $invoices,
            'count' => $invoices->total()
        ]);
    }

    public function show($id)
    {
        $invoice = PurchaseInvoice::with([
            'vendor',
            'createdBy',
            'items'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    public function verify(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $invoice = PurchaseInvoice::findOrFail($id);

            $validated = $request->validate([
                'vendor_id' => 'required|exists:vendors,id',
                'invoice_number' => 'required|string',
                'amount' => 'required|numeric'
            ]);

            // Update invoice
            $invoice->update([
                'vendor_id' => $validated['vendor_id'],
                'invoice_no' => $validated['invoice_number'],
                'amount' => $validated['amount'],
                'status' => 'verified'
            ]);

            // Update vendor learning logs
            $this->updateVendorLearning($invoice, $validated['vendor_id']);

            DB::commit();

            Log::info('Invoice verified: ' . $id . ' by user: ' . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Invoice verified successfully',
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $invoice = PurchaseInvoice::findOrFail($id);

            if ($invoice->status !== 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only verified invoices can be approved'
                ], 400);
            }

            $invoice->update(['status' => 'approved']);

            DB::commit();

            Log::info('Invoice approved: ' . $id . ' by user: ' . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Invoice approved successfully',
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice approval failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function markPaid(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $invoice = PurchaseInvoice::findOrFail($id);

            if ($invoice->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved invoices can be marked as paid'
                ], 400);
            }

            $invoice->update(['status' => 'paid']);

            DB::commit();

            Log::info('Invoice marked as paid: ' . $id . ' by user: ' . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Invoice marked as paid',
                'data' => $invoice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice mark paid failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function updateVendorLearning($invoice, $vendorId)
    {
        // Find or create learning log
        $learning = VendorLearningLog::where('vendor_name_raw', $invoice->vendor_name_raw)
            ->where('gstin', $invoice->gstin)
            ->where('matched_vendor_id', $vendorId)
            ->first();

        if ($learning) {
            // Update existing learning
            $learning->update([
                'is_verified' => true,
                'confidence' => min(1.0, $learning->confidence + 0.1)
            ]);
        } else {
            // Create new learning log
            VendorLearningLog::create([
                'vendor_name_raw' => $invoice->vendor_name_raw,
                'gstin' => $invoice->gstin,
                'matched_vendor_id' => $vendorId,
                'confidence' => 0.95,
                'is_verified' => true
            ]);
        }

        Log::info('Vendor learning updated for invoice: ' . $invoice->id);
    }
}
