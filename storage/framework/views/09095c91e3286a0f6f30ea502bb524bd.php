<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; page-break-inside: avoid; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .no-border { border: none !important; }
        .text-end { text-align: right; }
        .signature { height: 80px; text-align: center; vertical-align: bottom; }
    </style>
</head>
<body>
    
            <div class="row">
                <div class="col-md-2">
                    <?php
                        $logo = $feasibility->company->company_logo ?? $company->company_logo ?? null;
                    ?>
                    <?php if($logo && file_exists(public_path('images/companylogos/' . $logo))): ?>
                        <img src="<?php echo e(asset('images/companylogos/' . $logo)); ?>" alt="Company Logo" style="max-width: 100px; max-height: 100px;">
                    <?php else: ?>
                        <div style="width:100px; height:100px; background:#ccc; display:flex; align-items:center; justify-content:center;">No Logo</div>
                    <?php endif; ?>
                </div>

                <div class="col-md-7 text-center" style="width: 350px;">
                    <h5 class="mb-1"><strong><?php echo e($feasibility->company->company_name ?? ''); ?></strong></h5>
                    Company ID: <?php echo e($feasibility->company->company_id ?? ''); ?> <br>
                    <?php echo e($feasibility->company->address ?? ''); ?> <br>
                    GSTIN: <?php echo e($feasibility->company->gstin ?? ''); ?> <br>
                    Phone: 04341222226 / 9688862676 <br>
                    Email: <?php echo e($feasibility->company->company_email ?? ''); ?>

                </div>

                <div class="col-md-3 text-end">
                    <h3 class="fw-bold">TAX INVOICE</h3>
                </div>
            </div>

    <table>
        
    <tr>

    
        <!-- LEFT SIDE -->
        <td style="width:50%; text-align: left;" >
            
            <strong>Invoice #</strong> : <span><?php echo e($invoice->invoice_no); ?></span> <br>
            <strong>Invoice Date</strong> : <span><?php echo e($invoice->invoice_date); ?> </span> <br>
            <strong>Terms</strong> : <span>Net 30</span><br>
            <strong>Due Date</strong> : <span><?php echo e($invoice->due_date); ?></span> <br>
            <strong>P.O #</strong> : <span><?php echo e($deliverables->purchaseOrder->po_number ?? ''); ?></span>
        </td>

        <!-- RIGHT SIDE -->
        <td style="width:50%; text-align: left;">
            <strong>Place Of Supply</strong> : <?php echo e($feasibility->client->state ?? ''); ?> <br>
            <strong>Service ID</strong> : <?php echo e($invoice->service_id ?? ''); ?> <br>
            <strong>UNBORN Service ID/Circuit_id</strong> : <?php echo e($invoice->service_id ?? ''); ?> | <?php echo e($deliverablePlan->circuit_id ?? ''); ?> <br>
            <strong>Feasibility ID</strong> : <?php echo e($deliverables->feasibility->feasibility_request_id ?? ''); ?> <br>
            <strong>Vendor ID</strong> : <?php echo e($deliverablePlan->vendor_code ?? ''); ?>

        </td>
    </tr>

    <!-- BILL TO / SHIP TO HEADER -->
    <tr class="table-secondary">
        <td><strong>Bill To</strong></td>
        <td><strong>Ship To</strong></td>
    </tr>

    <!-- BILL TO / SHIP TO DATA -->
    <tr>
        <td>
            <strong><?php echo e($feasibility->client->client_name ?? ''); ?></strong><br>
            <?php echo e($feasibility->client->client_name ?? ''); ?><br>
            <?php echo e($feasibility->client->address1 ?? ''); ?><br>
            <?php echo e($feasibility->client->city ?? ''); ?>,
            <?php echo e($feasibility->client->state ?? ''); ?> -
            <?php echo e($feasibility->client->pincode ?? ''); ?><br>
            GSTIN: <?php echo e($feasibility->client->gstin ?? ''); ?>

        </td>

        <td class="Ship">
            <?php echo e($feasibility->client->client_name ?? ''); ?><br>
            <?php echo e($feasibility->address ?? ''); ?><br>
            <?php echo e($feasibility->city ?? ''); ?>,
            <?php echo e($feasibility->district ?? ''); ?>,
            <?php echo e($feasibility->state ?? ''); ?> -
            <?php echo e($feasibility->pincode ?? ''); ?><br>
            GSTIN: <?php echo e($feasibility->client->gstin ?? ''); ?>

        </td>
    </tr>
