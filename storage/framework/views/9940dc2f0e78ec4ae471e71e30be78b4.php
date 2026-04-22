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
        $comp        = $feasibility->company ?? $company ?? null;
        $logo        = $comp->company_logo ?? null;
        $deliverablePlan = $deliverables->deliverablePlan ?? null;
        $taxable     = $deliverables->purchaseOrder->arc_per_link ?? 0;
        $igstPct     = 18;
        $cgstPct     = 9;
        $sgstPct     = 9;
        $companyGstin = $comp->gstin ?? '';
        $clientGstin = $feasibility->client->gstin ?? '';
        $companyStateCode = preg_match('/^\d{2}/', $companyGstin, $matches) ? $matches[0] : null;
        $clientStateCode = preg_match('/^\d{2}/', $clientGstin, $matches) ? $matches[0] : null;
        $companyState = strtolower(trim($comp->state ?? ''));
        $clientState = strtolower(trim($feasibility->client->state ?? ''));
        $isSameState = ($companyStateCode && $clientStateCode)
            ? $companyStateCode === $clientStateCode
            : ($companyState && $clientState ? $companyState === $clientState : true);
        $cgstAmt     = $isSameState ? (($taxable * $cgstPct) / 100) : 0;
        $sgstAmt     = $isSameState ? (($taxable * $sgstPct) / 100) : 0;
        $igstAmt     = $isSameState ? 0 : (($taxable * $igstPct) / 100);
        $taxAmount   = $cgstAmt + $sgstAmt + $igstAmt;
        $invoiceTotal = $taxable + $taxAmount;
    ?>

    <div class="wrap">
        <div class="head">
            <div class="title">TAX INVOICE</div>
            <div class="company"><?php echo e($comp->company_name ?? 'Unborn Group'); ?></div>
            <div class="muted"><?php echo e($comp->address ?? ''); ?></div>
            <div class="muted">GSTIN: <?php echo e($comp->gstin ?? ''); ?></div>
            <div class="muted">Email: <?php echo e($comp->company_email ?? ''); ?></div>
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
                        <strong>Invoice #:</strong> <?php echo e($sales->invoice_no); ?><br>
                        <strong>Invoice Date:</strong> <?php echo e($sales->invoice_date ? \Carbon\Carbon::parse($sales->invoice_date)->format('d/m/Y') : ''); ?><br>
                        <strong>Terms:</strong> Net 30<br>
                        <strong>Due Date:</strong> <?php echo e($sales->due_date ? \Carbon\Carbon::parse($sales->due_date)->format('d/m/Y') : ''); ?><br>
                        <strong>P.O. #:</strong> <?php echo e($deliverables->purchaseOrder->po_number ?? ''); ?>

                    </td>
                    <td>
                        <strong><?php echo e($feasibility->client->client_name ?? ''); ?></strong><br>
                        <?php echo e($feasibility->client->address1 ?? ''); ?><br>
                        <?php echo e($feasibility->client->city ?? ''); ?>, <?php echo e($feasibility->client->state ?? ''); ?> – <?php echo e($feasibility->client->pincode ?? ''); ?><br>
                        GSTIN: <?php echo e($feasibility->client->gstin ?? ''); ?>

                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Place Of Supply</th>
                    <th>Service ID</th>
                    <th>UNBORN Service ID</th>
                    <th>Feasibility ID</th>
                    <th>Vendor ID</th>
                    <th>Invoice From Date</th>
                    <th>Invoice To Date</th>
                </tr>
                <tr>
                    <td><?php echo e($feasibility->client->state ?? ''); ?></td>
                    <td><?php echo e($sales->service_id ?? ''); ?></td>
                    <td><?php echo e($deliverablePlan->circuit_id ?? ''); ?></td>
                    <td><?php echo e($deliverables->feasibility->feasibility_request_id ?? ''); ?></td>
                    <td><?php echo e($deliverablePlan->vendor_code ?? ''); ?></td>
                    <td><?php echo e($deliverablePlan->date_of_activation ?? ''); ?></td>
                    <td><?php echo e($deliverablePlan->date_of_expiry ?? ''); ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <strong>Subject:</strong>
            Invoice for Order ID: <?php echo e($sales->order_id ?? ''); ?>

            &nbsp;|&nbsp; Client Code: <?php echo e($deliverables->deliverablePlan->client_code ?? ''); ?>

        </div>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item & Description</th>
                        <th>HSN/SAC</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Taxable Amount</th>
                        <?php if($isSameState): ?>
                        <th>CGST %</th>
                        <th>CGST Amt</th>
                        <th>SGST %</th>
                        <th>SGST Amt</th>
                        <?php else: ?>
                        <th>IGST %</th>
                        <th>IGST Amt</th>
                        <th>Tax</th>
                        <th>-</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $salesItems = collect($sales->items ?? []);
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $salesItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($k + 1); ?></td>
                        <td><?php echo e($item->item_name ?? 'N/A'); ?></td>
                        <td><?php echo e($item->hsn_sac_code ?? '995423'); ?></td>
                        <td>1</td>
                        <td><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td><?php echo e(number_format($taxable, 2)); ?></td>
                        <?php if($isSameState): ?>
                        <td><?php echo e($cgstPct); ?>%</td>
                        <td><?php echo e(number_format($cgstAmt, 2)); ?></td>
                        <td><?php echo e($sgstPct); ?>%</td>
                        <td><?php echo e(number_format($sgstAmt, 2)); ?></td>
                        <?php else: ?>
                        <td><?php echo e($igstPct); ?>%</td>
                        <td><?php echo e(number_format($igstAmt, 2)); ?></td>
                        <td>-</td>
                        <td>-</td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td>1</td>
                        <td>Recurring Charges</td>
                        <td>995423</td>
                        <td>1</td>
                        <td><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td><?php echo e(number_format($taxable, 2)); ?></td>
                        <?php if($isSameState): ?>
                        <td><?php echo e($cgstPct); ?>%</td>
                        <td><?php echo e(number_format($cgstAmt, 2)); ?></td>
                        <td><?php echo e($sgstPct); ?>%</td>
                        <td><?php echo e(number_format($sgstAmt, 2)); ?></td>
                        <?php else: ?>
                        <td><?php echo e($igstPct); ?>%</td>
                        <td><?php echo e(number_format($igstAmt, 2)); ?></td>
                        <td>-</td>
                        <td>-</td>
                        <?php endif; ?>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="5" class="tr"><strong>Sub Total</strong></td>
                        <td><?php echo e(number_format($taxable, 2)); ?></td>
                        <td>₹<?php echo e(number_format($taxable, 2)); ?></td>
                        <?php if($isSameState): ?>
                        <td></td>
                        <td><?php echo e(number_format($cgstAmt, 2)); ?></td>
                        <td></td>
                        <td><?php echo e(number_format($sgstAmt, 2)); ?></td>
                        <?php else: ?>
                        <td></td>
                        <td><?php echo e(number_format($igstAmt, 2)); ?></td>
                        <td></td>
                        <td>-</td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <table style="width:100%">
                <tr>
                    <td style="width:60%;vertical-align:top;">
                        <div><strong>Total in Words:</strong> <?php echo e($sales->total_in_words ?? '---'); ?></div>
                        <div><strong>Notes:</strong> <?php echo e($sales->notes ?? 'Thanks for your business.'); ?></div>
                        <div><strong>Terms & Conditions / Payment Details:</strong><br>
                            <strong>Account Name:</strong> <?php echo e($comp->company_name ?? ''); ?><br>
                            <strong>Account Number:</strong> <?php echo e($comp->account_number ?? ''); ?><br>
                            <strong>IFSC Code:</strong> <?php echo e($comp->ifsc_code ?? ''); ?><br>
                            <strong>Branch & Bank:</strong> <?php echo e($comp->branch_name ?? ''); ?>, <?php echo e($comp->bank_name ?? ''); ?>

                        </div>
                    </td>
                    <td style="width:40%;vertical-align:top;">
                        <table style="width:100%">
                            <tr><td>Taxable Amount</td><td class="tr">₹<?php echo e(number_format($taxable, 2)); ?></td></tr>
                            <?php if($isSameState): ?>
                            <tr><td>CGST (<?php echo e($cgstPct); ?>%)</td><td class="tr">₹<?php echo e(number_format($cgstAmt, 2)); ?></td></tr>
                            <tr><td>SGST (<?php echo e($sgstPct); ?>%)</td><td class="tr">₹<?php echo e(number_format($sgstAmt, 2)); ?></td></tr>
                            <?php else: ?>
                            <tr><td>IGST (<?php echo e($igstPct); ?>%)</td><td class="tr">₹<?php echo e(number_format($igstAmt, 2)); ?></td></tr>
                            <?php endif; ?>
                            <tr><td><strong>Total</strong></td><td class="tr"><strong>₹<?php echo e(number_format($invoiceTotal, 2)); ?></strong></td></tr>
                            <tr><td><strong>Balance Due</strong></td><td class="tr"><strong>₹<?php echo e(number_format($invoiceTotal, 2)); ?></strong></td></tr>
                        </table>
                        <div style="margin-top:24px;text-align:center;">
                            <div style="border-top:1px solid #aaa;padding-top:8px;">Authorized Signature</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\emails\sales_invoice.blade.php ENDPATH**/ ?>