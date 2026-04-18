<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Vendor;
use App\Models\Gstin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;
use App\Models\Items;
use App\Models\Deliverables;
use App\Models\CompanySetting;
use App\Models\EmailLog;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\SurepassService;

class PurchaseController extends Controller
{
    private function lastManualFetchCacheKey(): string
    {
        $userId = (int) (Auth::id() ?? 0);
        return 'purchase_invoices:last_manual_fetch_at:user:' . $userId;
    }

    private function lastFetchStatusCacheKey(): string
    {
        $userId = (int) (Auth::id() ?? 0);
        return 'purchase_invoices:last_fetch_status:user:' . $userId;
    }

    private function cacheFetchStatus(int $exitCode, string $output, int $mailReadDays): void
    {
        $cleanOutput = trim(strip_tags($output));
        $message = 'Mail checked.';
        $level = $exitCode === 0 ? 'info' : 'error';

        if ($exitCode !== 0) {
            $lines = preg_split('/\r\n|\r|\n/', $cleanOutput) ?: [];
            $message = trim((string) collect($lines)->filter()->last()) ?: 'Mail fetch failed.';
        } elseif (Str::contains($cleanOutput, 'Purchase Invoice created')) {
            $created = substr_count($cleanOutput, 'Purchase Invoice created');
            $level = 'success';
            $message = $created . ' invoice(s) imported from the last ' . $mailReadDays . ' day(s).';
        } elseif (Str::contains($cleanOutput, 'No emails found in mailbox')) {
            $message = 'Mailbox reachable, but no emails were found.';
        } elseif (Str::contains($cleanOutput, 'No new emails to process')) {
            $message = 'Mail checked successfully, but no new emails matched the read window.';
        } elseif (Str::contains($cleanOutput, 'No supported invoice attachments found')) {
            $message = 'Emails were read, but no supported invoice attachments were found.';
        } elseif (Str::contains($cleanOutput, 'Done! 0 invoice(s) created.')) {
            $message = 'Mail checked successfully, but no invoice could be created from the emails.';
        }

        Cache::put($this->lastFetchStatusCacheKey(), [
            'level' => $level,
            'message' => $message,
            'checked_at' => now()->toDateTimeString(),
            'output' => $cleanOutput,
        ], now()->addDays(30));
    }

    private function getInvoiceMailReadDays(): int
    {
        $companySetting = $this->resolveInvoiceCompanySetting();
        $mailReadDays = (int) (($companySetting?->invoice_mail_read_days) ?? 30);
        return max($mailReadDays, 1);
    }

    public function index(Request $request)
    {
        $mailReadDays = $this->getInvoiceMailReadDays();
        $this->autoFetchGmailInvoices($mailReadDays);

        $purchasesQuery = PurchaseInvoice::with(['vendor', 'deliverable.feasibility.client'])->latest();
        $fromDate = now()->subDays($mailReadDays)->startOfDay();
        $purchasesQuery->where('created_at', '>=', $fromDate)
            // Hide 'draft' & 'failed' invoices — draft stays in Auto Invoice Processing, failed goes to Failed Invoices
            ->where('status', '!=', 'draft')
            ->where('status', '!=', 'failed');

        $purchases = $purchasesQuery->get();

        $vendorGstinMap = Vendor::query()
            ->whereNotNull('gstin')
            ->get(['gstin', 'vendor_name'])
            ->mapWithKeys(function ($vendor) {
                return [strtoupper(trim((string) $vendor->gstin)) => $vendor->vendor_name];
            })
            ->toArray();

        return view('finance.purchases.index', compact('purchases', 'vendorGstinMap'));
    }

    private function autoFetchGmailInvoices(int $mailReadDays): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        @ini_set('max_execution_time', '0');

        $cooldownKey = 'purchase_invoices:auto_fetch:cooldown';
        $runningKey = 'purchase_invoices:auto_fetch:running';

        if (Cache::has($cooldownKey)) {
            return;
        }

        // Prevent duplicate runs when multiple users refresh around the same time.
        if (!Cache::add($runningKey, 1, now()->addSeconds(90))) {
            return;
        }

        try {
            $exitCode = Artisan::call('invoice:fetch-gmail', [
                '--recent' => true,
                '--days' => $mailReadDays,
            ]);
            $output = trim(Artisan::output());
            $this->cacheFetchStatus($exitCode, $output, $mailReadDays);
            if ($exitCode === 0) {
                Cache::put($this->lastManualFetchCacheKey(), now()->toDateTimeString(), now()->addDays(30));
            }
            Cache::put($cooldownKey, 1, now()->addMinutes(1)); // 1 minute cooldown after completion
        } catch (\Throwable $e) {
            Cache::put($this->lastFetchStatusCacheKey(), [
                'level' => 'error',
                'message' => 'Mail fetch failed: ' . $e->getMessage(),
                'checked_at' => now()->toDateTimeString(),
                'output' => $e->getMessage(),
            ], now()->addDays(30));
            Log::warning('Auto Gmail invoice fetch failed on purchases index.', ['error' => $e->getMessage()]);
        } finally {
            Cache::forget($runningKey);
        }
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
    $purchase = PurchaseInvoice::with(['vendor', 'items.item', 'deliverable'])->findOrFail($id);
    $deliverables = Deliverables::find($purchase->deliverable_id);

    $displayData = $this->buildPurchaseDisplayData($purchase);

    $gstin = strtoupper(trim((string) ($displayData['displayGstin'] ?? $purchase->gstin ?? $purchase->vendor_gstin ?? $purchase->gst_number ?? '')));
    $vendorFromMaster = null;

    if (!$purchase->vendor && $gstin !== '') {
        $vendorFromMaster = Vendor::query()
            ->whereRaw('UPPER(TRIM(gstin)) = ?', [$gstin])
            ->value('vendor_name');
    }

    return view('finance.purchases.show', array_merge(
        compact('purchase', 'deliverables', 'vendorFromMaster'),
        $displayData
    ));
}

public function edit($id)
{
    $purchase = PurchaseInvoice::with(['vendor', 'items', 'deliverable'])->findOrFail($id);
    $vendors = Vendor::all();

    $displayData = $this->buildPurchaseDisplayData($purchase);

    return view('finance.purchases.edit', array_merge(
        compact('purchase', 'vendors'),
        $displayData
    ));
}

