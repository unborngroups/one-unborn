<?php

namespace App\Services\Pdf;

use App\Models\Invoice;

class InvoicePdfService
{
    public function generate(Invoice $invoice)
    {
        $invoice->load(['company', 'client', 'items']);

        $html = view('finance.invoices.pdf', compact('invoice'))->render();

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }
}
