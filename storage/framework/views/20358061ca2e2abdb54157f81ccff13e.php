

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Notification Settings</h3>
                    <div>
                        <?php if($companies->count() > 1): ?>
                        <select class="form-select" onchange="window.location.href='?company_id='+this.value">
                            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($company->id); ?>" <?php echo e($companyId == $company->id ? 'selected' : ''); ?>>
                                    <?php echo e($company->company_name); ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo e(session('success')); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('notification-settings.update', $settings->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <!-- SLA Breach Notifications -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">📊 SLA Breach Notifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="sla_breach_enabled" id="sla_breach_enabled" value="1" <?php echo e($settings->sla_breach_enabled ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <label class="form-check-label" for="sla_breach_enabled">
                                        <strong>Enable SLA Breach Notifications</strong>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="sla_breach_to_client" id="sla_breach_to_client" value="1" <?php echo e($settings->sla_breach_to_client ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                            <label class="form-check-label" for="sla_breach_to_client">
                                                Send to Client Portal Users
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="sla_breach_to_operations" id="sla_breach_to_operations" value="1" <?php echo e($settings->sla_breach_to_operations ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                            <label class="form-check-label" for="sla_breach_to_operations">
                                                Send to Operations Team
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="sla_breach_recipients" class="form-label">Additional Email Recipients (comma-separated)</label>
                                    <input type="text" class="form-control" name="sla_breach_recipients" id="sla_breach_recipients" 
                                           value="<?php echo e(is_array($settings->sla_breach_recipients) ? implode(', ', $settings->sla_breach_recipients) : ''); ?>" 
                                           placeholder="email1@example.com, email2@example.com" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                </div>
                            </div>
                        </div>

                        <!-- Link Down Alerts -->
                        <div class="card mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">🔴 Link Down Alerts</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="link_down_enabled" id="link_down_enabled" value="1" <?php echo e($settings->link_down_enabled ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <label class="form-check-label" for="link_down_enabled">
                                        <strong>Enable Link Down Alerts</strong>
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="link_down_threshold_minutes" class="form-label">Alert Threshold (minutes)</label>
                                    <input type="number" class="form-control" name="link_down_threshold_minutes" id="link_down_threshold_minutes" 
                                           value="<?php echo e($settings->link_down_threshold_minutes); ?>" min="1" max="60" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <small class="text-muted">Send alert if link is down for more than X minutes</small>
                                </div>

                                <div class="mb-3">
                                    <label for="link_down_recipients" class="form-label">Email Recipients (comma-separated)</label>
                                    <input type="text" class="form-control" name="link_down_recipients" id="link_down_recipients" 
                                           value="<?php echo e(is_array($settings->link_down_recipients) ? implode(', ', $settings->link_down_recipients) : ''); ?>" 
                                           placeholder="ops@example.com, noc@example.com" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <small class="text-muted">Operations team emails will be added automatically</small>
                                </div>
                            </div>
                        </div>

                        <!-- High Latency Alerts -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">⚠️ High Latency Alerts</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="high_latency_enabled" id="high_latency_enabled" value="1" <?php echo e($settings->high_latency_enabled ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <label class="form-check-label" for="high_latency_enabled">
                                        <strong>Enable High Latency Alerts</strong>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="high_latency_threshold_ms" class="form-label">Latency Threshold (ms)</label>
                                        <input type="number" class="form-control" name="high_latency_threshold_ms" id="high_latency_threshold_ms" 
                                               value="<?php echo e($settings->high_latency_threshold_ms); ?>" min="10" max="500" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="high_latency_duration_minutes" class="form-label">Duration (minutes)</label>
                                        <input type="number" class="form-control" name="high_latency_duration_minutes" id="high_latency_duration_minutes" 
                                               value="<?php echo e($settings->high_latency_duration_minutes); ?>" min="5" max="60" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="high_latency_recipients" class="form-label">Email Recipients (comma-separated)</label>
                                    <input type="text" class="form-control" name="high_latency_recipients" id="high_latency_recipients" 
                                           value="<?php echo e(is_array($settings->high_latency_recipients) ? implode(', ', $settings->high_latency_recipients) : ''); ?>" 
                                           placeholder="ops@example.com" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                </div>
                            </div>
                        </div>

                        <!-- High Packet Loss Alerts -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">⚠️ High Packet Loss Alerts</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="high_packet_loss_enabled" id="high_packet_loss_enabled" value="1" <?php echo e($settings->high_packet_loss_enabled ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <label class="form-check-label" for="high_packet_loss_enabled">
                                        <strong>Enable High Packet Loss Alerts</strong>
                                    </label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="high_packet_loss_threshold_percent" class="form-label">Packet Loss Threshold (%)</label>
                                        <input type="number" step="0.1" class="form-control" name="high_packet_loss_threshold_percent" id="high_packet_loss_threshold_percent" 
                                               value="<?php echo e($settings->high_packet_loss_threshold_percent); ?>" min="0.1" max="50" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="high_packet_loss_duration_minutes" class="form-label">Duration (minutes)</label>
                                        <input type="number" class="form-control" name="high_packet_loss_duration_minutes" id="high_packet_loss_duration_minutes" 
                                               value="<?php echo e($settings->high_packet_loss_duration_minutes); ?>" min="5" max="60" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="high_packet_loss_recipients" class="form-label">Email Recipients (comma-separated)</label>
                                    <input type="text" class="form-control" name="high_packet_loss_recipients" id="high_packet_loss_recipients" 
                                           value="<?php echo e(is_array($settings->high_packet_loss_recipients) ? implode(', ', $settings->high_packet_loss_recipients) : ''); ?>" 
                                           placeholder="ops@example.com" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">📱 WhatsApp Notifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_enabled" id="whatsapp_enabled" value="1" <?php echo e($settings->whatsapp_enabled ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <label class="form-check-label" for="whatsapp_enabled">
                                        <strong>Enable WhatsApp Notifications</strong>
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="whatsapp_numbers" class="form-label">WhatsApp Numbers (comma-separated with country code)</label>
                                    <input type="text" class="form-control" name="whatsapp_numbers" id="whatsapp_numbers" 
                                           value="<?php echo e(is_array($settings->whatsapp_numbers) ? implode(', ', $settings->whatsapp_numbers) : ''); ?>" 
                                           placeholder="+919876543210, +918765432109" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                </div>
                            </div>
                        </div>

                        <!-- General Settings -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">⚙️ General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_enabled" id="email_enabled" value="1" <?php echo e($settings->email_enabled ? 'checked' : ''); ?> <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <label class="form-check-label" for="email_enabled">
                                        <strong>Enable Email Notifications</strong>
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="email_from" class="form-label">Email From Address (optional)</label>
                                    <input type="email" class="form-control" name="email_from" id="email_from" 
                                           value="<?php echo e($settings->email_from); ?>" placeholder="alerts@yourdomain.com" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                </div>

                                <div class="mb-3">
                                    <label for="alert_cooldown_minutes" class="form-label">Alert Cooldown Period (minutes)</label>
                                    <input type="number" class="form-control" name="alert_cooldown_minutes" id="alert_cooldown_minutes" 
                                           value="<?php echo e($settings->alert_cooldown_minutes); ?>" min="5" max="180" <?php echo e(!$permissions->can_edit ? 'disabled' : ''); ?>>
                                    <small class="text-muted">Prevent duplicate alerts within this time period</small>
                                </div>
                            </div>
                        </div>

                        <?php if($permissions->can_edit): ?>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                        <?php endif; ?>
                    </form>

                    <!-- Recent Notifications -->
                    <div class="card mt-5">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="mb-0">Recent Notifications</h5>
                            <a href="<?php echo e(route('notification-settings.logs')); ?>" class="btn btn-sm btn-outline-primary">View All Logs</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Type</th>
                                            <th>Link</th>
                                            <th>Channel</th>
                                            <th>Recipients</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $recentNotifications->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($log->created_at->format('Y-m-d H:i')); ?></td>
                                                <td><span class="badge bg-info"><?php echo e(str_replace('_', ' ', $log->notification_type)); ?></span></td>
                                                <td><?php echo e($log->clientLink->deliverable->deliverable_id ?? 'N/A'); ?></td>
                                                <td><?php echo e(ucfirst($log->channel)); ?></td>
                                                <td><?php echo e(is_array($log->recipients) ? count($log->recipients) : 0); ?> recipient(s)</td>
                                                <td>
                                                    <?php if($log->sent_successfully): ?>
                                                        <span class="badge bg-success">Sent</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Failed</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No notifications sent yet</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views\notification_settings\index.blade.php ENDPATH**/ ?>