private function buildPurchaseDisplayData(PurchaseInvoice $purchase): array
{
    $parsedPdfData = $this->parseSourcePdfForEdit($purchase);
    $prefillRows = $parsedPdfData['items'] ?? [];
    $rawJson = is_array($purchase->raw_json) ? $purchase->raw_json : [];

    if (!empty($parsedPdfData['vendor_name'])) {
        $purchase->vendor_name_raw = $parsedPdfData['vendor_name'];
        $purchase->vendor_name = $parsedPdfData['vendor_name'];
    }
    if (!empty($parsedPdfData['invoice_no'])) {
        $purchase->invoice_no = $parsedPdfData['invoice_no'];
    }
    if (!empty($parsedPdfData['invoice_date'])) {
        $purchase->invoice_date = $parsedPdfData['invoice_date'];
    }
    if (!empty($parsedPdfData['gstin'])) {
        $purchase->gstin = $parsedPdfData['gstin'];
    }

    $parsedSubTotal = (float) ($parsedPdfData['sub_total'] ?? 0);
    $parsedCgstTotal = (float) ($parsedPdfData['cgst_total'] ?? 0);
    $parsedSgstTotal = (float) ($parsedPdfData['sgst_total'] ?? 0);
    $parsedGrandTotal = (float) ($parsedPdfData['grand_total'] ?? 0);

    $displaySubTotal = $parsedSubTotal > 0
        ? $parsedSubTotal
        : (float) ((($purchase->sub_total ?? 0) > 0 ? $purchase->sub_total : null)
            ?? (($purchase->amount ?? 0) > 0 ? $purchase->amount : null)
            ?? ($rawJson['amount'] ?? $rawJson['taxable_value'] ?? 0));

    $displayCgstTotal = $parsedCgstTotal > 0
        ? $parsedCgstTotal
        : (float) ((($purchase->cgst_total ?? 0) > 0 ? $purchase->cgst_total : null)
            ?? 0);

    $displaySgstTotal = $parsedSgstTotal > 0
        ? $parsedSgstTotal
        : (float) ((($purchase->sgst_total ?? 0) > 0 ? $purchase->sgst_total : null)
            ?? 0);

    if ($displayCgstTotal <= 0 && $displaySgstTotal <= 0) {
        $rawTax = (float) ($rawJson['tax'] ?? (($purchase->tax_amount ?? 0) > 0 ? $purchase->tax_amount : 0));
        if ($rawTax > 0) {
            $displayCgstTotal = round($rawTax / 2, 2);
            $displaySgstTotal = round($rawTax / 2, 2);
        }
    }

    $displayGrandTotal = $parsedGrandTotal > 0
        ? $parsedGrandTotal
        : (float) ((($purchase->total_amount ?? 0) > 0 ? $purchase->total_amount : null)
            ?? (($purchase->grand_total ?? 0) > 0 ? $purchase->grand_total : null)
            ?? ($rawJson['total'] ?? $rawJson['grand_total'] ?? 0));

    if ($displayGrandTotal <= 0 && $displaySubTotal > 0) {
        $displayGrandTotal = $displaySubTotal + $displayCgstTotal + $displaySgstTotal;
    }

    $displayVendorName = trim((string) (
        $parsedPdfData['vendor_name']
        ?? $rawJson['vendor_name']
        ?? $purchase->vendor_name_raw
        ?? $purchase->vendor_name
        ?? optional($purchase->vendor)->vendor_name
        ?? ''
    ));

    $displayGstin = strtoupper(trim((string) (
        $parsedPdfData['gstin']
        ?? $purchase->gstin
        ?? $purchase->vendor_gstin
        ?? $purchase->gst_number
        ?? ($rawJson['gstin'] ?? $rawJson['gst'] ?? '')
    )));

    $storedInvoiceNo = trim((string) ($purchase->invoice_no ?? ''));
    $parsedInvoiceNo = trim((string) ($parsedPdfData['invoice_no'] ?? ''));
    $rawInvoiceNo = trim((string) ($rawJson['invoice_number'] ?? ''));

    // If stored value is generated fallback key, prefer parsed/raw real invoice number.
    if ($storedInvoiceNo !== '' && str_starts_with(strtoupper($storedInvoiceNo), 'GMAIL-')) {
        $displayInvoiceNo = $parsedInvoiceNo !== '' ? $parsedInvoiceNo : ($rawInvoiceNo !== '' ? $rawInvoiceNo : $storedInvoiceNo);
    } else {
        $displayInvoiceNo = $storedInvoiceNo !== '' ? $storedInvoiceNo : ($rawInvoiceNo !== '' ? $rawInvoiceNo : $parsedInvoiceNo);
    }

    $displayInvoiceDate = $parsedPdfData['invoice_date']
        ?? ($rawJson['invoice_date'] ?? null)
        ?? ($purchase->invoice_date ? \Carbon\Carbon::parse($purchase->invoice_date)->format('Y-m-d') : '');

    if ($displayInvoiceDate) {
        try {
            $displayInvoiceDate = \Carbon\Carbon::parse($displayInvoiceDate)->format('Y-m-d');
        } catch (\Throwable $e) {
            $displayInvoiceDate = '';
        }
    }

    $displayAccuracy = $purchase->confidence_score;
    if ((is_null($displayAccuracy) || (float) $displayAccuracy <= 0) && is_array($purchase->raw_json)) {
        $displayAccuracy = data_get($purchase->raw_json, 'matching.combined_confidence')
            ?? data_get($purchase->raw_json, 'reprocessed.combined_confidence');
    }

    $displayStatus = (string) ($purchase->status ?? 'pending');
    $valueChangeHistory = [];
    if (is_array($purchase->raw_json)) {
        $valueChangeHistory = data_get($purchase->raw_json, 'value_changes', []);
        if (!is_array($valueChangeHistory)) {
            $valueChangeHistory = [];
        }
        $valueChangeHistory = array_reverse($valueChangeHistory);
    }

    if (($displaySubTotal <= 0 || $displayGrandTotal <= 0) && !empty($prefillRows)) {
        $displaySubTotal = array_sum(array_map(fn ($row) => (float) ($row['quantity'] ?? 0) * (float) ($row['price'] ?? 0), $prefillRows));
        $displayCgstTotal = 0;
        $displaySgstTotal = 0;

        foreach ($prefillRows as $row) {
            $taxable = (float) ($row['quantity'] ?? 0) * (float) ($row['price'] ?? 0);
            $displayCgstTotal += ($taxable * (float) ($row['cgst_percent'] ?? 0)) / 100;
            $displaySgstTotal += ($taxable * (float) ($row['sgst_percent'] ?? 0)) / 100;
        }

        $displayGrandTotal = $displaySubTotal + $displayCgstTotal + $displaySgstTotal;
    }

    if (empty($prefillRows) && $displaySubTotal > 0) {
        $cgstPct = round(($displayCgstTotal / $displaySubTotal) * 100, 2);
        $sgstPct = round(($displaySgstTotal / $displaySubTotal) * 100, 2);
        $prefillRows = [[
            'item_label'   => 'Invoice Services',
            'item_id'      => null,
            'hsn'          => '',
            'quantity'     => 1,
            'price'        => $displaySubTotal,
            'cgst_percent' => $cgstPct,
            'sgst_percent' => $sgstPct,
        ]];
    }

    return compact(
        'parsedPdfData',
        'prefillRows',
        'displayVendorName',
        'displayGstin',
        'displayInvoiceNo',
        'displayInvoiceDate',
        'displayAccuracy',
        'displayStatus',
        'displaySubTotal',
        'displayCgstTotal',
        'displaySgstTotal',
        'displayGrandTotal',
        'valueChangeHistory'
    );
}

