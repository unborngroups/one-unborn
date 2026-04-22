@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">
   

    <div class="row">

        <div class="col-12">

            <div class="card shadow border-0">

                {{-- ✅ Header Section --}}

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>Open Feasibilities</h5>

                    <form id="searchForm" method="GET" class="d-flex align-items-center w-25">
                        <input type="text" name="search" id="tableSearch" class="form-control form-control-sm w-100" placeholder="Search..." value="{{ $search ?? '' }}" oninput="this.form.submit()">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    </form>

                </div>
                <div class="">
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
                </div>

                <div class="card-body">

                    {{-- ✅ Check if any feasibility records exist --}}

                    @if($records->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-striped table-hover" id="open">

                                {{-- ✅ Table Headers --}}

                                <thead class="table-dark-primary">

                                    <tr>

                                        <th>S.No</th>

                                        <th>Request ID</th>

                                        <th>Action</th>

                                        <th>Company Name</th>

                                        <th>Name</th>

                                        <th>Area / State</th>

                                        <th>Type of Service</th>

                                        <th>Speed</th>

                                        <th>Links</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    {{-- ✅ Loop through all records --}}

                                    @foreach($records as $index => $record)

                                        <tr>
                                           

                                            {{-- Serial No --}}

                                            <td>{{ ($records->currentPage() - 1) * $records->perPage() + $loop->iteration }}</td>

                                            {{-- Request ID --}}

                                            <td>

                                                <span class="">{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</span>

                                            </td>

                                            {{-- Action Buttons (View only) --}}

                                            <td>

                                                <div class="btn-group" role="group">

                                                    {{-- View button --}}

                                                    <a href="{{ route('sm.feasibility.view', $record->id) }}" 

                                                       class="btn btn-info btn-sm" title="View">

                                                        <i class="bi bi-eye"></i> View

                                                    </a>

                                                    

                                                </div>

                                            </td>

                                            {{-- Company Name --}}

                                            <td>{{ $record->feasibility->company->company_name ?? 'N/A' }}</td>

                                            {{-- Client Name --}}

                                            <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>

                                            {{-- Area / State --}}
                                            <td>{{ $record->feasibility->area ?? 'N/A' }} / {{ $record->feasibility->state ?? 'N/A' }}</td>

                                            {{-- Type of Service --}}

                                            <td>{{ $record->feasibility->type_of_service ?? 'N/A' }}</td>

                                            {{-- Speed --}}

                                            <td>{{ $record->feasibility->speed ?? 'N/A' }}</td>

                                            {{-- No of Links --}}

                                            <td>{{ $record->feasibility->no_of_links ?? 'N/A' }}</td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                    {{-- ✅ Show message when no records found --}}

                        <div class="text-center py-4">

                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>

                            <h5 class="text-muted mt-3">No open feasibilities found</h5>

                            <p class="text-muted">All feasibilities have been processed or none have been created yet.</p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $records->firstItem() ?? 0 }}
                to
                {{ $records->lastItem() ?? 0 }}
                of
                {{ number_format($records->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($records->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $records->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $records->lastPage();
                            $current = $records->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $records->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $records->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $records->url($total) }}">{{ $total }}</a></li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($records->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $records->nextPageUrl() }}" rel="next">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>



    </div>

</div>
<script>
     document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#open tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

    document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = [...rowCheckboxes].every(x => x.checked);
            const noneChecked = [...rowCheckboxes].every(x => !x.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && !noneChecked;
        });
    });
});
</script>

<style>
    .table th,  .table td {
        width: 130px;

    white-space: nowrap;

    }
    </style>
@endsection