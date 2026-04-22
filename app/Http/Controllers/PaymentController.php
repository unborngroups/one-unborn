<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentBatch;
use App\Models\PaymentTransaction;
use App\Models\PurchaseInvoice;
use App\Models\RazorpayWebhook;
use App\Services\PaymentService;
use App\Services\RazorpayService;
use App\Jobs\ProcessPaymentBatchJob;
use App\Jobs\ProcessRazorpayWebhookJob;
use App\Jobs\AutoPaymentDetectionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Exports\PaymentBatchExport;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private RazorpayService $razorpayService
    ) {}

    /**
     * Display payment dashboard
     */
    public function dashboard(Request $request)
    {
        $companyId = $this->getCurrentCompanyId();
        $period = $request->get('period', 'month');
        
        $stats = $this->paymentService->getPaymentStats($companyId, $period);
        
        $recentBatches = PaymentBatch::where('company_id', $companyId)
            ->with('paymentTransactions.purchaseInvoice.vendor')
            ->latest()
            ->take(10)
            ->get();

        $pendingInvoices = PurchaseInvoice::with('vendor')
            ->where('company_id', $companyId)
            ->where('payment_status', 'pending')
            ->where('auto_payment_enabled', true)
            ->where('due_date', '<=', now())
            ->orderBy('due_date')
            ->take(20)
            ->get();

        return view('payments.dashboard', compact('stats', 'recentBatches', 'pendingInvoices', 'period'));
    }

    /**
     * Display payment batches
     */
    public function batches(Request $request)
    {
        $companyId = $this->getCurrentCompanyId();
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 25;

        $query = PaymentBatch::where('company_id', $companyId)
            ->with(['approvedBy', 'paymentTransactions']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('batch_reference', 'like', "%{$search}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $batches = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('payments.batches', compact('batches'));
    }

    /**
     * Show payment batch details
     */
    public function batchDetails(PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $batch->load([
            'paymentTransactions.purchaseInvoice.vendor',
            'approvedBy',
            'company'
        ]);

        return view('payments.batch-details', compact('batch'));
    }

    /**
     * Create payment batch from selected invoices
     */
    public function createBatch(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array|min:1',
            'invoice_ids.*' => 'exists:purchase_invoices,id',
        ]);

        $companyId = $this->getCurrentCompanyId();
        $invoiceIds = $request->invoice_ids;

        try {
            $batch = $this->paymentService->createPaymentBatch($invoiceIds, $companyId);
            
            return redirect()->route('payments.batches.show', $batch->id)
                ->with('success', 'Payment batch created successfully');

        } catch (\Exception $e) {
            Log::error('Failed to create payment batch', [
                'error' => $e->getMessage(),
                'invoice_ids' => $invoiceIds,
            ]);

            return back()->with('error', 'Failed to create payment batch: ' . $e->getMessage());
        }
    }

    /**
     * Submit batch for accountant approval
     */
    public function submitForAccountantApproval(PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        try {
            $this->paymentService->submitForAccountantApproval($batch, Auth::id());
            
            return back()->with('success', 'Payment batch submitted for accountant approval');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit for accountant approval: ' . $e->getMessage());
        }
    }

    /**
     * Approve batch at accountant level
     */
    public function approveAccountantLevel(Request $request, PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->paymentService->approveAccountantLevel($batch, Auth::id(), $request->remarks);
            
            return back()->with('success', 'Payment batch approved at accountant level');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve batch: ' . $e->getMessage());
        }
    }

    /**
     * Approve batch at finance manager level
     */
    public function approveFinanceManagerLevel(Request $request, PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->paymentService->approveFinanceManagerLevel($batch, Auth::id(), $request->remarks);
            
            return back()->with('success', 'Payment batch approved at finance manager level');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve batch: ' . $e->getMessage());
        }
    }

    /**
     * Reject batch at accountant level
     */
    public function rejectAccountantLevel(Request $request, PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->rejectPaymentBatch($batch, Auth::id(), $request->remarks, 'accountant');
            
            return back()->with('success', 'Payment batch rejected at accountant level');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject batch: ' . $e->getMessage());
        }
    }

    /**
     * Reject batch at finance manager level
     */
    public function rejectFinanceManagerLevel(Request $request, PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->rejectPaymentBatch($batch, Auth::id(), $request->remarks, 'finance_manager');
            
            return back()->with('success', 'Payment batch rejected at finance manager level');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject batch: ' . $e->getMessage());
        }
    }

    /**
     * Approve payment batch
     */
    public function approveBatch(Request $request, PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            $this->paymentService->approvePaymentBatch($batch, Auth::id(), $request->remarks);
            
            // Dispatch processing job
            ProcessPaymentBatchJob::dispatch($batch, $batch->company_id)
                ->onQueue('payments');

            return back()->with('success', 'Payment batch approved and queued for processing');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve batch: ' . $e->getMessage());
        }
    }

    /**
     * Reject payment batch
     */
    public function rejectBatch(Request $request, PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        try {
            $this->paymentService->rejectPaymentBatch($batch, Auth::id(), $request->remarks);
            
            return back()->with('success', 'Payment batch rejected');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject batch: ' . $e->getMessage());
        }
    }

    /**
     * Process batch manually
     */
    public function processBatch(PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        try {
            // Dispatch processing job
            ProcessPaymentBatchJob::dispatch($batch, $batch->company_id)
                ->onQueue('payments');

            return back()->with('success', 'Payment batch queued for processing');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process batch: ' . $e->getMessage());
        }
    }

    /**
     * Export batch to Excel
     */
    public function exportBatch(PaymentBatch $batch)
    {
        $this->authorizeCompanyAccess($batch);

        $data = $this->paymentService->exportPaymentData($batch);
        
        return (new FastExcel($data))->download("payment_batch_{$batch->batch_reference}.xlsx");
    }

    /**
     * Display pending invoices for payment
     */
    public function pendingInvoices(Request $request)
    {
        $companyId = $this->getCurrentCompanyId();
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 25;

        $query = PurchaseInvoice::with('vendor')
            ->where('company_id', $companyId)
            ->where('payment_status', 'pending')
            ->where('auto_payment_enabled', true)
            ->where('due_date', '<=', now());

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vq) use ($search) {
                      $vq->where('vendor_name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('due_date')->paginate($perPage);

        return view('payments.pending_invoices', compact('invoices'));
    }

    /**
     * Handle Razorpay webhook
     */
    public function handleWebhook(Request $request)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->all();

        Log::info('Razorpay webhook received', [
            'event_type' => $payload['event'] ?? 'unknown',
            'webhook_id' => $payload['webhook_id'] ?? 'unknown',
        ]);

        try {
            // Validate webhook signature
            $companyId = $this->extractCompanyIdFromWebhook($payload);
            $razorpayService = new RazorpayService($companyId);
            
            if (!$razorpayService->validateWebhookSignature($payload, $signature)) {
                Log::error('Invalid webhook signature', ['payload' => $payload]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Store webhook
            $webhook = RazorpayWebhook::create([
                'webhook_id' => $payload['webhook_id'] ?? uniqid(),
                'event_type' => $payload['event'] ?? 'unknown',
                'company_id' => $companyId,
                'payload' => $payload,
            ]);

            // Dispatch processing job
            ProcessRazorpayWebhookJob::dispatch($webhook)
                ->onQueue('webhooks');

            return response()->json(['status' => 'received']);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Run auto payment detection
     */
    public function runAutoDetection()
    {
        try {
            AutoPaymentDetectionJob::dispatch()
                ->onQueue('payments');

            return back()->with('success', 'Auto payment detection job queued');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to queue auto detection: ' . $e->getMessage());
        }
    }

    /**
     * Get current company ID
     */
    private function getCurrentCompanyId(): int
    {
        $user = Auth::user();
        $companies = $user->companies;
        
        if ($companies->isNotEmpty()) {
            return $companies->first()->id;
        }

        return 1; // Fallback
    }

    /**
     * Authorize company access to payment batch
     */
    private function authorizeCompanyAccess(PaymentBatch $batch): void
    {
        $companyId = $this->getCurrentCompanyId();
        
        if ($batch->company_id !== $companyId) {
            abort(403, 'Unauthorized access to payment batch');
        }
    }

    /**
     * Extract company ID from webhook payload
     */
    private function extractCompanyIdFromWebhook(array $payload): int
    {
        // Try to extract company ID from payload notes
        $notes = $payload['payload']['payout']['notes'] ?? [];
        
        if (isset($notes['batch_reference'])) {
            // Extract company ID from batch reference if encoded
            $batchRef = $notes['batch_reference'];
            if (preg_match('/PAY-(\d{3})/', $batchRef, $matches)) {
                return (int) $matches[1];
            }
        }

        return 1; // Fallback
    }
}