private function parseSourcePdfForEdit(PurchaseInvoice $purchase): array
{
    $pdfPath = $this->resolveSourcePdfAbsolutePath($purchase);
    if (!$pdfPath || !is_file($pdfPath)) {
        return [];
    }

    $text = $this->extractTextFromInvoiceFile($pdfPath);

    if (trim($text) === '') {
        return [];
    }

    $normalized = preg_replace('/\s+/', ' ', $text) ?? '';
    if ($normalized === '') {
        return [];
    }

    $invoiceNo = null;
    if (preg_match('/Invoice\s*No\.?\s+Invoice\s*Date\s*\R+\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})\s+[0-9]{1,2}[\/\-.][0-9]{1,2}[\/\-.][0-9]{2,4}/i', $text, $m)) {
        $candidate = strtoupper(trim((string) ($m[1] ?? ''), " \t\n\r\0\x0B:.-_"));
        if ($candidate !== '' && preg_match('/\d/', $candidate)) {
            $invoiceNo = $candidate;
        }
    }

    $invoiceNoPatterns = [
        '/\b(?:tax\s+)?invoice\s*(?:no\.?|number|#)?\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})/i',
        '/\bbill\s*(?:no\.?|number|#)\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})/i',
        '/\b(?:ref(?:erence)?\s*no\.?|document\s*no\.?)\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})/i',
        '/\b(INV[\-\/]?[A-Z0-9\-\/]{2,30})\b/i',
    ];
    if ($invoiceNo === null) {
        foreach ($invoiceNoPatterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $candidate = strtoupper(trim((string) ($m[1] ?? ''), " \t\n\r\0\x0B:.-_"));
                if ($candidate !== '' && preg_match('/\d/', $candidate)) {
                    $invoiceNo = $candidate;
                    break;
                }
            }
        }
    }

    $invoiceDate = null;
    if (preg_match('/Invoice\s*No\.?\s+Invoice\s*Date\s*\R+\s*[A-Z0-9][A-Z0-9\-\/\.]{2,40}\s+([0-9]{1,2}[\/\-.][0-9]{1,2}[\/\-.][0-9]{2,4})/i', $text, $m)) {
        try {
            $invoiceDate = \Carbon\Carbon::parse(trim((string) ($m[1] ?? '')))->toDateString();
        } catch (\Throwable $e) {
            $invoiceDate = null;
        }
    }

    $invoiceDatePatterns = [
        '/\b(?:invoice\s*date|bill\s*date|date\s*of\s*invoice|dated)\b\s*[:\-]?\s*(\d{1,2}[\/\-.]\d{1,2}[\/\-.]\d{2,4})/i',
        '/\b(?:invoice\s*date|bill\s*date|date)\b\s*[:\-]?\s*(\d{1,2}\s+[A-Za-z]{3,9}\s+\d{2,4})/i',
        '/\b(\d{4}[\/-]\d{1,2}[\/-]\d{1,2})\b/',
    ];
    if ($invoiceDate === null) {
        foreach ($invoiceDatePatterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                try {
                    $invoiceDate = \Carbon\Carbon::parse(trim((string) ($m[1] ?? '')))->toDateString();
                    break;
                } catch (\Throwable $e) {
                    $invoiceDate = null;
                }
            }
        }
    }

    $vendorName = null;
    $vendorPatterns = [
        '/\bTAX\s*INVOICE\b\s*([A-Z][A-Z0-9&.,()\-\/\s]{3,140}?)(?=\s+\d|\s+Phone|\s+Email|\s+GSTIN)/mi',
        '/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To)/mi',
        '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|From|M\/?S\.?|Messrs)\b\s*[:\-]?\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+Invoice|\s+Date)/mi',
    ];
    foreach ($vendorPatterns as $pattern) {
        if (preg_match($pattern, $text, $m)) {
            $vendorName = trim((string) ($m[1] ?? ''));
            $vendorName = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email)\b/i', $vendorName)[0] ?? $vendorName;
            $vendorName = trim((string) $vendorName, " \t\n\r\0\x0B:,-");
            if (strlen($vendorName) >= 4) {
                break;
            }
            $vendorName = null;
        }
    }

    $gstin = null;
    if (preg_match('/\b(\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z][A-Z][0-9A-Z])\b/i', strtoupper($text), $m)) {
        $gstin = strtoupper((string) ($m[1] ?? ''));
    }

    $subTotal = $this->extractMoneyValue($normalized, '/\bSub\s*Total\s*([\d,]+\.\d{2})/i');
    if ($subTotal <= 0) {
        $subTotal = $this->extractMoneyValue($normalized, '/\bTaxable\s*Amt\.?\s*([\d,]+\.\d{2})/i');
    }

    $cgstTotal = $this->extractMoneyValue($normalized, '/\bCGST\s*\(?\d*%?\)?\s*([\d,]+\.\d{2})/i');
    $sgstTotal = $this->extractMoneyValue($normalized, '/\bSGST\s*\(?\d*%?\)?\s*([\d,]+\.\d{2})/i');

    $igstPercent = 0;
    $igstAmount = 0;
    if (preg_match('/\bIGST\s*@?\s*(\d+(?:\.\d+)?)\s*%\s*([\d,]+\.\d{2})/i', $normalized, $m)) {
        $igstPercent = (float) ($m[1] ?? 0);
        $igstAmount = (float) str_replace(',', '', (string) ($m[2] ?? 0));
    }
    if (($cgstTotal <= 0 && $sgstTotal <= 0) && $igstAmount > 0) {
        $cgstTotal = round($igstAmount / 2, 2);
        $sgstTotal = round($igstAmount / 2, 2);
    }

    $grandTotal = $this->extractMoneyValue($normalized, '/\bBalance\s*Due\s*₹?\s*([\d,]+\.\d{2})/i');
    if ($grandTotal <= 0) {
        $grandTotal = $this->extractMoneyValue($normalized, '/\bTotal\s*₹?\s*([\d,]+\.\d{2})/i');
    }
    if ($grandTotal <= 0) {
        $grandTotal = $this->extractMoneyValue($normalized, '/\bGrand\s*Total\s*[^0-9]*([\d,]+\.\d{2})/i');
    }

    // Extract PAN from text
    $pan = $this->extractPANFromText($text);

    return [
        'vendor_name' => $vendorName,
        'invoice_no' => $invoiceNo,
        'invoice_date' => $invoiceDate,
        'gstin' => $gstin,
        'pan' => $pan,
        'sub_total' => $subTotal,
        'cgst_total' => $cgstTotal,
        'sgst_total' => $sgstTotal,
        'grand_total' => $grandTotal,
        'items' => $this->extractLineItemsFromText($text),
    ];
}

private function extractLineItemsFromSourcePdf(PurchaseInvoice $purchase): array
{
    $pdfPath = $this->resolveSourcePdfAbsolutePath($purchase);
    if (!$pdfPath || !is_file($pdfPath)) {
        return [];
    }

    $text = $this->extractTextFromInvoiceFile($pdfPath);

    return $this->extractLineItemsFromText($text);
}

private function extractTextFromInvoiceFile(string $filePath): string
{
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    try {
        if (in_array($extension, ['txt', 'log', 'csv'])) {
            return (string) file_get_contents($filePath);
        }

        if (in_array($extension, ['xls', 'xlsx'])) {
            $spreadsheet = IOFactory::load($filePath);
            $textChunks = [];

            foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                $rows = $sheet->toArray(null, true, true, true);
                foreach ($rows as $row) {
                    $values = array_values(array_filter(array_map(function ($value) {
                        return trim((string) $value);
                    }, $row), fn ($v) => $v !== ''));

                    if (!empty($values)) {
                        $textChunks[] = implode(' ', $values);
                    }
                }
            }

            return implode("\n", $textChunks);
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return (string) $pdf->getText();
    } catch (\Throwable $e) {
        return '';
    }
}

