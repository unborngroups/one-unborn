

<?php $__env->startSection('content'); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        .section {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th {
            background-color: #f2f2f2;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .no-border {
            border: none !important;
        }
        img {
            max-width: 250px;
        }
        .card{
            width: 70%;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #000;
        }
    </style>
</head>

<body>
<div class="card">
<table width="100%" cellpadding="5">
    <tr>
        <td width="15%" style="border:none;">
                            <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="Logo" class="h-16">

        </td>

        <td width="55%" style="border:none;">
            <strong><?php echo e($feasibility->company->company_name ?? ''); ?></strong><br>
            Company ID: <?php echo e($feasibility->company->company_id ?? ''); ?><br>
            <?php echo e($feasibility->company->address ?? ''); ?><br>
            GSTIN: <?php echo e($feasibility->company->gstin ?? ''); ?><br>
            Phone: <?php echo e($feasibility->company->alternative_contact_number ?? ''); ?><br>
            Email: <?php echo e($feasibility->company->company_email ?? ''); ?>

        </td>

        <td width="30%" align="right" style="border:none;">
            <h2>TAX INVOICE</h2>
        </td>
    </tr>
</table>

<hr>


<table width="100%" cellpadding="5">
    <tr>
        <td width="50%">
            <strong>Invoice #:</strong> <?php echo e($invoice->invoice_no); ?><br>
            <strong>Invoice Date:</strong> <?php echo e($invoice->invoice_date); ?><br>
            <strong>Terms:</strong> Net 30<br>
            <strong>Due Date:</strong> <?php echo e($invoice->due_date); ?><br>
            <strong>PO #:</strong> <?php echo e($deliverables->purchaseOrder->po_number ?? ''); ?>

        </td>

        <td width="50%">
            <strong>Place Of Supply:</strong> <?php echo e($invoice->place_of_supply ?? ''); ?><br>
            <strong>Service ID:</strong> <?php echo e($invoice->service_id ?? ''); ?><br>
            <strong>UNBORN Service ID:</strong> <?php echo e($invoice->unborn_service_id ?? ''); ?><br>
            <strong>Feasibility ID:</strong> <?php echo e($deliverables->feasibility->feasibility_request_id ?? ''); ?><br>
            <strong>Vendor ID:</strong> <?php echo e($deliverablePlan->vendor_code ?? ''); ?>

        </td>
    </tr>
</table>

<br>


<table width="100%" cellpadding="5">
    <tr>
        <th width="50%">Bill To</th>
        <th width="50%">Ship To</th>
    </tr>
    <tr>
        <td>
            <strong><?php echo e($feasibility->client->client_name ?? ''); ?></strong><br>
            <?php echo e($feasibility->client->address1 ?? ''); ?><br>
            <?php echo e($feasibility->client->city ?? ''); ?>,
            <?php echo e($feasibility->client->state ?? ''); ?>

            - <?php echo e($feasibility->client->pincode ?? ''); ?><br>
            GSTIN: <?php echo e($feasibility->client->gstin ?? ''); ?>

        </td>

        <td>
            <strong><?php echo e($client->ship_to_name ?? $client->client_name ?? ''); ?></strong><br>
            <?php echo e($client->ship_to_address ?? $client->address ?? ''); ?><br>
            <?php echo e($client->ship_to_city ?? $client->city ?? ''); ?>,
            <?php echo e($client->ship_to_state ?? $client->state ?? ''); ?>

            - <?php echo e($client->ship_to_pincode ?? $client->pincode ?? ''); ?><br>
            GSTIN: <?php echo e($client->ship_to_gst_number ?? $client->gst_number ?? ''); ?>

        </td>
    </tr>
</table>

<br>
<strong>Subject:</strong>
            Invoice for Order ID: <?php echo e($invoice->order_id ?? ''); ?>

            | Vendor Code: <?php echo e($deliverablePlan->vendor_code ?? ''); ?>



<br>


<table width="100%" cellpadding="5">
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>HSN/SAC</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Taxable</th>
            <th>CGST</th>
            <th>SGST</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($key+1); ?></td>
            <td><?php echo e($item->description); ?></td>
            <td><?php echo e($item->hsn_sac ?? ''); ?></td>
            <td><?php echo e($item->quantity); ?></td>
            <td align="right"><?php echo e(number_format($item->rate,2)); ?></td>
            <td align="right"><?php echo e(number_format($item->amount,2)); ?></td>
            <td align="right"><?php echo e(number_format($item->taxable_amount ?? 0,2)); ?></td>
            <td align="right"><?php echo e(number_format($item->cgst_amount ?? 0,2)); ?></td>
            <td align="right"><?php echo e(number_format($item->sgst_amount ?? 0,2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<br>


<table width="100%">
    <tr>
        <td width="60%" style="border:none;"></td>

        <td width="40%">
            <table width="100%" cellpadding="5">
                <tr>
                    <td>Sub Total</td>
                    <td align="right"><?php echo e(number_format($invoice->subtotal,2)); ?></td>
                </tr>
                <tr>
                    <td>CGST</td>
                    <td align="right"><?php echo e(number_format($invoice->cgst_total ?? 0,2)); ?></td>
                </tr>
                <tr>
                    <td>SGST</td>
                    <td align="right"><?php echo e(number_format($invoice->sgst_total ?? 0,2)); ?></td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td align="right"><strong><?php echo e(number_format($invoice->total_amount,2)); ?></strong></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br><br>

<div style="text-align:right;">
    Authorized Signature
</div>


</div>

            <div class="d-flex justify-content-center gap-2">
                <a href="<?php echo e(route('finance.invoices.index')); ?>" class="btn btn-secondary"><--Back</a>
            </div>
</body> 
</html>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\finance\invoices\view.blade.php ENDPATH**/ ?>