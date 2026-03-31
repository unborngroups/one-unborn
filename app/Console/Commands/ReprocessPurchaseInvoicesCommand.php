<?php

namespace App\Console\Commands;

use App\Models\CompanySetting;
use App\Models\PurchaseInvoice;
use App\Services\VendorResolverService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser;

class ReprocessPurchaseInvoicesCommand extends Command
{
    protected $signature = 'invoice:reprocess {--days= : Reprocess invoices created in last N days (defaults to Company Settings value)} {--limit=200 : Max records to process} {--dry-run : Preview only, do not update database}';

    protected $description = 'Reprocess imported purchase invoices from saved attachment files and refresh parsed fields';

    public function handle(): int
    {
        $configuredDays = (int) (CompanySetting::query()->value('invoice_mail_read_days') ?? 30);
        $configuredDays = max($configuredDays, 1);

        $daysOption = $this->option('days');
        $days = ($daysOption !== null && $daysOption !== '')
            ? max((int) $daysOption, 1)
            : $configuredDays;

        $limit = max((int) $this->option('limit'), 1);
        $isDryRun = (bool) $this->option('dry-run');

        $fromDate = now()->subDays($days)->startOfDay();

        $invoices = PurchaseInvoice::query()
            ->where('type', 'purchase')
            ->whereNotNull('po_invoice_file')
            ->where('created_at', '>=', $fromDate)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('No purchase invoices found for reprocess window.');
            return 0;
        }

        $resolver = app(VendorResolverService::class);

        $processed = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;

        $this->info('Reprocessing ' . $invoices->count() . ' invoice(s) for last ' . $days . ' day(s)...');

        foreach ($invoices as $invoice) {
            $processed++;

            $filePath = $this->resolveInvoiceFilePath((string) $invoice->po_invoice_file);
            if (!$filePath) {
                $skipped++;
                $this->warn("[{$invoice->id}] File not found: {$invoice->po_invoice_file}");
                continue;
            }

            $parsed = $this->parseInvoice($filePath);
            if (isset($parsed['error'])) {
                $failed++;
                $this->warn("[{$invoice->id}] Parse failed: {$parsed['error']}");
                continue;
            }

            $matchResult = $resolver->resolveMatch([
                'gstin' => $parsed['gst'] ?? null,
                'vendor_name' => $parsed['vendor_name'] ?? $invoice->vendor_name_raw,
            ]);

            $parserConfidence = $this->calculateParserConfidence($parsed);
            $confidence = $this->combineConfidence($parserConfidence, $matchResult['score'] ?? 0);

            $total = $this->determineTotal($parsed, $invoice);
            $invoiceDate = $parsed['invoice_date'] ?? optional($invoice->invoice_date)?->toDateString() ?? now()->toDateString();

            $newInvoiceNo = $this->pickBestInvoiceNo(
                $parsed['invoice_number'] ?? null,
                $this->extractInvoiceNumberFromFilename((string) $invoice->po_invoice_file),
                (string) $invoice->invoice_no
            );

            $newVendorName = $matchResult['vendor']?->vendor_name
                ?? ($parsed['vendor_name'] ?? $invoice->vendor_name_raw ?? $invoice->vendor_name);

            $payload = [
                'vendor_id' => $matchResult['vendor']?->id,
                'vendor_name' => $newVendorName,
                'vendor_name_raw' => $parsed['vendor_name'] ?? $invoice->vendor_name_raw,
                'invoice_no' => $newInvoiceNo,
                'invoice_date' => $invoiceDate,
                'amount' => $total,
                'grand_total' => $total,
                'total_amount' => $total,
                'gstin' => $parsed['gst'] ?? $invoice->gstin,
                'gst_number' => $parsed['gst'] ?? $invoice->gst_number,
                'vendor_gstin' => $parsed['gst'] ?? $invoice->vendor_gstin,
                'arc_amount' => $parsed['arc'] ?? $invoice->arc_amount,
                'otc_amount' => $parsed['otc'] ?? $invoice->otc_amount,
                'static_amount' => $parsed['static'] ?? $invoice->static_amount,
                'confidence_score' => $confidence,
                'raw_json' => $this->buildUpdatedRawJson($invoice->raw_json, $parsed, $matchResult, $parserConfidence, $confidence),
            ];

            if ($isDryRun) {
                $this->line("[{$invoice->id}] DRY-RUN -> invoice_no={$payload['invoice_no']}, gstin={$payload['gstin']}, total={$payload['total_amount']}, confidence={$payload['confidence_score']}");
                $updated++;
                continue;
            }

            $invoice->update($payload);
            $updated++;
            $this->line("[{$invoice->id}] Updated");
        }

