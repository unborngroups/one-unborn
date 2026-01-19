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
                <tr>
                    <td>{{ $logs->firstItem() + $key }}</td>
                    <td>{{ $log->user->name ?? 'Unknown User' }}</td>
                    <td>{{ $log->login_time }}</td>
                    <td>{{ $log->logout_display }}</td>
                    <td>{{ $log->total_minutes }}
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
    
document.getElementById('tableSearch').addEventListener('keyup', function() {

    // ✅ Filter table rows by search value

    let value = this.value.toLowerCase();

    document.querySelectorAll('#admin tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});


</script>
@endsection
