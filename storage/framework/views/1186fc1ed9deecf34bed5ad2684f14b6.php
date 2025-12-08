

<?php $__env->startSection('content'); ?>
<div class="container mt-3">
    <h4 class="fw-bold">Notification Settings</h4>

    <form method="post" action="<?php echo e(route('client.notifications.settings.update')); ?>">
        <?php echo csrf_field(); ?>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Email Alerts</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notify_sla_breach" <?php echo e($settings->notify_sla_breach ? 'checked' : ''); ?>>
                    <label class="form-check-label">SLA Breach Alerts</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notify_link_down" <?php echo e($settings->notify_link_down ? 'checked' : ''); ?>>
                    <label class="form-check-label">Link Down Alerts</label>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Thresholds</label>
                <input class="form-control mb-2" type="number" name="latency_threshold" value="<?php echo e($settings->latency_threshold); ?>" placeholder="Latency Threshold (ms)">
                <input class="form-control mb-2" type="number" name="packet_loss_threshold" value="<?php echo e($settings->packet_loss_threshold); ?>" placeholder="Packet Loss (%)">
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label fw-bold">Additional Recipients</label>
                <input class="form-control" type="text" name="extra_recipients" value="<?php echo e($settings->extra_recipients); ?>" placeholder="Email1, Email2, Email3">
            </div>
        </div>

        <button class="btn btn-primary mt-3">Save</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\client_portal\notifications\settings.blade.php ENDPATH**/ ?>