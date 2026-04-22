@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">User Login Report</h3>

    <div class="card shadow p-3 border-0">

     <div class="card-header bg-light d-flex justify-content-between">

        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
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
                @foreach($logs as $key => $log)
                <tr @if(intval($log->total_minutes) >= 120) style="background-color:#fff3cd;" @endif>
                    <td>{{ $logs->firstItem() + $key }}</td>

                    <td>{{ $log->user->name ?? 'Unknown User' }}</td>

                    <td>{{ \Carbon\Carbon::parse($log->login_time)->format('d-m-Y h:i:s A') }}</td>
                    <td>
                        @if(str_contains($log->logout_display, 'Active Now'))
                            {{ $log->logout_display }}
                        @else
                            {{ \Carbon\Carbon::parse($log->logout_display)->format('d-m-Y h:i:s A') }}
                        @endif
                    </td>
                    <td>
                        @php
                            $minutes = intval($log->total_minutes);
                            $hours = intdiv($minutes, 60);
                            $mins = $minutes % 60;
                        @endphp
                        {{ $hours > 0 ? $hours . ' hr ' : '' }}{{ max($mins, 1) }} min
                        @if($log->status_display === 'Online')
                        <small class="text-muted">(Live)</small>
                        @endif
                    </td>
                    <td>
                        @if($log->status_display === 'Online')
                            <span class="badge bg-success">Online</span>
                        @else
                            <span class="badge bg-secondary">Offline</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!--  -->
    <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $logs->firstItem() ?? 0 }}
                to
                {{ $logs->lastItem() ?? 0 }}
                of
                {{ number_format($logs->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($logs->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $logs->lastPage();
                            $current = $logs->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $logs->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $logs->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $logs->url($total) }}">{{ $total }}</a></li>
                        @endif
  
                        {{-- Next Page Link --}}
                        @if ($logs->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        
    </div>

</div>



<script>
var userId = @json(Auth::id());

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

@push('scripts')
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
@endpush

@stack('scripts')
@endsection