private function extractLineItemsFromText(string $text): array
{
    if (trim($text) === '') {
        return [];
    }

    $normalized = preg_replace('/\s+/', ' ', $text) ?? '';
    if ($normalized === '') {
        return [];
    }

    $invoiceIgstPercent = 0;
    if (preg_match('/\bIGST\s*@?\s*(\d+(?:\.\d+)?)\s*%/i', $normalized, $igstMatch)) {
        $invoiceIgstPercent = (float) ($igstMatch[1] ?? 0);
    }

    $itemSection = $normalized;
    foreach (['#Item & Description', 'Item & Description', 'S.No. Description of Goods', 'Description of Goods', 'Particulars', 'Items', 'Item'] as $marker) {
        $markerPos = stripos($normalized, $marker);
        if ($markerPos !== false) {
            $itemSection = substr($normalized, $markerPos + strlen($marker));
            break;
        }
    }

    $patterns = [
        '/\b\d+\s*(.+?)\s+(\d{4,8})\s+(\d+(?:\.\d+)?)\s+u?n?t?\s*([\d,]+\.\d{2})\s+(\d+(?:\.\d+)?)%\s+([\d,]+\.\d{2})\s+(\d+(?:\.\d+)?)%\s+([\d,]*\d\.\d{2})\s*([\d,]+\.\d{2})/',
        '/\b\d+\s*(.+?)\s+(\d{4,8})\s+(\d+(?:\.\d+)?)\s+u?n?t?\s*([\d,]+\.\d{2})\s+(\d+(?:\.\d+)?)%\s+([\d,]+\.\d{2})\s+(\d+(?:\.\d+)?)%\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})/',
        '/\b\d+\.?\s*([A-Z0-9&.,()\-\/\s]{3,140}?)\s+(\d{4,8})\s+(\d+(?:\.\d+)?)\s+(?:MONTH|MONTHS|NOS?|UNITS?|UNT|UNIT)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})/i',
    ];

    $matches = [];
    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $itemSection, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            break;
        }
    }

    if (empty($matches)) {
        return $this->extractLineItemsFromSectionLines($text, $invoiceIgstPercent);
    }

    $rows = [];

    foreach ($matches as $match) {
        $description = trim((string) ($match[1] ?? ''));
        $hsn = trim((string) ($match[2] ?? ''));
        $qty = (float) str_replace(',', '', (string) ($match[3] ?? 0));
        $rate = (float) str_replace(',', '', (string) ($match[4] ?? 0));
        $cgstPercent = (float) str_replace(',', '', (string) ($match[5] ?? 0));
        $sgstPercent = (float) str_replace(',', '', (string) ($match[7] ?? 0));

        // For alternate format pattern with no explicit CGST/SGST in line, derive from IGST.
        if (!isset($match[7]) && $invoiceIgstPercent > 0) {
            $cgstPercent = round($invoiceIgstPercent / 2, 2);
            $sgstPercent = round($invoiceIgstPercent / 2, 2);
        }

        if (!isset($match[7]) && $invoiceIgstPercent <= 0 && isset($match[5])) {
            $lineAmount = (float) str_replace(',', '', (string) ($match[5] ?? 0));
            if ($qty > 0 && $rate > 0 && $lineAmount > 0) {
                $derivedIgst = ($lineAmount > 0 && ($qty * $rate) > 0)
                    ? ((($lineAmount - ($qty * $rate)) / ($qty * $rate)) * 100)
                    : 0;
                if ($derivedIgst > 0) {
                    $cgstPercent = round($derivedIgst / 2, 2);
                    $sgstPercent = round($derivedIgst / 2, 2);
                }
            }
        }

        if ($qty <= 0 && $rate <= 0) {
            continue;
        }

        $itemName = $description !== '' ? $description : 'Invoice Item';
        $matchedItem = $this->createItemFromParsedRow($itemName, $hsn, $rate);

        if (!$matchedItem) {
            continue;
        }

        $rows[] = [
            'item_id' => $matchedItem->id,
            'item_label' => $itemName,
            'hsn' => $hsn !== '' ? $hsn : ($matchedItem->hsn_sac_code ?? ''),
            'quantity' => $qty > 0 ? $qty : 1,
            'price' => round($rate, 2),
            'cgst_percent' => $cgstPercent,
            'sgst_percent' => $sgstPercent,
        ];
    }

    return $rows;
}

private function extractLineItemsFromSectionLines(string $text, float $invoiceIgstPercent = 0): array
{
    $lines = preg_split('/\R+/', $text) ?: [];
    $lines = array_values(array_filter(array_map(function ($line) {
        return trim((string) $line);
    }, $lines), fn ($line) => $line !== ''));

    if (empty($lines)) {
        return [];
    }

    $headerIndex = null;
    foreach ($lines as $index => $line) {
        if (preg_match('/^(item\s*&\s*description|description\s+of\s+goods|particulars|items?)\b/i', $line)) {
            $headerIndex = $index;
            break;
        }
    }

    if ($headerIndex === null) {
        return [];
    }

    $rows = [];
    $descriptionParts = [];

    for ($i = $headerIndex + 1; $i < count($lines); $i++) {
        $line = trim($lines[$i]);

        if ($line === '' || preg_match('/^(total|total\s+invoice\s+value|grand\s+total)/i', $line)) {
            if (!empty($rows)) {
                break;
            }
            continue;
        }

        if (preg_match('/^(?:rate\s+amount|sac\s+taxable\s+value|cgst|sgst|igst)/i', $line)) {
            continue;
        }

        if (preg_match('/^(\d{4,8})\s+([\d,]+\.\d{2})\s+(\d+(?:\.\d+)?)%\s+([\d,]+\.\d{2})\s+(\d+(?:\.\d+)?)%\s+([\d,]+\.\d{2})\s+%?\s*([\d,]+\.\d{2})$/i', $line, $match)) {
            $description = trim(implode(' ', $descriptionParts));
            $descriptionParts = [];

            if ($description === '') {
                $description = 'Invoice Item';
            }

            $hsn = trim((string) ($match[1] ?? ''));
            $taxableValue = (float) str_replace(',', '', (string) ($match[2] ?? 0));
            $cgstPercent = (float) ($match[3] ?? 0);
            $sgstPercent = (float) ($match[5] ?? 0);
            $igstAmount = (float) str_replace(',', '', (string) ($match[7] ?? 0));

            if ($cgstPercent <= 0 && $sgstPercent <= 0 && $invoiceIgstPercent > 0) {
                $cgstPercent = round($invoiceIgstPercent / 2, 2);
                $sgstPercent = round($invoiceIgstPercent / 2, 2);
            }

            if ($cgstPercent <= 0 && $sgstPercent <= 0 && $igstAmount > 0 && $taxableValue > 0) {
                $derivedIgstPercent = ($igstAmount / $taxableValue) * 100;
                $cgstPercent = round($derivedIgstPercent / 2, 2);
                $sgstPercent = round($derivedIgstPercent / 2, 2);
            }

            $matchedItem = $this->createItemFromParsedRow($description, $hsn, $taxableValue);

            if (!$matchedItem) {
                continue;
            }

            $rows[] = [
                'item_id' => $matchedItem->id,
                'item_label' => $description,
                'hsn' => $hsn !== '' ? $hsn : ($matchedItem->hsn_sac_code ?? ''),
                'quantity' => 1,
                'price' => round($taxableValue, 2),
                'cgst_percent' => $cgstPercent,
                'sgst_percent' => $sgstPercent,
            ];

            continue;
        }

        $descriptionParts[] = $line;
    }

    return $rows;
}

private function extractMoneyValue(string $text, string $pattern): float
{
    if (preg_match($pattern, $text, $m)) {
        return (float) str_replace(',', '', (string) ($m[1] ?? 0));
    }

    return 0;
}

