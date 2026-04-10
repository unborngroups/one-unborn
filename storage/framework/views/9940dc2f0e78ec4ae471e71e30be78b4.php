<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #111; margin: 0; padding: 20px; }
        .wrap { max-width: 900px; margin: 0 auto; border: 1px solid #ddd; }
        .head { padding: 16px; border-bottom: 1px solid #ddd; }
        .title { float: right; font-size: 20px; font-weight: 700; }
        .company { font-size: 14px; font-weight: 700; }
        .muted { color: #555; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background: #f6f6f6; text-align: left; }
        .section { padding: 16px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <?php
        $invoiceNo = $sales->invoice_no ?? ('INV-' . str_pad($sales->id, 5, '0', STR_PAD_LEFT));
        $clientName = $client->client_name ?? ($sales->client_name ?? '-');
        $clientAddress = trim((string) ($client->address1 ?? ''));
        $clientCity = trim((string) ($client->city ?? ''));
        $clientState = trim((string) ($client->state ?? ''));
        $clientPin = trim((string) ($client->pincode ?? ''));
        $grandTotal = (float) ($sales->grand_total ?? $sales->total_amount ?? 0);
    ?>

    <div class="wrap">
        <div class="head">
            <div class="title">TAX INVOICE</div>
            <div class="company"><?php echo e($company->company_name ?? 'Unborn Group'); ?></div>
            <div class="muted"><?php echo e($company->address ?? ''); ?></div>
            <div class="muted">GSTIN: <?php echo e($company->gstin ?? ''); ?></div>
            <div class="muted">Email: <?php echo e($company->company_email ?? ''); ?></div>
            <div class="clear"></div>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th style="width:50%">Invoice Details</th>
                    <th style="width:50%">Billing Details</th>
                </tr>
                <tr>
                    <td>
                        <strong>Invoice #:</strong> <?php echo e($invoiceNo); ?><br>
                        <strong>Invoice Date:</strong> <?php echo e($sales->invoice_date); ?><br>
                        <strong>Due Date:</strong> <?php echo e($sales->due_date ?? '-'); ?>

                    </td>
                    <td>
                        <strong><?php echo e($clientName); ?></strong><br>
                        <?php echo e($clientAddress); ?><br>
                        <?php echo e($clientCity); ?> <?php echo e($clientState); ?> <?php echo e($clientPin); ?><br>
                        GSTIN: <?php echo e($client->gstin ?? ($sales->client_gstin ?? '-')); ?>

                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Description</th>
                    <th class="right">Amount</th>
                </tr>
                <tr>
                    <td>Sales Invoice <?php echo e($invoiceNo); ?></td>
                    <td class="right"><?php echo e(number_format($grandTotal, 2)); ?></td>
                </tr>
                <tr>
                    <th class="right">Grand Total</th>
                    <th class="right"><?php echo e(number_format($grandTotal, 2)); ?></th>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\emails\sales_invoice.blade.php ENDPATH**/ ?>