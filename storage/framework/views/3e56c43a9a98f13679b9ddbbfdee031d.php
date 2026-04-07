

<?php $__env->startSection('content'); ?>

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

<div class="inv-wrapper">

    
    <div class="inv-actions no-print">
        <a href="<?php echo e(route('finance.sales.index')); ?>" class="inv-btn inv-btn-back">← Back</a>
        <button onclick="window.print()" class="inv-btn inv-btn-print">🖨 Print</button>
    </div>

    <div class="inv-box">

        
        <div class="inv-header">
            <div class="inv-company-block">
                <div class="inv-logo-wrap">
                    <?php if($logo && file_exists(public_path('images/companylogos/' . $logo))): ?>
                        <img src="<?php echo e(asset('images/companylogos/' . $logo)); ?>" alt="Logo" width="550px" height="550px">
                    <?php else: ?>
                        <div class="inv-logo-placeholder">LOGO</div>
                    <?php endif; ?>
                </div>
                <div class="inv-company-text">
                    <div class="inv-company-name"><?php echo e($comp->company_name ?? ''); ?></div>
                    <?php if($comp->company_id ?? ''): ?><div class="inv-company-meta">Company ID : <?php echo e($comp->company_id); ?></div><?php endif; ?>
                    <div class="inv-company-meta"><?php echo e($comp->address ?? ''); ?></div>
                    <div class="inv-company-meta">GSTIN: <strong><?php echo e($comp->gstin ?? ''); ?></strong></div>
                    <div class="inv-company-meta">Phone: <?php echo e($comp->company_phone ?? '04341222226'); ?></div>
                    <div class="inv-company-meta">
                        <?php if($comp->company_email ?? ''): ?><?php echo e($comp->company_email); ?><?php endif; ?>
                        <?php if($comp->website ?? ''): ?> &nbsp;|&nbsp; <?php echo e($comp->website); ?><?php endif; ?>
                    </div>
                    <div class="inv-company-meta"> MSME:<?php echo e($comp->msme_id ?? ''); ?></div>
                </div>
            </div>
            <div class="inv-title-block">
                <div class="inv-title-badge">TAX INVOICE</div>
            </div>
        </div>

        
        <table class="inv-meta-table">
            <tr>
                <td class="inv-meta-left">
                    <div class="inv-row"><span class="inv-lbl">Invoice #</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($sales->invoice_no); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Invoice Date</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($sales->invoice_date ? \Carbon\Carbon::parse($sales->invoice_date)->format('d/m/Y') : ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Terms</span><span class="inv-sep">:</span><span class="inv-val">Net 30</span></div>
                    <div class="inv-row"><span class="inv-lbl">Due Date</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($sales->due_date ? \Carbon\Carbon::parse($sales->due_date)->format('d/m/Y') : ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">P.O. #</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($deliverables->purchaseOrder->po_number ?? ''); ?></span></div>
                </td>
                <td class="inv-meta-right">
                    <div class="inv-row"><span class="inv-lbl">Place Of Supply</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($feasibility->client->state ?? ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Service ID</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($sales->service_id ?? ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">UNBORN Service ID</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($deliverablePlan->circuit_id ?? ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Feasibility ID</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($deliverables->feasibility->feasibility_request_id ?? ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Vendor ID</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($deliverablePlan->vendor_code ?? ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Invoice From Date</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($deliverablePlan->date_of_activation ?? ''); ?></span></div>
                    <div class="inv-row"><span class="inv-lbl">Invoice To Date</span><span class="inv-sep">:</span><span class="inv-val"><?php echo e($deliverablePlan->date_of_expiry ?? ''); ?></span></div>
                </td>
            </tr>
        </table>

        
        <table class="inv-addr-table">
            <tr class="inv-addr-header">
                <td>Bill To</td>
                <td>Ship To</td>
            </tr>
            <tr>
                <td>
                    <strong><?php echo e($feasibility->client->client_name ?? ''); ?></strong><br>
                    <?php echo e($feasibility->client->address1 ?? ''); ?><br>
                    <?php echo e($feasibility->client->city ?? ''); ?>,
                    <?php echo e($feasibility->client->state ?? ''); ?> –
                    <?php echo e($feasibility->client->pincode ?? ''); ?><br>
                    <span class="inv-gstin-tag">GSTIN:</span> <?php echo e($feasibility->client->gstin ?? ''); ?>

                </td>
                <td>
                    <strong><?php echo e($feasibility->client->client_name ?? ''); ?></strong><br>
                    <?php echo e($feasibility->address ?? ''); ?><br>
                    <?php echo e($feasibility->city ?? ''); ?>,
                    <?php echo e($feasibility->district ?? ''); ?>,
                    <?php echo e($feasibility->state ?? ''); ?> –
                    <?php echo e($feasibility->pincode ?? ''); ?><br>
                    <span class="inv-gstin-tag">GSTIN:</span> <?php echo e($feasibility->client->gstin ?? ''); ?>

                </td>
            </tr>
        </table>

        
        <div class="inv-subject">
            <strong>Subject:</strong>
            Invoice for Order ID: <?php echo e($sales->order_id ?? ''); ?>

            &nbsp;|&nbsp; Client Code: <?php echo e($deliverables->deliverablePlan->client_code ?? ''); ?>

        </div>

        
        <div class="inv-items-wrap">
            <table class="inv-items">
                <thead>
                    <tr>
                        <th rowspan="2" class="c-n">#</th>
                        <th rowspan="2" class="c-desc">Item &amp; Description</th>
                        <th rowspan="2" class="c-hsn">HSN/SAC</th>
                        <th rowspan="2" class="c-qty">Qty</th>
                        <th rowspan="2" class="c-rate">Rate</th>
                        <th rowspan="2" class="c-amt">Amount</th>
                        <th rowspan="2" class="c-tax">Taxable Amount</th>
                        <?php if($isSameState): ?>
                        <th colspan="2" class="c-span">CGST</th>
                        <th colspan="2" class="c-span">SGST</th>
                        <?php else: ?>
                        <th colspan="2" class="c-span">IGST</th>
                        <th colspan="2" class="c-span">Tax</th>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <th>%</th><th>Amt</th>
                        <th>%</th><th>Amt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $salesItems = collect($sales->items ?? []);
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $salesItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="tc"><?php echo e($k + 1); ?></td>
                        <td class="tl"><?php echo e($item->item_name ?? 'N/A'); ?></td>
                        <td class="tc"><?php echo e($item->hsn_sac_code ?? '995423'); ?></td>
                        <td class="tc">1</td>
                        <td class="tr"><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td class="tr"><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td class="tr"><?php echo e(number_format($taxable, 2)); ?></td>
                        <?php if($isSameState): ?>
                        <td class="tc"><?php echo e($cgstPct); ?>%</td>
                        <td class="tr"><?php echo e(number_format($cgstAmt, 2)); ?></td>
                        <td class="tc"><?php echo e($sgstPct); ?>%</td>
                        <td class="tr"><?php echo e(number_format($sgstAmt, 2)); ?></td>
                        <?php else: ?>
                        <td class="tc"><?php echo e($igstPct); ?>%</td>
                        <td class="tr"><?php echo e(number_format($igstAmt, 2)); ?></td>
                        <td class="tc">-</td>
                        <td class="tr">-</td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td class="tc">1</td>
                        <td class="tl">Recurring Charges</td>
                        <td class="tc">995423</td>
                        <td class="tc">1</td>
                        <td class="tr"><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td class="tr"><?php echo e(number_format($deliverables->purchaseOrder->arc_per_link ?? 0, 2)); ?></td>
                        <td class="tr"><?php echo e(number_format($taxable, 2)); ?></td>
                        <?php if($isSameState): ?>
                        <td class="tc"><?php echo e($cgstPct); ?>%</td>
                        <td class="tr"><?php echo e(number_format($cgstAmt, 2)); ?></td>
                        <td class="tc"><?php echo e($sgstPct); ?>%</td>
                        <td class="tr"><?php echo e(number_format($sgstAmt, 2)); ?></td>
                        <?php else: ?>
                        <td class="tc"><?php echo e($igstPct); ?>%</td>
                        <td class="tr"><?php echo e(number_format($igstAmt, 2)); ?></td>
                        <td class="tc">-</td>
                        <td class="tr">-</td>
                        <?php endif; ?>
                    </tr>
                    <?php endif; ?>
                    
                    <tr class="inv-subtotal-row">
                        <td colspan="5" class="tr" style="border:1px solid #dde3ec;"><strong>Sub Total</strong></td>
                        <td class="tr"><?php echo e(number_format($taxable, 2)); ?></td>
                        <td class="tr">₹<?php echo e(number_format($taxable, 2)); ?></td>
                        <?php if($isSameState): ?>
                        <td class="tc"></td>
                        <td class="tr"><?php echo e(number_format($cgstAmt, 2)); ?></td>
                        <td class="tc"></td>
                        <td class="tr"><?php echo e(number_format($sgstAmt, 2)); ?></td>
                        <?php else: ?>
                        <td class="tc"></td>
                        <td class="tr"><?php echo e(number_format($igstAmt, 2)); ?></td>
                        <td class="tc"></td>
                        <td class="tr">-</td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        
        <div class="inv-bottom">

            
            <div class="inv-bottom-left">
                <div class="inv-words">
                    <div class="inv-section-title">Total in Words</div>
                    <div class="inv-words-text"><?php echo e($sales->total_in_words ?? '---'); ?></div>
                </div>

                <div class="inv-notes-block">
                    <div class="inv-section-title">Notes</div>
                    <div><?php echo e($sales->notes ?? 'Thanks for your business.'); ?></div>
                </div>

                <div class="inv-terms-block">
                    <div class="inv-section-title">Terms &amp; Conditions / Payment Details</div>
                    <div>
                        <strong>Account Name:</strong> <?php echo e($comp->company_name ?? ''); ?><br>
                        <strong>Account Number:</strong> <?php echo e($comp->account_number ?? ''); ?><br>
                        <strong>IFSC Code:</strong> <?php echo e($comp->ifsc_code ?? ''); ?><br>
                        <strong>Branch &amp; Bank:</strong> <?php echo e($comp->branch_name ?? ''); ?>, <?php echo e($comp->bank_name ?? ''); ?>

                    </div>
                </div>
            </div>

            
            <div class="inv-bottom-right">
                <table class="inv-totals">
                    <tr>
                        <td>Taxable Amount</td>
                        <td class="tr">₹<?php echo e(number_format($taxable, 2)); ?></td>
                    </tr>
                    <?php if($isSameState): ?>
                    <tr>
                        <td>CGST (<?php echo e($cgstPct); ?>%)</td>
                        <td class="tr">₹<?php echo e(number_format($cgstAmt, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>SGST (<?php echo e($sgstPct); ?>%)</td>
                        <td class="tr">₹<?php echo e(number_format($sgstAmt, 2)); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td>IGST (<?php echo e($igstPct); ?>%)</td>
                        <td class="tr">₹<?php echo e(number_format($igstAmt, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="inv-total-row">
                        <td>Total</td>
                        <td class="tr">₹<?php echo e(number_format($invoiceTotal, 2)); ?></td>
                    </tr>
                    <tr class="inv-balance-row">
                        <td><strong>Balance Due</strong></td>
                        <td class="tr"><strong>₹<?php echo e(number_format($invoiceTotal, 2)); ?></strong></td>
                    </tr>
                </table>

                <div class="inv-sig">
                    <div class="inv-sig-inner">Authorized Signature</div>
                </div>
            </div>

        </div>

    </div>

    
    <div class="inv-actions no-print" style="margin-top:16px;">
        <a href="<?php echo e(route('finance.sales.index')); ?>" class="inv-btn inv-btn-back">← Back</a>
        <button onclick="window.print()" class="inv-btn inv-btn-print">🖨 Print</button>
    </div>

</div>

<style>
.inv-wrapper * { box-sizing: border-box; }

.inv-wrapper {
    max-width: 780px;
    margin: 20px auto;
    padding: 0 12px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #111;
}

.inv-box {
    background: #fff;
    border: 1px solid #9ea7b3;
    box-shadow: none;
}

.inv-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 10px 14px;
    border-bottom: 1px solid #9ea7b3;
}

.inv-company-block {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.inv-logo-wrap img {
    max-width: 84px;
    max-height: 52px;
    object-fit: contain;
}

.inv-logo-placeholder {
    width: 78px;
    height: 52px;
    border: 1px dashed #b3b3b3;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888;
    font-size: 9px;
}

.inv-company-name {
    font-size: 16px;
    font-weight: 700;
    color: #111;
    margin-bottom: 1px;
}

.inv-company-meta {
    font-size: 10px;
    line-height: 1.35;
    color: #222;
}

.inv-title-badge {
    display: inline-block;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #111;
    border: 1px solid #4a4a4a;
    padding: 6px 14px;
}

.inv-meta-table,
.inv-addr-table,
.inv-items,
.inv-totals {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.inv-meta-table {
    border-bottom: 1px solid #aab2bc;
}

.inv-meta-table td {
    width: 50%;
    padding: 6px 12px;
    vertical-align: top;
    border-right: 1px solid #aab2bc;
}

.inv-meta-table td:last-child,
.inv-addr-table td:last-child {
    border-right: none;
}

.inv-row {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 2px;
    line-height: 1.3;
    font-size: 10px;
}

.inv-lbl {
    min-width: 120px;
    font-weight: 600;
}

.inv-sep,
.inv-val {
    color: #111;
}

.inv-addr-table {
    border-bottom: 1px solid #aab2bc;
}

.inv-addr-table td {
    width: 50%;
    padding: 6px 10px;
    vertical-align: top;
    border-right: 1px solid #aab2bc;
    border-top: 1px solid #aab2bc;
    line-height: 1.45;
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    color: #111;
}

.inv-addr-header td {
    background: #b5b8be ;
    color: #0a0909 ;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .2px;
    padding: 4px 10px;
}

.inv-gstin-tag {
    font-weight: 700;
    color: #111;
}

.inv-subject {
    padding: 6px 10px;
    border-bottom: 1px solid #aab2bc;
    font-size: 10px;
    background: #fff;
}

.inv-items-wrap {
    border-bottom: 1px solid #aab2bc;
}

.inv-items th {
    background:  #b5b8be ;
    color: #0a0909 ;
    border: 1px solid #445783;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 4px;
    text-align: center;
}

.inv-items td {
    border: 1px solid #aab2bc;
    background: #fff;
    font-size: 10px;
    padding: 4px 4px;
}

.inv-items tbody tr:nth-child(even) td,
.inv-items tbody tr:hover td {
    background: #fff;
}

.inv-items .c-n { width: 4%; }
.inv-items .c-desc { width: 22%; }
.inv-items .c-hsn { width: 8%; }
.inv-items .c-qty { width: 5%; }
.inv-items .c-rate { width: 8%; }
.inv-items .c-amt { width: 8%; }
.inv-items .c-tax { width: 10%; }
.inv-items .c-span { width: 10%; }

.tc { text-align: center !important; }
.tl { text-align: left !important; }
.tr { text-align: right !important; }

.inv-no-items {
    color: #8e8e8e;
    padding: 6px;
    font-style: italic;
}

.inv-subtotal-row td {
    background: #fff;
    font-weight: 700;
    font-size: 10px;
    border-top: 1px solid #aab2bc;
}

.inv-bottom {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 8px 10px 12px;
}

.inv-bottom-left {
    flex: 1;
    line-height: 1.4;
    font-size: 10px;
}

.inv-bottom-left > div {
    margin-bottom: 8px;
}

.inv-section-title {
    font-weight: 700;
    font-size: 11px;
    border-bottom: 1px solid #c6ccd4;
    padding-bottom: 2px;
    margin-bottom: 3px;
    color: #111;
    text-transform: uppercase;
}

.inv-words-text {
    font-style: italic;
    font-weight: 700;
}

.inv-bottom-right {
    width: 255px;
    flex-shrink: 0;
}

.inv-totals {
    border: 1px solid #aab2bc;
}

.inv-totals td {
    border-bottom: 1px solid #d5dae1;
    padding: 5px 8px;
    font-size: 10px;
}

.inv-totals td:last-child {
    text-align: right;
    font-weight: 700;
}

.inv-total-row td,
.inv-balance-row td {
    background: #fff;
    color: #111;
}

.inv-sig {
    margin-top: 8px;
    text-align: right;
}

.inv-sig-inner {
    display: inline-block;
    min-width: 145px;
    border: 1px solid #aab2bc;
    padding: 20px 12px 5px;
    font-size: 10px;
    color: #111;
}

.inv-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 8px;
}

.inv-btn {
    display: inline-block;
    padding: 6px 16px;
    border: none;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    color: #fff;
}

.inv-btn-back { background: #6c757d; }
.inv-btn-print { background: #1a2f5e; }

@media print {
    body { background: #fff !important; }
    .no-print { display: none !important; }
    .inv-wrapper { margin: 0; padding: 0; max-width: 100%; }
    .inv-box { box-shadow: none; border: 1px solid #9ea7b3; }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\sales\show.blade.php ENDPATH**/ ?>