        $this->newLine();
        $this->info("Done. Processed={$processed}, Updated={$updated}, Skipped={$skipped}, Failed={$failed}");

        return 0;
    }

    private function resolveInvoiceFilePath(string $fileValue): ?string
    {
        $fileValue = trim($fileValue);
        if ($fileValue === '') {
            return null;
        }

        $candidates = [
            public_path('images/poinvoice_files/' . $fileValue),
            public_path($fileValue),
            storage_path('app/public/' . $fileValue),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function parseViaLocalPdf(string $filePath): array
    {
        try {
            $pdf = (new Parser())->parseFile($filePath);
            $text = $pdf->getText();
            if (!$text) {
                return ['error' => 'NO_TEXT_EXTRACTED'];
            }

            return $this->extractFromText($text);
        } catch (\Throwable $e) {
            Log::warning('invoice:reprocess parse failed', ['file' => $filePath, 'error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    private function parseInvoice(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ['txt', 'log'])) {
            return $this->parseViaPlainText($filePath);
        }

        if (in_array($extension, ['csv', 'xls', 'xlsx'])) {
            return $this->parseViaSpreadsheet($filePath);
        }

        $parsed = $this->parseViaLocalPdf($filePath);
        if (!isset($parsed['error'])) {
            return $parsed;
        }

        // Some non-PDF attachments are saved with a .pdf extension; fallback to plain text parsing.
        return $this->parseViaPlainText($filePath);
    }

    private function parseViaPlainText(string $filePath): array
    {
        try {
            $text = (string) file_get_contents($filePath);
            if (trim($text) === '') {
                return ['error' => 'NO_TEXT_EXTRACTED'];
            }

            return $this->extractFromText($text);
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function parseViaSpreadsheet(string $filePath): array
    {
        try {
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

            $text = trim(implode("\n", $textChunks));
            if ($text === '') {
                return ['error' => 'NO_TEXT_EXTRACTED'];
            }

            return $this->extractFromText($text);
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function extractFromText(string $text): array
    {
        return [
            'gst' => $this->extractGST($text),
            'vendor_name' => $this->extractVendorName($text),
            'invoice_number' => $this->extractInvoiceNumber($text),
            'invoice_date' => $this->extractInvoiceDate($text),
            'arc' => $this->extractAmount($text, 'ARC'),
            'otc' => $this->extractAmount($text, 'OTC'),
            'static' => $this->extractAmount($text, 'Static'),
            'router' => $this->extractAmount($text, 'Router'),
            'total' => $this->extractTotal($text),
        ];
    }

    private function extractGST(string $text): ?string
    {
        preg_match('/\b\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z][A-Z][0-9A-Z]\b/', strtoupper($text), $m);
        return isset($m[0]) ? strtoupper($m[0]) : null;
    }

    private function extractInvoiceNumber(string $text): ?string
    {
        $patterns = [
            '/\b(?:tax\s+)?invoice\s*(?:no\.?|number|#)?\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})/i',
            '/\bbill\s*(?:no\.?|number|#)\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})/i',
            '/\b(?:ref(?:erence)?\s*no\.?|document\s*no\.?)\s*[:\-]?\s*([A-Z0-9][A-Z0-9\-\/\.]{2,40})/i',
            '/\b(INV[\-\/]?[A-Z0-9\-\/]{2,30})\b/i',
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $text, $m)) {
                $candidate = strtoupper(trim((string) $m[1], " \t\n\r\0\x0B:.-"));

                if (strlen($candidate) < 3) {
                    continue;
                }

                if (!preg_match('/\d/', $candidate) && !str_contains($candidate, '-') && !str_contains($candidate, '/')) {
                    continue;
                }

                return $candidate;
            }
        }

        return null;
    }

    private function extractInvoiceNumberFromFilename(string $filename): ?string
    {
        $name = strtoupper(pathinfo($filename, PATHINFO_FILENAME));

        if (preg_match('/\b(INV[\-\/]?[A-Z0-9\-\/]{2,30})\b/', $name, $m)) {
            return $m[1];
        }

        if (preg_match('/\b([A-Z]{1,6}[\-\/]?\d{3,20})\b/', $name, $m)) {
            return $m[1];
        }

        return null;
    }

    private function extractVendorName(string $text): ?string
    {
        $linePatterns = [
            '/\bBill\s*To\b\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Ship\s+To)/mi',
            '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|From|M\/?S\.?|Messrs)\b\s*[:\-]?\s*(?:\R|\s)*([A-Z][A-Z0-9&.,()\-\/\s]{3,140})(?=\R|\s+GSTIN|\s+Invoice|\s+Date|\s+Address|\s+Phone|\s+Email)/mi',
        ];

        foreach ($linePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $candidate = $this->sanitizeVendorCandidate((string) ($matches[1] ?? ''));
                if ($candidate !== null) {
                    return $candidate;
                }
            }
        }

        $singleLine = preg_replace('/\s+/', ' ', $text) ?? '';
        if ($singleLine !== '') {
            $fallbackPatterns = [
                '/\bBill\s*To(?:\s*Ship\s*To)?\s+([A-Z][A-Z0-9&.,()\-\/\s]{3,140}?)(?=\s+GSTIN|\s+D\.?NO|\s+ST\-|\s+Place\s+Of|\s+Invoice\b)/i',
                '/\b(?:Supplier|Vendor|Bill\s*From|Sold\s*By|M\/?S\.?|Messrs)\s*[:\-]?\s*([A-Z][A-Z0-9&.,()\-\/\s]{3,140}?)(?=\s+GSTIN|\s+Invoice\b|\s+Date\b)/i',
            ];

            foreach ($fallbackPatterns as $pattern) {
                if (preg_match($pattern, $singleLine, $matches)) {
                    $candidate = $this->sanitizeVendorCandidate((string) ($matches[1] ?? ''));
                    if ($candidate !== null) {
                        return $candidate;
                    }
                }
            }
        }

        return null;
    }

    private function sanitizeVendorCandidate(string $candidate): ?string
    {
        $candidate = trim(preg_replace('/\s+/', ' ', $candidate));
        $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email|terms|place\s*of\s*supply|address)\b/i', $candidate)[0] ?? $candidate;
        $candidate = trim((string) $candidate, " \t\n\r\0\x0B:,-");

        if (strlen($candidate) < 4) {
            return null;
        }

        if (preg_match('/^(invoice|tax|total|ship\s*to|bill\s*to)$/i', $candidate)) {
            return null;
        }

        return $candidate;
    }

    private function extractAmount(string $text, string $keyword): float
    {
        preg_match('/' . preg_quote($keyword, '/') . '[\s:₹]*([\d,]+\.?\d*)/i', $text, $m);
        return isset($m[1]) ? (float) str_replace(',', '', $m[1]) : 0.0;
    }

    private function extractTotal(string $text): float
    {
        $strongPatterns = [
            '/(?:grand\s*total|invoice\s*total|net\s*payable|amount\s*payable|total\s*amount)\s*[:\-]?\s*(?:inr|rs\.?|₹)?\s*([\d,]+(?:\.\d{1,2})?)/i',
            '/(?:balance\s*due|amount\s*due)\s*[:\-]?\s*(?:inr|rs\.?|₹)?\s*([\d,]+(?:\.\d{1,2})?)/i',
        ];

        foreach ($strongPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches) && !empty($matches[1])) {
                $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
                return max($values);
            }
        }

        if (preg_match_all('/\btotal\b\s*[:\-]?\s*(?:inr|rs\.?|₹)?\s*([\d,]+(?:\.\d{1,2})?)/i', $text, $matches) && !empty($matches[1])) {
            $values = array_map(fn ($v) => (float) str_replace(',', '', $v), $matches[1]);
            return max($values);
        }

        return 0.0;
    }

    private function extractInvoiceDate(string $text): ?string
    {
        $patterns = [
            '/\b(?:invoice\s*date|bill\s*date|date\s*of\s*invoice|dated)\b\s*[:\-]?\s*(\d{1,2}[\/\-.]\d{1,2}[\/\-.]\d{2,4})/i',
            '/\b(?:invoice\s*date|bill\s*date|date)\b\s*[:\-]?\s*(\d{1,2}\s+[A-Za-z]{3,9}\s+\d{2,4})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return $this->normalizeDateString($m[1]);
            }
        }

        if (preg_match('/\b(\d{4}[\/-]\d{1,2}[\/-]\d{1,2})\b/', $text, $m)) {
            return $this->normalizeDateString($m[1]);
        }

        return null;
    }

    private function normalizeDateString(string $dateValue): ?string
    {
        $dateValue = trim($dateValue);
        $formats = ['d-m-Y', 'd/m/Y', 'd.m.Y', 'd-m-y', 'd/m/y', 'd.m.y', 'Y-m-d', 'Y/m/d', 'd M Y', 'd F Y', 'd M y', 'd F y'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $dateValue)->toDateString();
            } catch (\Throwable $e) {
                // continue
            }
        }

        try {
            return Carbon::parse($dateValue)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function determineTotal(array $parsed, PurchaseInvoice $invoice): float
    {
        $arc = (float) ($parsed['arc'] ?? 0);
        $otc = (float) ($parsed['otc'] ?? 0);
        $static = (float) ($parsed['static'] ?? 0);
        $bundle = $arc + $otc + $static;

        if ($bundle > 0) {
            return $bundle;
        }

        $total = (float) ($parsed['total'] ?? 0);
        if ($total > 0) {
            return $total;
        }

        return (float) ($invoice->total_amount ?? $invoice->grand_total ?? $invoice->amount ?? 0);
    }

    private function calculateParserConfidence(array $invoiceData): float
    {
        $fields = ['gst', 'vendor_name', 'invoice_number', 'invoice_date', 'total'];
        $presentFields = 0;

        foreach ($fields as $field) {
            if ($field === 'total') {
                if ((float) ($invoiceData[$field] ?? 0) > 0) {
                    $presentFields++;
                }
                continue;
            }

            if (!empty($invoiceData[$field])) {
                $presentFields++;
            }
        }

        return round(($presentFields / count($fields)) * 100, 2);
    }

    private function combineConfidence(float $parserConfidence, int $matchScore): float
    {
        if ($matchScore <= 0) {
            return round($parserConfidence, 2);
        }

        return round(($parserConfidence * 0.65) + ($matchScore * 0.35), 2);
    }

    private function pickBestInvoiceNo(?string $fromText, ?string $fromFilename, string $existing): string
    {
        if ($fromText) {
            return $fromText;
        }

        if ($fromFilename) {
            return $fromFilename;
        }

        return $existing;
    }

    private function buildUpdatedRawJson($existingRaw, array $parsed, array $matchResult, float $parserConfidence, float $confidence): array
    {
        $raw = is_array($existingRaw) ? $existingRaw : [];

        $raw['reprocessed'] = [
            'at' => now()->toDateTimeString(),
            'parser_confidence' => $parserConfidence,
            'combined_confidence' => $confidence,
            'matched_by' => $matchResult['matched_by'] ?? null,
            'vendor_match_score' => $matchResult['score'] ?? 0,
        ];

        foreach (['gst', 'vendor_name', 'invoice_number', 'invoice_date', 'total', 'arc', 'otc', 'static'] as $key) {
            if (array_key_exists($key, $parsed)) {
                $raw[$key] = $parsed[$key];
            }
        }

        return $raw;
    }
}
