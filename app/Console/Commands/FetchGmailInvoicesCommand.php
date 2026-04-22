<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PurchaseInvoice;
use App\Models\Vendor;
use App\Models\EmailLog;
use App\Models\CompanySetting;
use App\Services\VendorResolverService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FetchGmailInvoicesCommand extends Command
{
    protected $signature   = 'invoice:fetch-gmail {--recent : Also scan already-read emails from configured lookback days} {--days= : Lookback days for --recent mode}';
    protected $description = 'Fetch invoice attachments (PDF/Excel/Text) from Gmail (IMAP) and auto-create Purchase Invoices';

    public function handle(): int
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }
        @ini_set('max_execution_time', '0');

        if (!function_exists('imap_open')) {
            $this->error('PHP IMAP extension is not enabled. Enable php_imap in php.ini and restart XAMPP.');
            return 1;
        }

        $companySetting = $this->resolveInvoiceCompanySetting();
        $mailboxConfig = $this->resolveMailboxConfig($companySetting);

        $host     = $mailboxConfig['host'];
        $username = $mailboxConfig['username'];
        $password = $mailboxConfig['password'];

        $this->info("Connecting to Gmail: {$username}");

        $inbox = @imap_open($host, $username, $password);

        if (!$inbox) {
            $this->error('Gmail IMAP connection failed: ' . imap_last_error());
            Log::error('Gmail IMAP connection failed: ' . imap_last_error());
            return 1;
        }

        $this->info('Connected! Preparing mail scan...');

        // Track last processed UID so future runs process only new mails.
        $stateFile = storage_path('app/invoice_fetch_state.json');
        $state = $this->loadState($stateFile);
        $lastProcessedUid = (int) ($state['last_uid'] ?? 0);
        // Scan all emails in the lookback window to find invoice attachments.
        $maxPerRun = max((int) env('INVOICE_FETCH_MAX_PER_RUN', 100), 1);

        $uids = imap_search($inbox, 'ALL', SE_UID);
        if (!$uids) {
            $this->info('No emails found in mailbox.');
            imap_close($inbox);
            return 0;
        }

        sort($uids, SORT_NUMERIC);

        $configuredLookbackDays = (int) (($companySetting?->invoice_mail_read_days) ?? env('INVOICE_FETCH_LOOKBACK_DAYS', 30));
        $configuredLookbackDays = max($configuredLookbackDays, 1);
        $lookbackDaysOption = $this->option('days');
        $lookbackDays = ($lookbackDaysOption !== null && $lookbackDaysOption !== '')
            ? max((int) $lookbackDaysOption, 1)
            : $configuredLookbackDays;
        $recentUids = $this->getUidsSince($inbox, $lookbackDays);

        // --recent: one-time backfill for configured days (manual mode)
        if ($this->option('recent')) {
            $this->info('  [--recent mode] Scanning emails from last ' . $lookbackDays . ' days...');
            $candidateUids = $recentUids;
        } else {
            // First run bootstraps state to avoid scanning thousands of historical mails.
            if ($lastProcessedUid === 0) {
                $bootstrapUid = (int) end($uids);
                $this->saveState($stateFile, ['last_uid' => $bootstrapUid, 'updated_at' => now()->toDateTimeString()]);
                $this->warn('Bootstrap complete: historical mails skipped. Future runs will process only new incoming mails.');
                imap_close($inbox);
                return 0;
            }

            $candidateUids = array_values(array_filter(
                $recentUids,
                fn ($uid) => $uid > $lastProcessedUid
            ));
            sort($candidateUids, SORT_NUMERIC);
        }

        if (empty($candidateUids)) {
            $this->info('No new emails to process.');
            imap_close($inbox);
            return 0;
        }

        if (count($candidateUids) > $maxPerRun) {
            $this->warn('Too many new mails. Processing only latest ' . $maxPerRun . ' this run.');
            $candidateUids = array_slice($candidateUids, -$maxPerRun);
        }

        $this->info(count($candidateUids) . ' email(s) selected for this run.');
        $processed = 0;
        $lastScannedUid = $lastProcessedUid;

        foreach ($candidateUids as $emailUid) {
            $emailNum = imap_msgno($inbox, $emailUid);
            if ($emailNum <= 0) {
                continue;
            }

            $lastScannedUid = max($lastScannedUid, (int) $emailUid);
            $header  = imap_headerinfo($inbox, $emailNum);
            $subject = isset($header->subject) ? imap_utf8($header->subject) : 'No Subject';
            $from    = $header->fromaddress ?? 'unknown';
            $mailDate = $this->extractHeaderDate($header);
            $subjectHasInvoiceHint = $this->isInvoiceKeywordText($subject); // for logging only

            $this->line("→ Email: [{$subject}] from [{$from}]");

            $structure = imap_fetchstructure($inbox, $emailNum);
            if (!$structure) {
                $this->warn('  ⚠ Could not fetch email structure, skipping.');
                continue;
            }

            $this->line("  Structure: type={$structure->type} subtype={$structure->subtype} parts=" . (isset($structure->parts) ? count($structure->parts) : '0'));

            $parts     = [];
            if (isset($structure->parts)) {
                $this->collectParts($structure->parts, $parts, $emailNum, '');
            }

            $hasInvoiceAttachmentHint = false;
            foreach ($parts as $partMeta) {
                $partFilename = (string) ($partMeta['filename'] ?? '');
                if ($partFilename !== '' && $this->isInvoiceKeywordText($partFilename)) {
                    $hasInvoiceAttachmentHint = true;
                    break;
                }
            }

            if (empty($parts)) {
                $this->line('  ⏭ Skipped email (no parts found).');
                continue;
            }

            $attachmentsFound = 0;

            foreach ($parts as $part) {
                $filename = $part['filename'] ?? '';
                $subtype  = strtolower($part['subtype'] ?? '');

                if (!$this->isSupportedInvoiceAttachmentPart($part)) {
                    continue;
                }

                $attachmentsFound++;
                $this->line("  Attachment found: {$filename}");

                // All PDF attachments are treated as potential invoices (no keyword restriction).
                $fileHasInvoiceHint = $this->isInvoiceKeywordText($filename); // for logging only

                // Download & decode attachment
                $raw = imap_fetchbody($inbox, $emailNum, $part['partnum']);
                if ($part['encoding'] == 3) {
                    $raw = base64_decode($raw);
                } elseif ($part['encoding'] == 4) {
                    $raw = quoted_printable_decode($raw);
                }

                if (!$raw || strlen($raw) < 100) {
                    $this->error('  ✗ Empty/Invalid attachment content, skipping.');
                    continue;
                }

                $attachmentHash = hash('sha256', $raw);

                $existingInvoiceByHash = $this->findInvoiceByAttachmentHash($attachmentHash);
                if ($existingInvoiceByHash && !$this->shouldReprocessForInvoiceNumber($existingInvoiceByHash)) {
                    $this->warn('  ⚠ Attachment already imported earlier, skipping duplicate.');
                    continue;
                }

                if ($existingInvoiceByHash) {
                    $this->info('  ↻ Reprocessing duplicate attachment to backfill exact invoice number.');
                }

                // Save temp file
                $tempDir = storage_path('temp');
                if (!is_dir($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $extension = $this->resolveAttachmentExtension($filename, $subtype);
                $tempFile = $tempDir . '/' . time() . '_invoice.' . $extension;
                file_put_contents($tempFile, $raw);

                // Parse PDF → extract GST + amounts
                $invoiceData = $this->parseInvoice($tempFile);
                $invoiceDate = $invoiceData['invoice_date'] ?? $mailDate ?? now()->toDateString();

                if (isset($invoiceData['error'])) {
                    if (!$subjectHasInvoiceHint && !$fileHasInvoiceHint) {
                        $this->warn('  ⏭ Parse failed for non-invoice-looking attachment. Skipping.');
                        @unlink($tempFile);
                        continue;
                    }

                    $this->error('  ✗ Parse failed: ' . $invoiceData['error']);

                    // Always store any supported attachment for manual review regardless of keywords.
                    {
                        $publicDir = public_path('images/poinvoice_files');
                        if (!is_dir($publicDir)) {
                            mkdir($publicDir, 0755, true);
                        }

                        $savedFile = date('YmdHis') . '_' . ($filename ?: ('invoice.' . $extension));
                        copy($tempFile, $publicDir . '/' . $savedFile);

                        $emailLog = EmailLog::create([
                            'sender'          => $from,
                            'subject'         => $subject,
                            'body'            => '',
                            'attachment_path' => 'images/poinvoice_files/' . $savedFile,
                            'status'          => 'failed',
                            'error_message'   => $invoiceData['error'],
                            'file_hash'       => $attachmentHash,
                        ]);

                        $fallbackInvoiceNo = 'MAIL-' . $emailUid;
                        $duplicate = PurchaseInvoice::where('invoice_no', $fallbackInvoiceNo)->exists();

                        if (!$duplicate) {
                            $fromEmail = $this->extractEmail($from);
                            $vendor = null;
                            if ($fromEmail) {
                                $vendor = Vendor::where('contact_person_email', $fromEmail)->first();
                            }

                            $pi = PurchaseInvoice::create([
                                'type'            => 'purchase',
                                'vendor_id'       => $vendor?->id,
                                'vendor_name'     => $this->extractName($from),
                                'vendor_name_raw' => $this->extractName($from),
                                'invoice_no'      => $fallbackInvoiceNo,
                                'invoice_date'    => $invoiceDate,
                                'amount'          => 0,
                                'grand_total'     => 0,
                                'total_amount'    => 0,
                                'po_invoice_file' => $savedFile,
                                'status'          => 'failed',
                                'arc_amount'      => 0,
                                'otc_amount'      => 0,
                                'static_amount'   => 0,
                                'raw_json'        => [
                                    'parse_error' => $invoiceData['error'],
                                    'import_failure_reason' => $invoiceData['error'],
                                    'failure_stage' => 'parse',
                                    'failure_source' => 'gmail_import',
                                    'mail_uid'    => $emailUid,
                                ],
                                'email_log_id'    => $emailLog->id,
                            ]);

                            $this->info("  ✅ Saved for manual review. ID: {$pi->id} | Invoice No: {$fallbackInvoiceNo}");
                            $processed++;
                        } else {
                            $this->warn("  ⚠ Parse-failed mail already captured as [{$fallbackInvoiceNo}].");
                        }
                    }

                    @unlink($tempFile);
                    continue;
                }

                $gst       = $invoiceData['gst'] ?? null;
                $invoiceNo = $invoiceData['invoice_number'] ?? null;
                if (!$invoiceNo && !empty($filename)) {
                    $invoiceNo = $this->extractInvoiceNumberFromFilename($filename);
                }

                if (!$this->hasMeaningfulInvoiceData($invoiceData)) {
                    // If filename or subject has invoice keyword, still save for manual review.
                    if ($fileHasInvoiceHint || $subjectHasInvoiceHint) {
                        $this->warn('  ⚠ No data extracted but invoice hint found. Saving for manual review.');
                        $invoiceData['vendor_name'] = $this->extractName($from);
                    } else {
                        $this->warn('  ⏭ Attachment parsed but has no meaningful invoice data. Skipping.');
                        @unlink($tempFile);
                        continue;
                    }
                }

                if ($existingInvoiceByHash && !$invoiceNo) {
                    $this->warn('  ⚠ Duplicate attachment has no extractable invoice number. Skipping backfill to avoid wrong updates.');
                    @unlink($tempFile);
                    continue;
                }

                if (!$subjectHasInvoiceHint && !$fileHasInvoiceHint && empty($invoiceData['is_invoice']) && !$this->hasMeaningfulInvoiceData($invoiceData)) {
                    $this->warn('  ⏭ Attachment skipped because it does not look like an invoice.');
                    @unlink($tempFile);
                    continue;
                }

                $this->line('  GST: ' . ($gst ?? 'Not found'));
                $this->line('  Invoice No: ' . ($invoiceNo ?? 'Not found'));

                // Find vendor by GST or sender email
                $resolver = app(VendorResolverService::class);
                $matchResult = $resolver->resolveMatch([
                    'gstin' => $gst,
                    'vendor_name' => $invoiceData['vendor_name'] ?? $this->extractName($from),
                ]);
                $vendor = $matchResult['vendor'];
                if (!$vendor) {
                    $fromEmail = $this->extractEmail($from);
                    if ($fromEmail) {
                        $vendor = Vendor::where('contact_person_email', $fromEmail)->first();
                        if ($vendor) {
                            $nameMatch = trim((string) ($invoiceData['vendor_name'] ?? '')) !== ''
                                && strcasecmp(trim((string) $invoiceData['vendor_name']), trim((string) $vendor->vendor_name)) === 0;
                            $matchResult = [
                                'vendor' => $vendor,
                                'score' => $nameMatch ? 78 : 72,
                                'matched_by' => 'email',
                                'gst_match' => !empty($gst) && strtoupper((string) $gst) === strtoupper((string) $vendor->gstin),
                                'name_match' => $nameMatch,
                                'name_similarity' => $nameMatch ? 100 : 0,
                                'vendor_master_name' => $vendor->vendor_name,
                                'vendor_master_display_name' => $vendor->business_display_name,
                            ];
                        }
                    }
                }

                // Save file permanently
                $publicDir  = public_path('images/poinvoice_files');
                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }
                $savedFile = date('YmdHis') . '_' . ($filename ?: ('invoice.' . $extension));
                copy($tempFile, $publicDir . '/' . $savedFile);
                @unlink($tempFile);

                // Log the email
                $emailLog = EmailLog::create([
                    'sender'          => $from,
                    'subject'         => $subject,
                    'body'            => '',
                    'attachment_path' => 'images/poinvoice_files/' . $savedFile,
                    'status'          => 'processed',
                    'file_hash'       => $attachmentHash,
                ]);

                // Calculate total
                $arc    = $invoiceData['arc']    ?? 0;
                $otc    = $invoiceData['otc']    ?? 0;
                $static = $invoiceData['static'] ?? 0;
                $total  = ($arc + $otc + $static) > 0
                        ? ($arc + $otc + $static)
                        : ($invoiceData['total'] ?? $invoiceData['router'] ?? 0);

                $parserConfidence = $this->calculateParserConfidence($invoiceData);
                $confidence = $this->combineConfidence($parserConfidence, $matchResult['score'] ?? 0);

                $pdfVendorName = trim((string) ($invoiceData['vendor_name'] ?? ''));
                $masterVendorName = trim((string) ($matchResult['vendor_master_name'] ?? $vendor?->vendor_name ?? ''));
                $finalVendorName = $masterVendorName !== ''
                    ? $masterVendorName
                    : ($pdfVendorName !== '' ? $pdfVendorName : $this->extractName($from));
                $vendorFailureReason = $this->resolveVendorValidationFailure($pdfVendorName, $vendor, $matchResult);

                if ($vendorFailureReason) {
                    $emailLog->update([
                        'status' => 'failed',
                        'error_message' => $vendorFailureReason,
                    ]);
                }

                // Duplicate check / update-on-reimport
                $finalInvoiceNo = $invoiceNo ?: $this->generateFallbackInvoiceNo($gst, $invoiceDate, $total, $finalVendorName);
                $existingInvoice = $this->findExistingInvoiceForImport($finalInvoiceNo, $gst, $invoiceDate, $total, $finalVendorName);

                // Create Purchase Invoice

                if ($existingInvoice) {
                    $updated = $this->applyReimportUpdate(
                        $existingInvoice,
                        $invoiceData,
                        $matchResult,
                        $finalInvoiceNo,
                        $invoiceDate,
                        $gst,
                        $arc,
                        $otc,
                        $static,
                        $total,
                        $confidence,
                        $parserConfidence,
                        $savedFile,
                        $emailLog->id,
                        $attachmentHash,
                        $finalVendorName,
                        $vendorFailureReason
                    );

                    if ($updated) {
                        $this->info("  ♻ Duplicate invoice [{$finalInvoiceNo}] updated with changed values.");
                    } else {
                        $this->warn("  ⚠ Duplicate invoice [{$finalInvoiceNo}] found with no value changes.");
                    }
                    $processed++;
                    continue;
                }

                $pi = PurchaseInvoice::create([
                    'type'           => 'purchase',
                    'vendor_id'      => $vendor?->id,
                    'vendor_name'    => $finalVendorName,
                    'vendor_name_raw'=> $finalVendorName,
                    'invoice_no'     => $finalInvoiceNo,
                    'invoice_date'   => $invoiceDate,
                    'amount'         => $total,
                    'grand_total'    => $total,
                    'total_amount'   => $total,
                    'gstin'          => $gst,
                    'gst_number'     => $gst,
                    'vendor_gstin'   => $gst,
                    'po_invoice_file'=> $savedFile,
                    'status'         => 'needs_review',
                    'confidence_score'=> $confidence,
                    'arc_amount'     => $arc,
                    'otc_amount'     => $otc,
                    'static_amount'  => $static,
                    'raw_json'       => $this->attachImportFailureReason(
                        $this->withMatchDetails($invoiceData, $matchResult, $parserConfidence, $confidence),
                        $vendorFailureReason
                    ),
                    'email_log_id'   => $emailLog->id,
                ]);

                $this->info("  ✅ Purchase Invoice created! ID: {$pi->id} | GST: {$gst}");
                $processed++;
            }

            if ($attachmentsFound === 0) {
                $this->line('  ⊘ No file attachments. Trying email body as invoice...');

                // Fallback: parse email body (text/html/plain) directly as invoice
                $bodyText = $this->extractEmailBodyText($inbox, $emailNum, $parts);

                if ($bodyText !== '') {
                    if (!$subjectHasInvoiceHint && !$this->isInvoiceDocument($bodyText)) {
                        $this->warn('  ⏭ Body text does not look like an invoice. Skipping email.');
                        imap_setflag_full($inbox, (string)$emailNum, '\\Seen');
                        continue;
                    }

                    $invoiceData = $this->extractFromText($bodyText);

                    if ($this->hasMeaningfulInvoiceData($invoiceData)) {
                        $this->info('  ✓ Invoice data found in email body text.');

                        $gst       = $invoiceData['gst'] ?? null;
                        $invoiceNo = $invoiceData['invoice_number'] ?? null;
                        $invoiceDate = $invoiceData['invoice_date'] ?? $mailDate ?? now()->toDateString();

                        $emailLog = EmailLog::create([
                            'sender'          => $from,
                            'subject'         => $subject,
                            'body'            => substr($bodyText, 0, 2000),
                            'attachment_path' => null,
                            'status'          => 'processed',
                            'file_hash'       => hash('sha256', $from . $subject . substr($bodyText, 0, 500)),
                        ]);

                        $resolver = app(VendorResolverService::class);
                        $matchResult = $resolver->resolveMatch([
                            'gstin' => $gst,
                            'vendor_name' => $invoiceData['vendor_name'] ?? $this->extractName($from),
                        ]);
                        $vendor = $matchResult['vendor'];
                        if (!$vendor) {
                            $fromEmail = $this->extractEmail($from);
                            if ($fromEmail) {
                                $vendor = Vendor::where('contact_person_email', $fromEmail)->first();
                            }
                        }

                        $arc    = $invoiceData['arc']    ?? 0;
                        $otc    = $invoiceData['otc']    ?? 0;
                        $static = $invoiceData['static'] ?? 0;
                        $total  = ($arc + $otc + $static) > 0
                                ? ($arc + $otc + $static)
                                : ($invoiceData['total'] ?? 0);

                        $parserConfidence = $this->calculateParserConfidence($invoiceData);
                        $confidence = $this->combineConfidence($parserConfidence, $matchResult['score'] ?? 0);
                        $pdfVendorName = trim((string) ($invoiceData['vendor_name'] ?? ''));
                        $masterVendorName = trim((string) ($matchResult['vendor_master_name'] ?? $vendor?->vendor_name ?? ''));
                        $finalVendorName = $masterVendorName !== ''
                            ? $masterVendorName
                            : ($pdfVendorName !== '' ? $pdfVendorName : $this->extractName($from));
                        $vendorFailureReason = $this->resolveVendorValidationFailure($pdfVendorName, $vendor, $matchResult);
                        $finalInvoiceNo = $invoiceNo ?: $this->generateFallbackInvoiceNo($gst, $invoiceDate, $total, $finalVendorName);
                        $existingInvoice = $this->findExistingInvoiceForImport($finalInvoiceNo, $gst, $invoiceDate, $total, $finalVendorName);

                        if (!$existingInvoice) {
                            $pi = PurchaseInvoice::create([
                                'type'            => 'purchase',
                                'vendor_id'       => $vendor?->id,
                                'vendor_name'     => $finalVendorName,
                                'vendor_name_raw' => $finalVendorName,
                                'invoice_no'      => $finalInvoiceNo,
                                'invoice_date'    => $invoiceDate,
                                'amount'          => $total,
                                'grand_total'     => $total,
                                'total_amount'    => $total,
                                'gstin'           => $gst,
                                'gst_number'      => $gst,
                                'vendor_gstin'    => $gst,
                                'po_invoice_file' => null,
                                'status'          => 'needs_review',
                                'confidence_score' => $confidence,
                                'arc_amount'      => $arc,
                                'otc_amount'      => $otc,
                                'static_amount'   => $static,
                                'raw_json'        => $this->attachImportFailureReason(
                                    $this->withMatchDetails($invoiceData, $matchResult, $parserConfidence, $confidence),
                                    $vendorFailureReason
                                ),
                                'email_log_id'    => $emailLog->id,
                            ]);
                            $this->info("  ✅ Purchase Invoice created from body! ID: {$pi->id} | Invoice: {$finalInvoiceNo}");
                            $processed++;
                        } else {
                            $this->warn("  ⚠ Duplicate invoice [{$finalInvoiceNo}] already exists.");
                        }
                    } else {
                        $this->warn('  ⊘ Email body has no meaningful invoice data (no GST/invoice no/total found).');
                    }
                } else {
                    $this->warn('  ⊘ Could not extract any text from email body.');
                }
            }

            // Mark email as read.
            imap_setflag_full($inbox, (string)$emailNum, '\\Seen');
        }

        // Save incremental state only for non-recent mode.
        if (!$this->option('recent') && $lastScannedUid > 0) {
            $this->saveState($stateFile, ['last_uid' => $lastScannedUid, 'updated_at' => now()->toDateTimeString()]);
        }

        imap_close($inbox);
        $this->info("Done! {$processed} invoice(s) created.");
        return 0;
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    private function extractEmailBodyText($inbox, int $emailNum, array $parts): string
    {
        $htmlText = '';
        $plainText = '';

        foreach ($parts as $part) {
            $subtype = strtolower($part['subtype'] ?? '');
            $type    = (int) ($part['type'] ?? -1);
            $filename = trim((string) ($part['filename'] ?? ''));

            // Only grab text/* parts with no filename (body parts, not attachments)
            if ($type !== 0 || $filename !== '') {
                continue;
            }

            $raw = imap_fetchbody($inbox, $emailNum, $part['partnum']);
            $encoding = (int) ($part['encoding'] ?? 0);

            if ($encoding === 3) {
                $raw = base64_decode($raw);
            } elseif ($encoding === 4) {
                $raw = quoted_printable_decode($raw);
            }

            $charset = 'UTF-8';
            foreach (($part['parameters'] ?? []) as $p) {
                if (strtolower($p->attribute ?? '') === 'charset') {
                    $charset = strtoupper($p->value ?? 'UTF-8');
                    break;
                }
            }

            if ($charset !== 'UTF-8' && $charset !== '') {
                $converted = @iconv($charset, 'UTF-8//IGNORE', (string) $raw);
                if ($converted !== false) {
                    $raw = $converted;
                }
            }

            if ($subtype === 'html') {
                $htmlText .= strip_tags((string) $raw) . "\n";
            } else {
                $plainText .= ((string) $raw) . "\n";
            }
        }

        // Prefer plain text; fall back to HTML-stripped
        $text = trim($plainText !== '' ? $plainText : $htmlText);

        // If no body parts found, fetch raw body (single-part email)
        if ($text === '') {
            $raw = imap_body($inbox, $emailNum);
            $structure = imap_fetchstructure($inbox, $emailNum);
            $encoding = (int) ($structure->encoding ?? 0);
            if ($encoding === 3) {
                $raw = base64_decode($raw);
            } elseif ($encoding === 4) {
                $raw = quoted_printable_decode($raw);
            }
            $text = trim(strip_tags((string) $raw));
        }

        return $text;
    }

    private function collectParts(array $parts, array &$result, $emailNum, string $prefix): void
    {
        foreach ($parts as $i => $part) {
            $partnum  = $prefix !== '' ? "{$prefix}." . ($i + 1) : (string)($i + 1);
            $filename = null;
            $nameSegments = [];

            foreach (($part->dparameters ?? []) as $param) {
                $attribute = strtolower((string) ($param->attribute ?? ''));
                if ($attribute === 'filename') {
                    $filename = $param->value;
                }
                if (str_starts_with($attribute, 'filename*')) {
                    $nameSegments[$attribute] = (string) ($param->value ?? '');
                }
            }

            if (!$filename) {
                foreach (($part->parameters ?? []) as $param) {
                    $attribute = strtolower((string) ($param->attribute ?? ''));
                    if ($attribute === 'name') {
                        $filename = $param->value;
                    }
                    if (str_starts_with($attribute, 'name*')) {
                        $nameSegments[$attribute] = (string) ($param->value ?? '');
                    }
                }
            }

            if (!$filename && !empty($nameSegments)) {
                ksort($nameSegments);
                $filename = implode('', $nameSegments);
            }

            $filename = $this->decodeMimeFilename($filename);
            $disposition = strtolower((string) ($part->disposition ?? ''));

            $result[] = [
                'type'     => $part->type,
                'subtype'  => strtolower($part->subtype ?? ''),
                'encoding' => $part->encoding ?? 0,
                'partnum'  => $partnum,
                'filename' => $filename,
                'disposition' => $disposition,
            ];

            if (!empty($part->parts)) {
                $this->collectParts($part->parts, $result, $emailNum, $partnum);
            }
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

        $ocrKey = trim(explode('#', env('OCR_API_KEY', ''))[0]); // strip inline comment
        if ($ocrKey) {
            try {
                $response = Http::timeout(30)
                    ->attach('file', file_get_contents($filePath), basename($filePath))
                    ->post('https://api.ocr.space/parse/image', [
                        'apikey'   => $ocrKey,
                        'language' => 'eng',
                    ]);
                $result = $response->json();
                if (empty($result['IsErroredOnProcessing'])) {
                    $text = $result['ParsedResults'][0]['ParsedText'] ?? '';
                    if ($text) {
                        return $this->extractFromText($text);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('OCR failed, using local PDF parser: ' . $e->getMessage());
            }
        }

        return $this->parseViaLocalPdf($filePath);
    }

    private function parseViaPlainText(string $filePath): array
    {
        try {
            $text = (string) file_get_contents($filePath);
            if (trim($text) === '') {
                return ['error' => 'NO_TEXT_EXTRACTED'];
            }

            return $this->extractFromText($text);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function isSupportedInvoiceAttachment(string $filename, string $subtype): bool
    {
        // Do not trust generic plain-text MIME parts with empty names unless marked as attachments.
        $supportedBySubtype = in_array($subtype, ['pdf', 'octet-stream', 'csv', 'vnd.ms-excel', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        $lower = strtolower($filename);
        $supportedByName = str_ends_with($lower, '.pdf')
            || str_ends_with($lower, '.txt')
            || str_ends_with($lower, '.csv')
            || str_ends_with($lower, '.xls')
            || str_ends_with($lower, '.xlsx');

        if (trim($filename) === '' && !$supportedBySubtype) {
            return false;
        }

        return $supportedBySubtype || $supportedByName;
    }

    private function isSupportedInvoiceAttachmentPart(array $part): bool
    {
        $filename = trim((string) ($part['filename'] ?? ''));
        $subtype = strtolower(trim((string) ($part['subtype'] ?? '')));
        $disposition = strtolower(trim((string) ($part['disposition'] ?? '')));
        $type = (int) ($part['type'] ?? -1);

        if ($this->isSupportedInvoiceAttachment($filename, $subtype)) {
            return true;
        }

        if (in_array($disposition, ['attachment', 'inline'], true) && in_array($type, [3, 5, 7], true)) {
            return in_array($subtype, ['pdf', 'octet-stream', 'plain', 'csv', 'vnd.ms-excel', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet'], true);
        }

        return false;
    }

    private function decodeMimeFilename(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        if (function_exists('imap_mime_header_decode')) {
            $decodedParts = @imap_mime_header_decode($value);
            if (is_array($decodedParts) && !empty($decodedParts)) {
                $decoded = '';
                foreach ($decodedParts as $part) {
                    $decoded .= (string) ($part->text ?? '');
                }
                $value = $decoded !== '' ? $decoded : $value;
            }
        }

        $value = preg_replace("/^[^']*''/", '', $value) ?? $value;
        $decodedUrl = rawurldecode($value);

        return trim($decodedUrl !== '' ? $decodedUrl : $value, "\"'");
    }

    private function hasMeaningfulInvoiceData(array $invoiceData): bool
    {
        $invoiceNo   = trim((string) ($invoiceData['invoice_number'] ?? ''));
        $gst         = trim((string) ($invoiceData['gst'] ?? ''));
        $vendorName  = trim((string) ($invoiceData['vendor_name'] ?? ''));
        $invoiceDate = trim((string) ($invoiceData['invoice_date'] ?? ''));
        $total       = (float) ($invoiceData['total'] ?? 0);
        $isInvoiceFlag = (bool) ($invoiceData['is_invoice'] ?? false);

        // Has at least one core identifier
        if ($invoiceNo !== '' || $gst !== '' || $total > 0) {
            return true;
        }

        if ($isInvoiceFlag && ($invoiceNo !== '' || $gst !== '' || $total > 0 || ($vendorName !== '' && $invoiceDate !== ''))) {
            return true;
        }

        return false;
    }

    private function resolveAttachmentExtension(string $filename, string $subtype): string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, ['pdf', 'txt', 'csv', 'xls', 'xlsx'])) {
            return $ext;
        }

        if (str_contains($subtype, 'excel')) {
            return 'xls';
        }

        if ($subtype === 'plain') {
            return 'txt';
        }

        return 'pdf';
    }

    private function parseViaLocalPdf(string $filePath): array
    {
        try {
            if (!class_exists(Parser::class)) {
                return ['error' => 'smalot/pdfparser package not installed'];
            }

            $pdf  = (new Parser())->parseFile($filePath);
            $text = $pdf->getText();
            if (!$text) {
                return ['error' => 'NO_TEXT_EXTRACTED'];
            }
            return $this->extractFromText($text);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function extractFromText(string $text): array
    {
        return [
            'gst'            => $this->extractGST($text),
            'vendor_name'    => $this->extractVendorName($text),
            'invoice_number' => $this->extractInvoiceNumber($text),
            'invoice_date'   => $this->extractInvoiceDate($text),
            'is_invoice'     => $this->isInvoiceDocument($text),
            'arc'            => $this->extractAmount($text, 'ARC'),
            'otc'            => $this->extractAmount($text, 'OTC'),
            'static'         => $this->extractAmount($text, 'Static'),
            'router'         => $this->extractAmount($text, 'Router'),
            'total'          => $this->extractTotal($text),
        ];
    }

    private function resolveMailboxConfig(?CompanySetting $companySetting): array
    {
        // Priority: Company Settings invoice mail config > IMAP_* env > MAIL_* env > defaults
        $host = $this->buildImapHost($companySetting)
            ?: $this->buildImapHostFromEnv()
            ?: env('IMAP_HOST', '{imap.gmail.com:993/imap/ssl/novalidate-cert}[Gmail]/All Mail');

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

        return [
            'host' => $host,
            'username' => $username,
            'password' => $password,
        ];
    }

    private function buildImapHostFromEnv(): ?string
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

    private function buildImapHost(?CompanySetting $companySetting): ?string
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

    private function extractGST(string $text): ?string
    {
        // GSTIN: 2-digit state + 5 alpha (PAN) + 4 digits (PAN) + 1 alpha (PAN) + 1 alphanum entity (1-9 or A-Z) + 1 alpha (usually Z) + 1 alphanum check
        preg_match_all('/\b\d{2}[A-Z]{5}\d{4}[A-Z][1-9A-Z][A-Z][0-9A-Z]\b/', strtoupper($text), $matches);
        if (!empty($matches[0])) {
            // Return the first valid GSTIN found
            return strtoupper($matches[0][0]);
        }
        // Try alternate pattern (sometimes GSTIN is split by spaces or special chars)
        preg_match('/\b\d{2}\s*[A-Z]{5}\s*\d{4}\s*[A-Z]\s*[1-9A-Z]\s*[A-Z]\s*[0-9A-Z]\b/', strtoupper($text), $alt);
        if (isset($alt[0])) {
            return preg_replace('/\s+/', '', strtoupper($alt[0]));
        }
        Log::warning('GSTIN extraction failed', ['text_snippet' => mb_substr($text, 0, 200)]);
        return null;
    }

    private function extractInvoiceNumber(string $text): ?string
    {
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Look for "Invoice No" patterns
            if (preg_match('/\bInvoice\s*(?:No|Number|#)\s*[:\-]?\s*([A-Z0-9\/\-]+)/i', $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
            
            // Look for "Invoice#" patterns
            if (preg_match('/\bInvoice#\s*[:\-]?\s*([A-Z0-9\/\-]+)/i', $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
            
            // Look for patterns like "Invoice No  :  SAPR27005439775"
            if (preg_match('/\bInvoice\s*No\s*[:\-]?\s*([A-Z0-9\/\-]+)/i', $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
            
            // Look for patterns like "Invoice No  : SAPR27005439775"
            if (preg_match('/\bInvoice\s*No\s*[:\s]*([A-Z]{2,4}\d{8,12})/i', $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
            
            // Look for patterns like "Invoice#      : IPY26-27INV/0071"
            if (preg_match('/\bInvoice#\s*[:\-]?\s*([A-Z0-9\/\-]+)/i', $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return null;
    }

    private function extractInvoiceNumberFromFilename(string $filename): ?string
    {
        $name = strtoupper(pathinfo($filename, PATHINFO_FILENAME));
        $name = preg_replace('/\s+/', ' ', $name);

        if (preg_match('/\b(INV[\-\/]?[A-Z0-9\-\/]{2,30})\b/', $name, $m)) {
            return $this->normalizeInvoiceNumberCandidate((string) ($m[1] ?? ''));
        }

        if (preg_match('/\b([A-Z]{1,6}[\-\/]?\d{3,20})\b/', $name, $m)) {
            return $this->normalizeInvoiceNumberCandidate((string) ($m[1] ?? ''));
        }

        return null;
    }

    private function normalizeInvoiceNumberCandidate(string $candidate): ?string
    {
        $candidate = strtoupper(trim($candidate, " \t\n\r\0\x0B:.-_"));
        $candidate = preg_replace('/\s+/', '', $candidate) ?? '';

        if ($candidate === '' || strlen($candidate) < 3 || strlen($candidate) > 45) {
            return null;
        }

        $stopWords = ['INVOICE', 'NUMBER', 'AMOUNT', 'DATE', 'TOTAL', 'GST', 'GSTIN', 'SUPPLY', 'BILL', 'TAX', 'THE', 'OICE'];
        if (in_array($candidate, $stopWords, true)) {
            return null;
        }

        // Invoice number must include at least one digit to avoid label captures.
        if (!preg_match('/\d/', $candidate)) {
            return null;
        }

        // Reject pure date-like strings such as 2026-03-30.
        if (preg_match('/^\d{1,4}[\-\/]\d{1,2}[\-\/]\d{1,4}$/', $candidate)) {
            return null;
        }

        return $candidate;
    }

    private function extractVendorName(string $text): ?string
    {
        $lines = explode("\n", $text);
        
        // Look for company names at document start
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Simple company name patterns
            if (strlen($trimmedLine) > 15 && strlen($trimmedLine) < 100) {
                if (strpos($trimmedLine, 'LIMITED') !== false || 
                    strpos($trimmedLine, 'PRIVATE') !== false ||
                    strpos($trimmedLine, 'COMMUNICATIONS') !== false ||
                    strpos($trimmedLine, 'NETWORKS') !== false ||
                    strpos($trimmedLine, 'SOLUTIONS') !== false ||
                    strpos($trimmedLine, 'CORPORATION') !== false ||
                    strpos($trimmedLine, 'INDUSTRIES') !== false) {
                    
                    // Clean up the vendor name
                    $vendorName = preg_replace('/["""]/', '', $trimmedLine);
                    $vendorName = preg_replace('/\s{2,}/', ' ', $vendorName);
                    $vendorName = trim($vendorName);
                    
                    if (strlen($vendorName) >= 5 && strlen($vendorName) <= 100) {
                        return $vendorName;
                    }
                }
            }
        }
        
        // Look for Bill To sections
        for ($i = 0; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            
            if (stripos($line, 'Bill To') !== false || stripos($line, 'Bill to') !== false) {
                // Check next few lines for vendor name
                for ($j = $i + 1; $j < min($i + 4, count($lines)); $j++) {
                    $nextLine = trim($lines[$j]);
                    
                    if (strlen($nextLine) > 10 && strlen($nextLine) < 100) {
                        if (strpos($nextLine, 'LIMITED') !== false || 
                            strpos($nextLine, 'PRIVATE') !== false ||
                            strpos($nextLine, 'COMMUNICATIONS') !== false ||
                            strpos($nextLine, 'NETWORKS') !== false ||
                            strpos($nextLine, 'SOLUTIONS') !== false) {
                            
                            $vendorName = preg_replace('/["""]/', '', $nextLine);
                            $vendorName = preg_replace('/\s{2,}/', ' ', $vendorName);
                            $vendorName = trim($vendorName);
                            
                            if (strlen($vendorName) >= 5 && strlen($vendorName) <= 100) {
                                return $vendorName;
                            }
                        }
                    }
                    
                    // Stop at address indicators
                    if (stripos($nextLine, 'Address') !== false || 
                        stripos($nextLine, 'GSTIN') !== false ||
                        stripos($nextLine, 'Phone') !== false ||
                        stripos($nextLine, 'Email') !== false) {
                        break;
                    }
                }
            }
        }
        
        return null;
    }

    private function sanitizeVendorCandidate(string $candidate): ?string
    {
        $candidate = trim((string) preg_replace('/\s+/', ' ', $candidate));
        $candidate = preg_split('/\b(?:gst|gstin|invoice|bill|phone|mobile|email|terms|place\s*of\s*supply|address|pin\s*code|pincode|state|country)\b/i', $candidate)[0] ?? $candidate;
        $candidate = preg_split('/\b(?:road|rd\.?|street|st\.?|nagar|lane|ln\.?|floor|flr|district|city|village|near|opp\.?|opposite)\b/i', $candidate)[0] ?? $candidate;
        $candidate = explode(',', $candidate)[0] ?? $candidate;
        $candidate = trim((string) $candidate, " \t\n\r\0\x0B:,-");
        $candidate = trim((string) preg_replace('/\s{2,}/', ' ', $candidate));

        if (strlen($candidate) < 4) {
            return null;
        }

        if (strlen($candidate) > 90 || str_word_count($candidate) > 8) {
            return null;
        }

        if (preg_match('/^(invoice|tax|total|ship\s*to|bill\s*to)$/i', $candidate)) {
            return null;
        }

        if (preg_match('/\b(?:transaction|subject|jurisdiction|courts|details\s+on\s+this\s+form|provided\s+by\s+me\/us)\b/i', $candidate)) {
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
        $lines = explode("\n", $text);
        $amounts = [];
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Look for total amounts
            if (stripos($trimmedLine, 'Total') !== false ||
                stripos($trimmedLine, 'Grand Total') !== false ||
                stripos($trimmedLine, 'Invoice Amount') !== false ||
                stripos($trimmedLine, 'Amount Payable') !== false ||
                stripos($trimmedLine, 'Balance Payable') !== false) {
                
                // Extract numbers from the line
                if (preg_match('/([0-9,]+(?:\.[0-9]{2})?)/', $trimmedLine, $matches)) {
                    $amount = (float) str_replace([',', ' '], '', $matches[1]);
                    if ($amount > 0 && $amount < 1000000) {
                        $amounts[] = $amount;
                    }
                }
            }
            
            // Look for currency symbols
            if (preg_match('/([0-9,]+(?:\.[0-9]{2})?)\s*(Rs|Rs\.|INR|USD|\$|£|¥)/i', $trimmedLine, $matches)) {
                $amount = (float) str_replace([',', ' '], '', $matches[1]);
                if ($amount > 0 && $amount < 1000000) {
                    $amounts[] = $amount;
                }
            }
        }
        
        if (!empty($amounts)) {
            // Return the largest amount (most likely the grand total)
            return max($amounts);
        }
        
        return 0.0;
    }

    private function extractInvoiceDate(string $text): ?string
    {
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Look for Invoice Date
            if (stripos($trimmedLine, 'Invoice Date') !== false) {
                // Extract date from the line
                if (preg_match('/(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/', $trimmedLine, $matches)) {
                    return date('Y-m-d', strtotime($matches[1]));
                }
                
                if (preg_match('/([A-Za-z]+\s+\d{1,2},?\s+\d{4})/', $trimmedLine, $matches)) {
                    return date('Y-m-d', strtotime($matches[1]));
                }
            }
            
            // Look for date patterns at line start
            if (preg_match('/^(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/', $trimmedLine, $matches)) {
                return date('Y-m-d', strtotime($matches[1]));
            }
        }
        
        return null;
    }

    private function extractHeaderDate(object $header): ?string
    {
        $rawDate = $header->date ?? null;
        if (!$rawDate) {
            return null;
        }

        try {
            return Carbon::parse($rawDate)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizeDateString(string $dateValue): ?string
    {
        $dateValue = trim((string) $dateValue);
        $formats = [
            'd-m-Y',
            'd/m/Y',
            'd.m.Y',
            'd-m-y',
            'd/m/y',
            'd.m.y',
            'Y-m-d',
            'Y/m/d',
            'd M Y',
            'd F Y',
            'd M y',
            'd F y',
        ];

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

    private function extractEmail(string $from): ?string
    {
        preg_match('/[\w.+\-]+@[\w\-]+\.[\w.\-]+/', $from, $m);
        return $m[0] ?? null;
    }

    private function extractName(string $from): string
    {
        if (preg_match('/^([^<]+)</', $from, $m)) {
            return trim($m[1]);
        }
        return $from;
    }

    private function isInvoiceKeywordText(string $text): bool
    {
        $text = strtolower($text);
        $keywords = [
            'invoice',
            'tax invoice',
            'bill',
            'gst',
            'gstin',
            'debit note',
            'credit note',
            'proforma',
        ];

        foreach ($keywords as $keyword) {
            if (strpos($text, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function isInvoiceDocument(string $text): bool
    {
        $text = strtolower($text);
        $signals = 0;
        $patterns = [
            '/\binvoice\b/',
            '/\bgstin\b|\bgst\b/',
            '/\bcgst\b|\bsgst\b|\bigst\b/',
            '/\binvoice\s*no\b|\binvoice\s*number\b/',
            '/\btotal\b|\bgrand\s*total\b/',
            '/\bbill\s*to\b|\bship\s*to\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $signals++;
            }
        }

        return $signals >= 2;
    }

    private function loadState(string $stateFile): array
    {
        if (!is_file($stateFile)) {
            return [];
        }

        $json = file_get_contents($stateFile);
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function getUidsSince($inbox, int $lookbackDays): array
    {
        $since = date('d-M-Y', strtotime('-' . $lookbackDays . ' days'));
        $recentMsgNos = imap_search($inbox, "SINCE \"{$since}\"") ?: [];
        $candidateUids = [];

        foreach ($recentMsgNos as $msgNo) {
            $uid = (int) imap_uid($inbox, $msgNo);
            if ($uid > 0) {
                $candidateUids[] = $uid;
            }
        }

        $candidateUids = array_values(array_unique($candidateUids));
        sort($candidateUids, SORT_NUMERIC);

        return $candidateUids;
    }

    private function hasProcessedAttachment(string $attachmentHash): bool
    {
        $emailLogIds = EmailLog::query()
            ->where('file_hash', $attachmentHash)
            ->pluck('id');

        if ($emailLogIds->isEmpty()) {
            return false;
        }

        // Treat as duplicate only when at least one purchase invoice already references the same attachment hash.
        return PurchaseInvoice::query()
            ->whereIn('email_log_id', $emailLogIds)
            ->exists();
    }

    private function findInvoiceByAttachmentHash(string $attachmentHash): ?PurchaseInvoice
    {
        $emailLogIds = EmailLog::query()
            ->where('file_hash', $attachmentHash)
            ->pluck('id');

        if ($emailLogIds->isEmpty()) {
            return null;
        }

        return PurchaseInvoice::query()
            ->whereIn('email_log_id', $emailLogIds)
            ->latest('id')
            ->first();
    }

    private function shouldReprocessForInvoiceNumber(PurchaseInvoice $invoice): bool
    {
        $storedInvoiceNo = strtoupper(trim((string) ($invoice->invoice_no ?? '')));
        if ($storedInvoiceNo === '' || str_starts_with($storedInvoiceNo, 'GMAIL-') || str_starts_with($storedInvoiceNo, 'MAIL-')) {
            return true;
        }

        $rawInvoiceNo = strtoupper(trim((string) data_get($invoice->raw_json, 'invoice_number', '')));

        return $rawInvoiceNo === '';
    }

    private function saveState(string $stateFile, array $state): void
    {
        $dir = dirname($stateFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($stateFile, json_encode($state, JSON_PRETTY_PRINT));
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

    private function withMatchDetails(array $invoiceData, array $matchResult, float $parserConfidence, float $combinedConfidence): array
    {
        $invoiceData['matching'] = [
            'parser_confidence' => $parserConfidence,
            'vendor_match_score' => $matchResult['score'] ?? 0,
            'combined_confidence' => $combinedConfidence,
            'matched_by' => $matchResult['matched_by'] ?? null,
            'gst_match' => $matchResult['gst_match'] ?? false,
            'name_match' => $matchResult['name_match'] ?? false,
            'name_similarity' => $matchResult['name_similarity'] ?? 0,
            'vendor_master_name' => $matchResult['vendor_master_name'] ?? null,
            'vendor_master_display_name' => $matchResult['vendor_master_display_name'] ?? null,
        ];

        return $invoiceData;
    }

    private function findExistingInvoiceForImport(string $invoiceNo, ?string $gst, ?string $invoiceDate = null, ?float $total = null, ?string $vendorName = null): ?PurchaseInvoice
    {
        if ($invoiceNo === '') {
            $invoiceNo = null;
        }

        if ($invoiceNo && $gst) {
            $exact = PurchaseInvoice::query()
                ->where('invoice_no', $invoiceNo)
                ->where(function ($query) use ($gst) {
                    $query->where('gstin', $gst)
                        ->orWhere('gst_number', $gst)
                        ->orWhere('vendor_gstin', $gst);
                })
                ->latest('id')
                ->first();

            if ($exact) {
                return $exact;
            }
        }

        if ($invoiceNo) {
            $byInvoiceNo = PurchaseInvoice::query()
                ->where('invoice_no', $invoiceNo)
                ->latest('id')
                ->first();

            if ($byInvoiceNo) {
                return $byInvoiceNo;
            }
        }

        if ($gst && $invoiceDate) {
            $byGstAndDate = PurchaseInvoice::query()
                ->whereDate('invoice_date', $invoiceDate)
                ->where(function ($query) use ($gst) {
                    $query->where('gstin', $gst)
                        ->orWhere('gst_number', $gst)
                        ->orWhere('vendor_gstin', $gst);
                })
                ->latest('id')
                ->first();

            if ($byGstAndDate) {
                return $byGstAndDate;
            }
        }

        if ($vendorName && $invoiceDate && $total !== null) {
            $normalizedVendor = strtolower(trim($vendorName));
            $roundedTotal = round((float) $total, 2);

            return PurchaseInvoice::query()
                ->whereDate('invoice_date', $invoiceDate)
                ->whereRaw('LOWER(TRIM(COALESCE(vendor_name_raw, vendor_name, ""))) = ?', [$normalizedVendor])
                ->whereRaw('ROUND(COALESCE(total_amount, grand_total, amount, 0), 2) = ?', [$roundedTotal])
                ->latest('id')
                ->first();
        }

        return null;
    }

    private function generateFallbackInvoiceNo(?string $gst, string $invoiceDate, float $total, string $vendorName): string
    {
        $keyParts = [
            strtoupper(trim((string) ($gst ?? 'NO_GST'))),
            trim((string) $invoiceDate) !== '' ? $invoiceDate : 'NO_DATE',
            number_format((float) $total, 2, '.', ''),
            strtoupper(trim($vendorName)) !== '' ? strtoupper(trim($vendorName)) : 'NO_VENDOR',
        ];

        $fingerprint = substr(hash('sha1', implode('|', $keyParts)), 0, 10);

        return 'GMAIL-' . $fingerprint;
    }

    private function applyReimportUpdate(
        PurchaseInvoice $invoice,
        array $invoiceData,
        array $matchResult,
        string $invoiceNo,
        string $invoiceDate,
        ?string $gst,
        float $arc,
        float $otc,
        float $static,
        float $total,
        float $confidence,
        float $parserConfidence,
        string $savedFile,
        int $emailLogId,
        string $attachmentHash,
        string $finalVendorName,
        ?string $vendorFailureReason = null
    ): bool {
        $newPayload = [
            'vendor_id' => $matchResult['vendor']?->id ?? $invoice->vendor_id,
            'vendor_name' => $finalVendorName,
            'vendor_name_raw' => $finalVendorName,
            'invoice_no' => $invoiceNo,
            'invoice_date' => $invoiceDate,
            'amount' => $total,
            'grand_total' => $total,
            'total_amount' => $total,
            'gstin' => $gst,
            'gst_number' => $gst,
            'vendor_gstin' => $gst,
            'arc_amount' => $arc,
            'otc_amount' => $otc,
            'static_amount' => $static,
            'confidence_score' => $confidence,
            'po_invoice_file' => $savedFile,
            'email_log_id' => $emailLogId,
            'status' => $invoice->status,
        ];

        if (in_array(strtolower((string) $invoice->status), ['draft', 'needs_review', 'failed'], true)) {
            $newPayload['status'] = $vendorFailureReason ? 'failed' : 'draft';
        }

        $incomingInvoiceNo = trim((string) ($newPayload['invoice_no'] ?? ''));
        if ($incomingInvoiceNo !== '' && strcasecmp(trim((string) $invoice->invoice_no), $incomingInvoiceNo) !== 0) {
            $conflictExists = PurchaseInvoice::query()
                ->where('invoice_no', $incomingInvoiceNo)
                ->where('id', '!=', $invoice->id)
                ->exists();

            if ($conflictExists) {
                unset($newPayload['invoice_no']);
            }
        }

        $changedFields = $this->collectChangedFields($invoice, $newPayload);

        if (empty($changedFields)) {
            return false;
        }

        $raw = is_array($invoice->raw_json) ? $invoice->raw_json : [];
        $history = data_get($raw, 'value_changes', []);
        if (!is_array($history)) {
            $history = [];
        }

        $history[] = [
            'changed_at' => now()->toDateTimeString(),
            'source' => 'email_reimport',
            'email_log_id' => $emailLogId,
            'attachment_hash' => $attachmentHash,
            'changed_fields' => $changedFields,
        ];

        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        $raw['value_changes'] = $history;
        $raw['last_reimport'] = [
            'changed_at' => now()->toDateTimeString(),
            'parser_confidence' => $parserConfidence,
            'combined_confidence' => $confidence,
            'changed_count' => count($changedFields),
        ];

        if ($vendorFailureReason) {
            $raw['import_failure_reason'] = $vendorFailureReason;
        } else {
            unset($raw['import_failure_reason']);
        }

        $mergedInvoiceData = $this->withMatchDetails($invoiceData, $matchResult, $parserConfidence, $confidence);
        $newPayload['raw_json'] = array_merge($raw, $mergedInvoiceData);

        $invoice->update($newPayload);

        return true;
    }

    private function collectChangedFields(PurchaseInvoice $invoice, array $newPayload): array
    {
        $watchFields = [
            'vendor_name',
            'vendor_name_raw',
            'invoice_no',
            'invoice_date',
            'gstin',
            'gst_number',
            'vendor_gstin',
            'amount',
            'grand_total',
            'total_amount',
            'arc_amount',
            'otc_amount',
            'static_amount',
            'confidence_score',
            'po_invoice_file',
            'status',
        ];

        $changes = [];

        foreach ($watchFields as $field) {
            if (!array_key_exists($field, $newPayload)) {
                continue;
            }

            $oldValue = $invoice->{$field};
            $newValue = $newPayload[$field];

            if ($field === 'invoice_date') {
                $oldValue = $oldValue ? Carbon::parse((string) $oldValue)->toDateString() : null;
                $newValue = $newValue ? Carbon::parse((string) $newValue)->toDateString() : null;
            }

            if (in_array($field, ['amount', 'grand_total', 'total_amount', 'arc_amount', 'otc_amount', 'static_amount', 'confidence_score'])) {
                $oldValue = round((float) ($oldValue ?? 0), 2);
                $newValue = round((float) ($newValue ?? 0), 2);
            }

            if ((string) ($oldValue ?? '') !== (string) ($newValue ?? '')) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function resolveVendorValidationFailure(?string $extractedVendorName, ?Vendor $vendor, array $matchResult): ?string
    {
        $invoiceVendorName = trim((string) $extractedVendorName);
        $isGstMatch = (bool) ($matchResult['gst_match'] ?? false);

        // If GST is matched to a vendor, treat it as authoritative and avoid name mismatch noise.
        if ($vendor && $isGstMatch) {
            return null;
        }

        if (!$vendor) {
            return 'Vendor name mismatch with Vendor Master.';
        }

        if ($invoiceVendorName === '') {
            return null;
        }

        $isNameMatch = (bool) ($matchResult['name_match'] ?? false);
        if ($isNameMatch) {
            return null;
        }

        $masterVendorName = trim((string) ($matchResult['vendor_master_name'] ?? $vendor->vendor_name ?? ''));
        if ($masterVendorName === '') {
            return 'Vendor name mismatch with Vendor Master.';
        }

        return 'Vendor name mismatch with Vendor Master. Invoice: ' . $invoiceVendorName . ' | Master: ' . $masterVendorName;
    }

    private function attachImportFailureReason(array $rawJson, ?string $failureReason): array
    {
        if ($failureReason) {
            $rawJson['import_failure_reason'] = $failureReason;
            return $rawJson;
        }

        unset($rawJson['import_failure_reason']);
        return $rawJson;
    }
}