private function resolveSourcePdfAbsolutePath(PurchaseInvoice $purchase): ?string
{
    $candidatePaths = [];

    if (!empty($purchase->po_invoice_file)) {
        $candidatePaths[] = (string) $purchase->po_invoice_file;

        if (!str_contains((string) $purchase->po_invoice_file, '/')) {
            $candidatePaths[] = 'images/poinvoice_files/' . $purchase->po_invoice_file;
        }
    }

    if (!empty($purchase->email_log_id)) {
        $emailLogPath = EmailLog::query()->where('id', $purchase->email_log_id)->value('attachment_path');
        if (!empty($emailLogPath)) {
            $candidatePaths[] = (string) $emailLogPath;
        }
    }

    $candidatePaths = array_values(array_unique(array_filter($candidatePaths)));

    foreach ($candidatePaths as $path) {
        $publicPath = public_path(ltrim($path, '/'));
        if (is_file($publicPath)) {
            return $publicPath;
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }
    }

    return null;
}

private function createItemFromParsedRow(string $description, string $hsn, float $rate): ?Items
{
    $name = trim($description);
    if ($name === '') {
        return null;
    }

    $name = substr($name, 0, 190);

    $existing = Items::query()
        ->whereRaw('LOWER(item_name) = ?', [strtolower($name)])
        ->orWhereRaw('LOWER(item_description) = ?', [strtolower($name)])
        ->first();
    if ($existing) {
        return $existing;
    }

    return Items::create([
        'item_name' => $name,
        'item_description' => $description,
        'item_rate' => $rate > 0 ? $rate : 0,
        'hsn_sac_code' => $hsn !== '' ? (int) $hsn : null,
        'usage_unit' => 'Nos',
        'status' => 'Active',
    ]);
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
        'vendor_id' => 'nullable|exists:vendors,id',
        'vendor_name_raw' => 'required|string|max:255',
        'invoice_number' => 'required',
        'invoice_date' => 'nullable|date',
        'total_amount' => 'required|numeric',
        'sub_total' => 'nullable|numeric',
        'cgst_total' => 'nullable|numeric',
        'sgst_total' => 'nullable|numeric',
        'tax_amount' => 'nullable|numeric',
        'grand_total' => 'nullable|numeric',
        'items' => 'nullable|array',
        'items.*.item_id' => 'nullable|exists:items,id',
        'items.*.item_name' => 'required_with:items|string|max:255',
        'items.*.quantity' => 'required_with:items|numeric|min:0.01',
        'items.*.price' => 'required_with:items|numeric|min:0',
        'po_invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
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
        'vendor_id' => $request->vendor_id ?? $purchase->vendor_id,
        'vendor_name_raw' => trim((string) $request->vendor_name_raw),
        'vendor_name' => trim((string) $request->vendor_name_raw),
        'deliverable_id' => $request->deliverable_id,
        'invoice_no' => $request->invoice_number,
        'invoice_date' => $request->invoice_date,
        'sub_total' => $request->sub_total ?? 0,
        'cgst_total' => $request->cgst_total ?? 0,
        'sgst_total' => $request->sgst_total ?? 0,
        'tax_amount' => $request->tax_amount ?? (($request->cgst_total ?? 0) + ($request->sgst_total ?? 0)),
        'grand_total' => $request->grand_total ?? $request->total_amount,
        'total_amount' => $request->total_amount,
        'po_invoice_file' => $fileName,
        'status' => $status,
        'updated_by' => Auth::id(),
    ]);

    // Clear import failure reason when user manually updates the record
    $rawJson = $purchase->raw_json ?? [];
    if (isset($rawJson['import_failure_reason'])) {
        unset($rawJson['import_failure_reason']);
        $purchase->raw_json = $rawJson;
        $purchase->save();
    }

    if (is_array($request->items)) {
        $itemsData = [];

        foreach ($request->items as $item) {
            $itemId = $item['item_id'] ?? null;
            $itemName = trim((string) ($item['item_name'] ?? ''));

            if (empty($itemId) && $itemName !== '') {
                $matchedItem = Items::query()
                    ->whereRaw('LOWER(item_name) = ?', [strtolower($itemName)])
                    ->orWhereRaw('LOWER(item_description) = ?', [strtolower($itemName)])
                    ->first();

                if (!$matchedItem) {
                    $matchedItem = $this->createItemFromParsedRow($itemName, '', (float) ($item['price'] ?? 0));
                }

                $itemId = $matchedItem?->id;
            }

            if (empty($itemId)) {
                continue;
            }

            $qty = (float) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);

            $itemsData[] = [
                'item_id' => $itemId,
                'quantity' => $qty,
                'price' => $price,
                'total' => $qty * $price,
            ];
        }

        $purchase->items()->delete();

        if (!empty($itemsData)) {
            $purchase->items()->createMany($itemsData);
        }
    }

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
        // $purchase->delete();
        $purchase->status = 'Inactive';
        $purchase->deleted_by = Auth::id(); // store user id
        $purchase->save();
        return back()->with('success', 'Purchase Deleted');
    }

    // ─── Auto Invoice Processing (finance/purchase-invoices) ───

    public function autoInvoiceIndex(Request $request)
    {
        $status  = $request->input('status');
        $mailReadDays = $this->getInvoiceMailReadDays();
        $this->autoFetchGmailInvoices($mailReadDays);

        // Auto Invoice Processing shows invoices imported from email that are being processed
        $query   = PurchaseInvoice::with(['vendor', 'emailLog'])
            ->whereNotNull('email_log_id')
            ->latest();

        if ($status === 'failed') {
            // Failed tab should include explicit failed status AND records carrying failure details.
            $invoices = $query->get()
                ->filter(fn (PurchaseInvoice $invoice) => $this->isFailedAutoInvoice($invoice))
                ->values();
        } elseif ($status) {
            $query->where('status', $status);
            $invoices = $query->get();
        } else {
            // By default, show draft, needs_review, verified status (invoices being processed)
            // Hide approved, paid, failed from default auto-processing list.
            $invoices = $query
                ->whereIn('status', ['draft', 'needs_review', 'verified'])
                ->get();
        }

        $latestImportedMailAt = EmailLog::latest()->value('created_at');
        $lastManualFetchAt = Cache::get($this->lastManualFetchCacheKey());

        $lastMailReadAt = $latestImportedMailAt;
        if (!empty($lastManualFetchAt) && (empty($lastMailReadAt) || strtotime((string) $lastManualFetchAt) > strtotime((string) $lastMailReadAt))) {
            $lastMailReadAt = $lastManualFetchAt;
        }

        $lastFetchStatus = Cache::get($this->lastFetchStatusCacheKey());

        return view('finance.purchase_invoices.index', compact('invoices', 'lastMailReadAt', 'lastFetchStatus'));
    }

    private function isFailedAutoInvoice(PurchaseInvoice $invoice): bool
    {
        $status = strtolower(trim((string) ($invoice->status ?? '')));
        if ($status === 'failed') {
            return true;
        }

        $raw = is_array($invoice->raw_json) ? $invoice->raw_json : [];
        $failureReason = trim((string) (
            data_get($raw, 'import_failure_reason')
            ?? data_get($raw, 'parse_error')
            ?? data_get($raw, 'error')
            ?? ''
        ));

        if ($failureReason !== '') {
            return true;
        }

        $emailLogStatus = strtolower(trim((string) optional($invoice->emailLog)->status));
        $emailLogError = trim((string) optional($invoice->emailLog)->error_message);

        return $emailLogStatus === 'failed' || $emailLogError !== '';
    }

    public function showAutoInvoice($id)
    {
        $invoice = PurchaseInvoice::with('vendor')->findOrFail($id);
        $raw     = $invoice->raw_json ?? [];
        return view('finance.purchase_invoices.show', compact('invoice', 'raw'));
    }

    public function editAutoInvoice($id)
    {
        $invoice = PurchaseInvoice::with('vendor')->findOrFail($id);
        $vendors = Vendor::orderBy('vendor_name')->get();
        $raw     = $invoice->raw_json ?? [];
        return view('finance.purchase_invoices.edit', compact('invoice', 'vendors', 'raw'));
    }

    public function updateAutoInvoice(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        $request->validate([
            'vendor_name'  => 'nullable|string|max:255',
            'gstin'        => ['nullable', 'string', 'regex:/^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/'],
            'invoice_no'   => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'amount'       => 'nullable|numeric|min:0',
            'tax_amount'   => 'nullable|numeric|min:0',
            'cgst_total'   => 'nullable|numeric|min:0',
            'sgst_total'   => 'nullable|numeric|min:0',
            'grand_total'  => 'nullable|numeric|min:0',
            'vendor_id'    => 'nullable|exists:vendors,id',
            'notes'        => 'nullable|string|max:1000',
            'due_date'     => 'nullable|date',
        ]);

        // Enhanced vendor lookup with PAN and GSTIN
        $vendorId = $request->vendor_id ?: null;
        $gstin = $request->gstin ? strtoupper(trim($request->gstin)) : null;
        
        // If no vendor selected, try to find by PAN from invoice parsing
        if (!$vendorId && $request->has('raw_json') && $request->raw_json) {
            $rawData = is_array($request->raw_json) ? $request->raw_json : json_decode($request->raw_json, true);
            $pan = $rawData['pan'] ?? null;
            
            if ($pan) {
                $vendor = $this->findVendorByPANAndGSTIN($pan, $gstin);
                if ($vendor) {
                    $vendorId = $vendor->id;
                    $gstin = $gstin ?: $vendor->gstin;
                }
            }
        }

        $invoice->update([
            'vendor_id'    => $vendorId,
            'vendor_name'  => $request->vendor_name,
            'vendor_name_raw' => $request->vendor_name,
            'gstin'        => $gstin,
            'gst_number'   => $gstin,
            'vendor_gstin' => $gstin,
            'invoice_no'   => $request->invoice_no,
            'invoice_date' => $request->invoice_date,
            'due_date'     => $request->due_date ?: null,
            'amount'       => $request->amount ?? 0,
            'tax_amount'   => $request->tax_amount ?? 0,
            'cgst_total'   => $request->cgst_total ?? 0,
            'sgst_total'   => $request->sgst_total ?? 0,
            'grand_total'  => $request->grand_total ?? 0,
            'notes'        => $request->notes,
        ]);

        // After user updates in Auto Invoice Processing, move to Purchases workflow.
        if (in_array(strtolower((string) $invoice->status), ['draft', 'needs_review', 'failed', 'email_imported'], true)) {
            $invoice->update(['status' => 'verified']);
        }

        return redirect()
            ->route('finance.purchase_invoices.show', $id)
            ->with('success', 'Invoice details updated successfully.');
    }

    public function approve($id)
    {
        $purchase = PurchaseInvoice::findOrFail($id);
        $purchase->update(['status' => 'approved']);
        // After approving, send to Auto Invoice Processing (approved tab)
        return redirect()
            ->route('finance.purchase_invoices.index', ['status' => 'approved'])
            ->with('success', 'Auto Invoice Processing approved and moved to Invoice.');
    }

    public function verify($id)
    {
        $purchase = PurchaseInvoice::findOrFail($id);
        $purchase->update(['status' => 'verified']);
        return redirect()
            ->route('finance.purchase_invoices.index', ['status' => 'verified'])
            ->with('success', 'Invoice verified successfully.');
    }

    public function markPaid($id)
    {
        $purchase = PurchaseInvoice::findOrFail($id);
        $purchase->update(['status' => 'paid']);
        return redirect()
            ->route('finance.purchase_invoices.index', ['status' => 'paid'])
            ->with('success', 'Invoice marked as paid.');
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

/**
 * Validate PAN number format
 */
private function validatePANFormat(string $pan): array
{
    $pan = strtoupper(trim($pan));
    
    // PAN format: 5 letters + 4 digits + 1 letter
    if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan)) {
        return [
            'valid' => false,
            'error' => 'Invalid PAN format. Should be 5 letters + 4 digits + 1 letter (e.g., ABCDE1234F)'
        ];
    }
    
    return ['valid' => true, 'pan' => $pan];
}

