<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Vendor;
use App\Models\VendorInvoice;
use App\Models\VendorInvoiceItem;
use App\Services\InvoiceParserService;

class InvoiceParserServices
{
    public function parse($path)
    {
        $ocr = app(OCRServices::class)->extract($path);

        if ($ocr['confidence'] >= config('invoice.confidence_threshold')) {
            return $ocr;
        }

        return $this->fallback($path);
    }

    private function fallback($path)
    {
        $pdf = (new \Smalot\PdfParser\Parser())->parseFile($path);
        $text = $pdf->getText();

        preg_match(config('invoice.regex_patterns.invoice_number'), $text, $inv);

        return [
            'vendor_name' => 'Unknown',
            'invoice_number' => $inv[1] ?? null,
            'confidence' => 0.5
        ];
    }
}