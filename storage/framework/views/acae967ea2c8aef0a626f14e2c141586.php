
<?php if(isset($templateContent) && $status === 'Closed' && $templateContent): ?>
    <?php echo $templateContent; ?>

<?php else: ?>
    <p>Feasibility Status: <?php echo e($status); ?></p>
    <p>Feasibility ID: <?php echo e($feasibility->feasibility_request_id ?? ''); ?></p>
    <p>Action By: <?php echo e($actionBy->name ?? ''); ?></p>
    <p>Previous Status: <?php echo e($previousStatus ?? '-'); ?></p>
<?php endif; ?>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/emails/feasibility/status.blade.php ENDPATH**/ ?>