/**
 * Extract PAN number from invoice text
 */
private function extractPANFromText(string $text): ?string
{
    // PAN patterns in various formats
    $patterns = [
        '/\bPAN\s*[:\-]?\s*([A-Z]{5}[0-9]{4}[A-Z]{1})\b/i',
        '/\bPermanent\s*Account\s*Number\s*[:\-]?\s*([A-Z]{5}[0-9]{4}[A-Z]{1})\b/i',
        '/\b(?:P\.?A\.?N\.?|Pan)\s*[:\-]?\s*([A-Z]{5}[0-9]{4}[A-Z]{1})\b/i',
        '/\([A-Z]{5}[0-9]{4}[A-Z]{1}\)/i',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            return strtoupper(trim($matches[1]));
        }
    }

    return null;
}

/**
 * Find vendor by PAN number
 */
private function findVendorByPAN(string $pan): ?Vendor
{
    return Vendor::where('pan_no', $pan)
        ->orWhereHas('gstins', function ($query) use ($pan) {
            // Extract PAN from GSTIN (5th to 9th characters)
            $query->whereRaw('SUBSTRING(UPPER(gstin), 5, 5) = ?', [substr($pan, 0, 5)]);
        })
        ->first();
}

/**
 * Verify PAN with Surepass API
 */
private function verifyPANWithSurepass(string $pan): array
{
    try {
        $surepassService = new SurepassService();
        $response = $surepassService->verifyPAN($pan);
        
        if ($response['success'] ?? false) {
            return [
                'valid' => true,
                'verified' => true,
                'data' => $response['data'] ?? []
            ];
        }
        
        return [
            'valid' => false,
            'verified' => false,
            'error' => $response['message'] ?? 'PAN verification failed'
        ];
        
    } catch (\Exception $e) {
        Log::error('PAN verification failed', [
            'pan' => $pan,
            'error' => $e->getMessage()
        ]);
        
        return [
            'valid' => false,
            'verified' => false,
            'error' => 'Verification service unavailable'
        ];
    }
}

/**
 * API endpoint to validate PAN
 */
public function validatePAN(Request $request)
{
    $request->validate([
        'pan' => 'required|string|max:10'
    ]);

    $pan = $request->input('pan');
    $validation = $this->validatePANFormat($pan);
    
    if (!$validation['valid']) {
        return response()->json([
            'success' => false,
            'error' => $validation['error']
        ]);
    }

    // Try to find vendor by PAN
    $vendor = $this->findVendorByPAN($validation['pan']);
    
    return response()->json([
        'success' => true,
        'pan' => $validation['pan'],
        'vendor_exists' => $vendor ? true : false,
        'vendor' => $vendor ? [
            'id' => $vendor->id,
            'name' => $vendor->vendor_name,
            'gstin' => $vendor->gstin
        ] : null
    ]);
}