</table>
            <br>

            
            <p>
                <strong>Subject:</strong>
                Invoice for Order ID: <?php echo e($invoice->order_id ?? ''); ?>

                | Vendor Code: <?php echo e($deliverables->deliverablePlan->vendor_code ?? ''); ?>

            </p>

            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Item & Description</th>
                            <th rowspan="2">HSN/SAC</th>
                            <th rowspan="2">Qty</th>
                            <th rowspan="2">Rate</th>
                            <th rowspan="2">Amount</th>
                            <th rowspan="2">Taxable Amount</th>
                            <th colspan="2">CGST</th>
                            <th colspan="2">SGST</th>
                        </tr>
                        <tr>
                            <th>%</th>
                            <th>Amt</th>
                            <th>%</th>
                            <th>Amt</th>
                        </tr>
                    </thead>

                    <?php
    $taxable = $deliverables->purchaseOrder->arc_per_link ?? 0;
    $cgstPercent = 9;
    $sgstPercent = 9;

    $cgstAmount = ($taxable * $cgstPercent) / 100;
    $sgstAmount = ($taxable * $sgstPercent) / 100;
?>
<!-- 995423 -->
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?php echo e($item ? $item->item_name : 'N/A'); ?></td>
                            <td><?php echo e($item ? $item->hsn_sac_code : 'N/A'); ?></td>
                            <td></td>
                            <td><?php echo e($deliverables->purchaseOrder->arc_per_link); ?></td>
                            <td><?php echo e($deliverables->purchaseOrder->arc_per_link); ?></td>
                            <td><?php echo e($deliverables->purchaseOrder->arc_per_link); ?></td>
                            <td><?php echo e($cgstPercent); ?>%</td>
                            <td><?php echo e(number_format($cgstAmount,2)); ?></td>
                            <td><?php echo e($sgstPercent); ?>%</td>
                            <td><?php echo e(number_format($sgstAmount,2)); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
            <div class="invoice-bottom">

                <!-- LEFT -->
                <div class="left-box">
                    <p><strong>Total in Words</strong><br>
                    <?php echo e($invoice->total_in_words ?? '---'); ?></p>

                    <p><strong>Notes</strong><br>
                    Thanks for your business.</p>

                    <p><strong>Terms & Conditions</strong><br>
                        Payment Details: <?php echo e($feasibility->company->company_name); ?><br>
                        Account Number : <?php echo e($feasibility->company->account_number ?? ''); ?><br>
                        IFSC Code : <?php echo e($feasibility->company->ifsc_code ?? ''); ?><br>
                        Branch & Bank : <?php echo e($feasibility->company->branch_name ?? ''); ?>,
                        <?php echo e($feasibility->company->bank_name ?? ''); ?>

                    </p>
                </div>

                <!-- RIGHT -->
                <!-- RIGHT SIDE -->
       <!-- RIGHT SIDE -->
        <div style="width:35%; padding:0;">
            <table style="width:100%;">
                <tr>
                    <td>Total</td>
                    <td class="text-end"><strong><?php echo e(number_format($invoice->total_amount,2)); ?></strong></td>
                </tr>
                <tr>
                    <td><strong>Balance Due</strong></td>
                    <td class="text-end"><strong><?php echo e(number_format($invoice->total_amount,2)); ?></strong></td>
                </tr>
                <tr>
                    <td colspan="2" style="height:80px; text-align:center; vertical-align:bottom;">
                        Authorized Signature
                    </td>
                </tr>
            </table>
        </div>

            </div>
        </div>
    </div>
    
</div>

<style>
.card {
    font-size: 14px;
}

.table th, .table td {
    vertical-align: middle;
    font-size: 13px;
}

span{
    text-align: right;
}

.invoice-bottom {
    display: flex;
    margin-top: 20px;
}

.left-box {
    width: 60%;
}

.right-box {
    width: 40%;
    border: 1px solid #000;
    padding: 10px;
}

.right-box table td {
    border: none;
    padding: 5px;
}

.signature {
    margin-top: 40px;
    text-align: center;
}

.card{
    border-color: #000;
    border-radius: 1px;
    border-style: solid;
    width: 70%;
    margin-left: 15%;
}
</style>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\invoices\pdf.blade.php ENDPATH**/ ?>