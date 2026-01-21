

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="mb-3 text-primary">User Login Report</h3>

    <div class="card shadow p-3 border-0">

     <div class="card-header bg-light d-flex justify-content-between">

        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" <?php echo e(request('per_page', 10) == 10 ? 'selected' : ''); ?>>10</option>
                <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
            </select>
            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->
        </form>

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
 
        </div>

        <table class="table table-bordered table-striped" id="admin">
            <thead class="table-dark-primary">
                <tr>
                    <th>S.No</th>
                    <th>User Name</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Total Minutes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr <?php if(intval($log->total_minutes) >= 120): ?> style="background-color:#fff3cd;" <?php endif; ?>>
                    <td><?php echo e($logs->firstItem() + $key); ?></td>

                    <td><?php echo e($log->user->name ?? 'Unknown User'); ?></td>

                    <td><?php echo e(\Carbon\Carbon::parse($log->login_time)->format('d-m-Y h:i:s A')); ?></td>
                    <td>
                        <?php if(str_contains($log->logout_display, 'Active Now')): ?>
                            <?php echo e($log->logout_display); ?>

                        <?php else: ?>
                            <?php echo e(\Carbon\Carbon::parse($log->logout_display)->format('d-m-Y h:i:s A')); ?>

                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            $minutes = intval($log->total_minutes);
                            $hours = intdiv($minutes, 60);
                            $mins = $minutes % 60;
                        ?>
                        <?php echo e($hours > 0 ? $hours . ' hr ' : ''); ?><?php echo e(max($mins, 1)); ?> min
                        <?php if($log->status_display === 'Online'): ?>
                        <small class="text-muted">(Live)</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($log->status_display === 'Online'): ?>
                            <span class="badge bg-success">Online</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Offline</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <!--  -->
    <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                <?php echo e($logs->firstItem() ?? 0); ?>

                to
                <?php echo e($logs->lastItem() ?? 0); ?>

                of
                <?php echo e(number_format($logs->total())); ?> entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        
                        <?php if($logs->onFirstPage()): ?>
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        <?php else: ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($logs->previousPageUrl()); ?>" rel="prev">Previous</a></li>
                        <?php endif; ?>

                        
                        <?php
                            $total = $logs->lastPage();
                            $current = $logs->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        ?>

                        <?php if($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($logs->url(1)); ?>">1</a></li>
                            <?php if($start > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for($i = $start; $i <= $end; $i++): ?>
                            <?php if($i == $current): ?>
                                <li class="page-item active"><span class="page-link"><?php echo e($i); ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?php echo e($logs->url($i)); ?>"><?php echo e($i); ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($end < $total): ?>
                            <?php if($end < $total - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($logs->url($total)); ?>"><?php echo e($total); ?></a></li>
                        <?php endif; ?>
  
                        
                        <?php if($logs->hasMorePages()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo e($logs->nextPageUrl()); ?>" rel="next">Next</a></li>
                        <?php else: ?>
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        
    </div>

</div>



<script>
var userId = <?php echo json_encode(Auth::id(), 15, 512) ?>;

document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll('#admin tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
    });
});

window.addEventListener('beforeunload', function () {
    if (userId) {
        navigator.sendBeacon('/api/user-logout', JSON.stringify({ user_id: userId }));
    }
});

</script>

<?php $__env->startPush('scripts'); ?>
<script>
function updateLiveMinutes() {
    const now = new Date();
    document.querySelectorAll('#admin tbody tr').forEach(function(row) {
        const statusCell = row.querySelector('td:last-child .badge');
        if (statusCell && statusCell.textContent.trim() === 'Online') {
            const loginCell = row.querySelector('td:nth-child(3)');
            if (!loginCell) return;
            const loginText = loginCell.textContent.trim();
            // Parse date and time from cell (d-m-Y h:i:s A)
            const match = loginText.match(/(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2}):(\d{2}) (AM|PM)/);
            if (!match) return;
            const [_, day, month, year, hour, min, sec, ampm] = match;
            let h = parseInt(hour);
            if (ampm === 'PM' && h < 12) h += 12;
            if (ampm === 'AM' && h === 12) h = 0;
            const loginDate = new Date(year, month - 1, day, h, parseInt(min), parseInt(sec));
            let diffMs = now - loginDate;
            if (diffMs < 0) diffMs = 0;
            let totalMinutes = Math.floor(diffMs / 60000);
            let hours = Math.floor(totalMinutes / 60);
            let mins = totalMinutes % 60;
            let displayMins = (hours > 0 || mins > 0) ? mins : 1;
            let text = (hours > 0 ? hours + ' hr ' : '') + displayMins + ' min';
            const minCell = row.querySelector('td:nth-child(5)');
            if (minCell) minCell.innerHTML = text + ' <small class="text-muted">(Live)</small>';
        }
    });
}
setInterval(updateLiveMinutes, 60000);
document.addEventListener('DOMContentLoaded', updateLiveMinutes);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->yieldPushContent('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/admin/index.blade.php ENDPATH**/ ?>