/**
 * API endpoint to verify PAN with Surepass
 */
public function verifyPAN(Request $request)
{
    $request->validate([
        'pan' => 'required|string|max:10'
    ]);

    $pan = $request->input('pan');
    $validation = $this->validatePANFormat($pan);
    
    if (!$validation['valid']) {
        return response()->json([
            'success' => false,
            'error' => $validation['error']
        ]);
    }

    $verification = $this->verifyPANWithSurepass($validation['pan']);
    
    return response()->json($verification);
}

/**
 * Enhanced vendor lookup with PAN and GSTIN
 */
private function findVendorByPANAndGSTIN(string $pan, string $gstin): ?Vendor
{
    // First try direct PAN match
    $vendor = Vendor::where('pan_no', $pan)->first();
    if ($vendor) {
        return $vendor;
    }
    
    // Try GSTIN match
    if ($gstin) {
        $vendor = Vendor::where('gstin', $gstin)->first();
        if ($vendor) {
            return $vendor;
        }
    }
    
    // Try PAN extracted from GSTIN
    if (strlen($gstin) >= 15) {
        $panFromGSTIN = substr($gstin, 2, 10);
        $vendor = Vendor::where('pan_no', $panFromGSTIN)->first();
        if ($vendor) {
            return $vendor;
        }
    }
    
    return null;
}

