<?php
    $client   = $feasibility->client ?? null;
    $appName  = config('app.name', 'One-Unborn');
    // Generate full URL based on APP_URL + route definition
    $editUrl  = route('operations.feasibility.edit', $feasibilityStatus->id ?? null);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Feasibility Exception</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; }
        .mail-wrapper { background: #f4f6f8; padding: 20px; }
        .mail-card { max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 6px; padding: 20px 24px; box-shadow: 0 2px 6px rgba(15,23,42,0.08); }
        .mail-header { border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 16px; }
        .mail-title { margin: 0; font-size: 18px; color: #111827; }
        .mail-subtitle { margin: 4px 0 0; font-size: 13px; color: #6b7280; }
        .section-title { font-weight: 600; font-size: 14px; margin: 18px 0 6px; color: #111827; }
        .details-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .details-table th, .details-table td { padding: 6px 8px; text-align: left; }
        .details-table th { width: 40%; color: #6b7280; font-weight: 500; }
        .details-table tr:nth-child(odd) { background-color: #f9fafb; }
        .btn-primary { display: inline-block; padding: 10px 18px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 999px; font-size: 13px; font-weight: 600; margin-top: 14px; }
        .btn-primary:hover { background-color: #1d4ed8; }
        .footer { margin-top: 20px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
<div class="mail-wrapper">
    <div class="mail-card">
        <div class="mail-header">
            <h1 class="mail-title">Feasibility Exception</h1>
            <p class="mail-subtitle">An exception has been sent for the following feasibility.</p>
        </div>

        <p>Dear Team,</p>

        <h3 class="section-title">Feasibility Details</h3>
        <table class="details-table">
            <tr>
                <th>Feasibility ID</th>
                <td><?php echo e($feasibility->feasibility_request_id ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Client</th>
                <td><?php echo e(optional($client)->client_name ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Type of Service</th>
                <td><?php echo e($feasibility->type_of_service ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>No. of Links</th>
                <td><?php echo e($feasibility->no_of_links ?? 'N/A'); ?></td>
            </tr>
        </table>

        <h3 class="section-title">Selected Vendor</h3>
        <table class="details-table">
            <tr>
                <th>Vendor Name</th>
                <td><?php echo e($vendorName); ?></td>
            </tr>
        </table>

        <?php if(!empty($vendorDetails)): ?>
            <h3 class="section-title">Vendor Commercial Details</h3>
            <table class="details-table">
                <?php if(isset($vendorDetails['arc'])): ?>
                    <tr>
                        <th>ARC</th>
                        <td><?php echo e($vendorDetails['arc']); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($vendorDetails['otc'])): ?>
                    <tr>
                        <th>OTC</th>
                        <td><?php echo e($vendorDetails['otc']); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($vendorDetails['static_ip_cost'])): ?>
                    <tr>
                        <th>Static IP Cost</th>
                        <td><?php echo e($vendorDetails['static_ip_cost']); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(isset($vendorDetails['delivery_timeline'])): ?>
                    <tr>
                        <th>Delivery Timeline</th>
                        <td><?php echo e($vendorDetails['delivery_timeline']); ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>

        <h3 class="section-title">Raised By</h3>
        <table class="details-table">
            <tr>
                <th>User</th>
                <td><?php echo e($sentBy->name ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo e($sentBy->official_email ?? $sentBy->email ?? 'N/A'); ?></td>
            </tr>
        </table>

        <p style="margin-top: 16px;">You can review and take action on this feasibility using the button below:</p>

        <a href="<?php echo e($editUrl); ?>" class="btn-primary" target="_blank">View Feasibility in <?php echo e($appName); ?></a>

        <p class="footer">This is an automated email from <?php echo e($appName); ?>. Please do not reply directly to this message.</p>
    </div>
</div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/emails/feasibility/exception.blade.php ENDPATH**/ ?>