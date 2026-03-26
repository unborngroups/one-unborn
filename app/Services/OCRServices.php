<?php

namespace App\Services;


class OCRServices
{
    public function extract($path)
    {
        // Simulated OCR response (replace with AWS Textract later)
        $text = $this->getRawText($path);

        preg_match_all('/\b[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}\b/', $text, $matches);

        $gstins = $matches[0] ?? [];

        return [
            'vendor_name' => $this->extractVendorName($text),
            'vendor_gstin' => $this->identifyVendorGSTIN($gstins, $text),
            'buyer_gstin' => $this->identifyBuyerGSTIN($gstins, $text),
            'invoice_number' => $this->extractInvoiceNumber($text),
            'amount' => $this->extractAmount($text),
            'tax' => $this->extractTax($text),
            'total' => $this->extractTotal($text),
            'all_gstins' => $gstins,
            'confidence' => $this->calculateConfidence($gstins)
        ];
    }
}