public function downloadSourcePdf($id)
{
    $purchase = PurchaseInvoice::findOrFail($id);

    $candidatePaths = [];

    if (!empty($purchase->po_invoice_file)) {
        $candidatePaths[] = (string) $purchase->po_invoice_file;

        // Some records store only filename; normalize to default public image folder.
        if (!str_contains((string) $purchase->po_invoice_file, '/')) {
            $candidatePaths[] = 'images/poinvoice_files/' . $purchase->po_invoice_file;
        }
    }

    if (!empty($purchase->email_log_id)) {
        $emailLogPath = EmailLog::query()->where('id', $purchase->email_log_id)->value('attachment_path');
        if (!empty($emailLogPath)) {
            $candidatePaths[] = (string) $emailLogPath;
        }
    }

    $candidatePaths = array_values(array_unique(array_filter($candidatePaths)));

    foreach ($candidatePaths as $path) {
        $publicFile = public_path(ltrim($path, '/'));
        if (is_file($publicFile)) {
            return response()->download($publicFile);
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->download($path);
        }
    }

    return back()->with('error', 'Source PDF not found for this invoice.');
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
    $gst = $this->extractGSTNumber($text);
    $invoiceNumber = $this->extractInvoiceNumber($text);

    return response()->json([
        'arc' => $arc,
        'otc' => $otc,
        'static' => $static,
        'router' => $router,
        'gst' => $gst,
        'invoice_number' => $invoiceNumber,
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

// 🔧 Extract GST Number (15-digit format)
private function extractGSTNumber($text)
{
    // GST Pattern: 2 digits (state) + 5 letters (PAN) + 4 digits + 1 letter + 1 checksum + 1 check digit
    $pattern = '/\b\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/';
    
    preg_match($pattern, $text, $matches);

    if (isset($matches[0])) {
        return strtoupper($matches[0]);
    }

    return null;
}

// 📧 Read PDF from Email and Auto-Create Invoice
public function createInvoiceFromEmailPDF(Request $request)
{
    $request->validate([
        'email_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // PDF or image from email
        'deliverable_id' => 'nullable|exists:deliverables,id',
    ]);

    if (!$request->hasFile('email_file')) {
        return response()->json(['error' => 'No file attached'], 400);
    }

    $file = $request->file('email_file');
    
    // ✅ Step 1: Parse Invoice (Extract text, amounts, GST)
    $parseResponse = $this->parseInvoicePDF($file);
    
    if (isset($parseResponse['error'])) {
        return response()->json($parseResponse, 400);
    }

    $extractedData = $parseResponse;
    
    // ✅ Step 2: Find Vendor by GST Number
    $vendor = null;
    if ($extractedData['gst']) {
        $vendor = Vendor::where('gst_number', $extractedData['gst'])->first();
    }

    if (!$vendor) {
        return response()->json([
            'error' => 'VENDOR_NOT_FOUND',
            'message' => 'Could not find vendor with GST: ' . ($extractedData['gst'] ?? 'Not found'),
            'extracted_data' => $extractedData
        ], 404);
    }

    // ✅ Step 3: Auto-Create Purchase Invoice
    $fileName = $this->saveUploadedFile($file);

    $invoice = PurchaseInvoice::create([
        'type' => 'purchase',
        'vendor_id' => $vendor->id,
        'deliverable_id' => $request->deliverable_id,
        'invoice_no' => $extractedData['invoice_number'] ?? 'AUTO-' . time(),
        'invoice_date' => now(),
        'total_amount' => $extractedData['arc'] + $extractedData['otc'] + $extractedData['static'],
        'po_invoice_file' => $fileName,
        'status' => 'email_imported', // Mark as email-imported
        'arc_amount' => $extractedData['arc'],
        'otc_amount' => $extractedData['otc'],
        'static_amount' => $extractedData['static'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Invoice created from email PDF',
        'invoice_id' => $invoice->id,
        'vendor_name' => $vendor->name,
        'extracted_data' => $extractedData
    ], 201);
}

// 🔧 Parse Invoice PDF (Used for Email & Manual Upload)
private function parseInvoicePDF($file)
{
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
        return [
            'error' => 'OCR failed',
            'message' => $result['ErrorMessage'] ?? 'Unknown error'
        ];
    }

    // ✅ Extract text
    $text = $result['ParsedResults'][0]['ParsedText'] ?? '';

    if (!$text) {
        return ['error' => 'Parsing failed'];
    }

    // 🔍 Extract values
    $arc = $this->extractAmount($text, 'ARC');
    $otc = $this->extractAmount($text, 'OTC');
    $static = $this->extractAmount($text, 'Static');
    $router = $this->extractAmount($text, 'Router');
    $gst = $this->extractGSTNumber($text);
    $invoiceNumber = $this->extractInvoiceNumber($text);

    return [
        'arc' => $arc,
        'otc' => $otc,
        'static' => $static,
        'router' => $router,
        'gst' => $gst,
        'invoice_number' => $invoiceNumber,
    ];
}

// 🔧 Extract Invoice Number from PDF
private function extractInvoiceNumber($text)
{
    // Try common patterns: INV-123, INV/123, Invoice No. 123, etc.
    $patterns = [
        '/[Ii]nvoice\s+(?:[Nn]o\.?|[Nn]umber)?\s*:?\s*([A-Z0-9\-\/]+)/i',
        '/[Ii]nv[oice]*\s*[#-]?\s*([A-Z0-9\-\/]+)/i',
        '/^([A-Z0-9\-]{5,20})$/im',
    ];

    foreach ($patterns as $pattern) {
        preg_match($pattern, $text, $matches);
        if (isset($matches[1])) {
            return trim($matches[1]);
        }
    }

    return null;
}

// 🔧 Save Uploaded File
private function saveUploadedFile($file)
{
    $filename = time() . '_' . $file->getClientOriginalName();
    $file->move(public_path("images/poinvoice_files"), $filename);
    return $filename;
}

// ✅ Manual Gmail IMAP Fetch Trigger (called from UI button)
public function fetchGmail(Request $request)
{
    set_time_limit(300);
    try {
        $mailReadDays = $this->getInvoiceMailReadDays();

        $exitCode = Artisan::call('invoice:fetch-gmail', [
            '--recent' => true,
            '--days' => $mailReadDays,
        ]);
        $output   = trim(Artisan::output());
        $created  = substr_count($output, 'Purchase Invoice created');
        $this->cacheFetchStatus($exitCode, $output, $mailReadDays);

        if ($exitCode === 0) {
            Cache::put($this->lastManualFetchCacheKey(), now()->toDateTimeString(), now()->addDays(30));
        }

        if ($exitCode !== 0) {
            return redirect()->route('finance.purchase_invoices.index')
                ->with('error', 'Gmail fetch failed. Please check the fetch status shown on this page.');
        }

        return redirect()->route('finance.purchase_invoices.index')
            ->with('success', 'Gmail check complete (last ' . $mailReadDays . ' days). ' . $created . ' new invoice(s) imported.');
    } catch (\Exception $e) {
        return redirect()->route('finance.purchase_invoices.index')
            ->with('error', 'Gmail fetch failed: ' . $e->getMessage());
    }
}

/**
 * Test Email Connection for Invoice Import
 */
public function testInvoiceEmailConnection(Request $request)
{
    if (!function_exists('imap_open')) {
        return response()->json([
            'success' => false,
            'message' => 'PHP IMAP extension not enabled. Enable php_imap in php.ini'
        ], 500);
    }

    try {
        $companySetting = $this->resolveInvoiceCompanySetting();

        $username = trim((string) (
            $companySetting?->invoice_mail_username
            ?: $companySetting?->invoice_mail_from_address
            ?: env('IMAP_USERNAME')
            ?: env('MAIL_USERNAME')
        ));
        $password = (string) (
            $companySetting?->invoice_mail_password
            ?: env('IMAP_PASSWORD')
            ?: env('MAIL_PASSWORD')
        );
        $imapPath = $this->buildImapMailboxPath($companySetting)
            ?: $this->buildImapMailboxPathFromEnv();

        if (!$imapPath || !$username || !$password) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice IMAP host, email account, or password is missing'
            ], 400);
        }

        // Try to connect
        $inbox = @imap_open($imapPath, $username, $password, OP_HALFOPEN);

        if (!$inbox) {
            $error = imap_last_error() ?: 'Unknown IMAP connection error';
            return response()->json([
                'success' => false,
                'message' => 'IMAP Connection Failed: ' . $error
            ], 500);
        }

        // Get mailbox info
        $mailboxInfo = imap_mailboxmsginfo($inbox);
        $emailCount = $mailboxInfo->Nmsgs ?? 0;

        imap_close($inbox);

        return response()->json([
            'success' => true,
            'message' => '✅ Connection successful! Found ' . $emailCount . ' email(s) in mailbox'
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

private function buildImapMailboxPathFromEnv(): ?string
{
    $host = trim((string) (env('IMAP_HOST') ?: env('MAIL_HOST', '')));
    if ($host === '') {
        return null;
    }

    if (str_starts_with($host, '{')) {
        return $host;
    }

    $port = trim((string) (env('IMAP_PORT') ?: env('MAIL_PORT', '993')));
    $encryption = strtolower(trim((string) (env('IMAP_ENCRYPTION') ?: env('MAIL_ENCRYPTION', 'ssl'))));
    $mailbox = trim((string) env('IMAP_MAILBOX', '[Gmail]/All Mail'));

    [$host, $port, $encryption, $mailbox] = $this->normalizeImapServerSettings($host, $port, $encryption, $mailbox);

    $flags = '/imap';

    if ($encryption === 'ssl') {
        $flags .= '/ssl/novalidate-cert';
    } elseif ($encryption === 'tls') {
        $flags .= '/tls/novalidate-cert';
    } else {
        $flags .= '/notls';
    }

    return '{' . $host . ':' . $port . $flags . '}' . $mailbox;
}

private function resolveInvoiceCompanySetting(): ?CompanySetting
{
    $candidate = CompanySetting::query()
        ->orderByDesc('is_default')
        ->orderByDesc('updated_at')
        ->get()
        ->first(function (CompanySetting $setting) {
            return trim((string) ($setting->invoice_mail_host ?? '')) !== ''
                || trim((string) ($setting->invoice_mail_username ?? '')) !== ''
                || trim((string) ($setting->invoice_mail_from_address ?? '')) !== '';
        });

    if ($candidate) {
        return $candidate;
    }

    return CompanySetting::query()
        ->orderByDesc('is_default')
        ->orderByDesc('updated_at')
        ->first();
}

private function buildImapMailboxPath(?CompanySetting $companySetting): ?string
{
    $host = trim((string) ($companySetting?->invoice_mail_host ?? ''));
    if ($host === '') {
        return null;
    }

    if (str_starts_with($host, '{')) {
        return $host;
    }

    $port = trim((string) ($companySetting?->invoice_mail_port ?: '993'));
    $encryption = strtolower(trim((string) ($companySetting?->invoice_mail_encryption ?: 'ssl')));
    $mailbox = 'INBOX';

    [$host, $port, $encryption, $mailbox] = $this->normalizeImapServerSettings($host, $port, $encryption, $mailbox);

    $flags = '/imap';

    if ($encryption === 'ssl') {
        $flags .= '/ssl/novalidate-cert';
    } elseif ($encryption === 'tls') {
        $flags .= '/tls/novalidate-cert';
    } else {
        $flags .= '/notls';
    }

    return '{' . $host . ':' . $port . $flags . '}' . $mailbox;
}

private function normalizeImapServerSettings(string $host, string $port, string $encryption, string $mailbox): array
{
    $normalizedHost = strtolower(trim($host));

    if (str_contains($normalizedHost, 'smtp.gmail.com') || $normalizedHost === 'gmail.com') {
        return ['imap.gmail.com', '993', 'ssl', '[Gmail]/All Mail'];
    }

    if (str_contains($normalizedHost, 'smtp.office365.com') || str_contains($normalizedHost, 'outlook.office365.com')) {
        return ['outlook.office365.com', '993', 'ssl', 'INBOX'];
    }

    if (str_contains($normalizedHost, 'smtp-mail.outlook.com')) {
        return ['imap-mail.outlook.com', '993', 'ssl', 'INBOX'];
    }

    if (str_starts_with($normalizedHost, 'smtp.')) {
        $normalizedHost = 'imap.' . substr($normalizedHost, 5);
    }

    if ($port === '587') {
        $port = '993';
    }

    if ($encryption === 'tls') {
        $encryption = 'ssl';
    }

    return [$normalizedHost, $port ?: '993', $encryption ?: 'ssl', $mailbox];
}

/**
 * Download purchases as Excel
 */
    public function downloadExcel(Request $request)
    {
        $purchases = PurchaseInvoice::with('vendor')->get();
        $records = $purchases->map(function ($purchase) {
            return (object) [
                'vendor_name' => optional($purchase->vendor)->vendor_name ?? $purchase->vendor_name ?? '',
                'vendor_bank_account' => optional($purchase->vendor)->bank_account ?? '',
                'vendor_ifsc' => optional($purchase->vendor)->ifsc_code ?? '',
                'amount' => $purchase->total_amount ?? $purchase->grand_total ?? $purchase->amount ?? '',
                'invoice_no' => $purchase->invoice_no ?? '',
            ];
        });
        return Excel::download(new PurchasesExport($records), 'purchases.xlsx');
    }

}