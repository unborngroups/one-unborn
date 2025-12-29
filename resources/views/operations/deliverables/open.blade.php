@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 float-start"><i class="bi bi-hourglass-split me-2"></i>Open Deliverables</h5>
            
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25 float-end" placeholder="Search...">

        </div>
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
        
        <div class="card-body">
            @if($records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center" id="open">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <!-- <th width="50">
                                    <input type="checkbox" id="select_all" class="form-check-input">
                                </th> -->
                                <th width="50">S.No</th>
                                <th width="150">Action</th>
                                <th>PO Number</th>
                                <th>PO Date</th>
                                <th>Client Name</th>
                                <th>No. of Links</th>
                            </tr>

                            

                        </thead>

                        <tbody>
                            @foreach($records as $index => $record)
                            <tr>
                                <!-- <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $record->id }}">
                                </td> -->

                                <td>{{ $index + 1 }}</td>

                                <td>
                                    @if($permissions->can_edit)
                                    <a href="{{ route('operations.deliverables.edit', $record->id) }}" 
                                       class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    @endif
                                    @if($permissions->can_view)
                                    <a href="{{ route('operations.deliverables.view', $record->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    @endif
                                </td>

                                <td>{{ $record->po_number ?? 'N/A' }}</td>

                                <td>
                                    {{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : 'N/A' }}
                                </td>

                                <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>

                                <td>{{ $record->no_of_links ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No Open Deliverables Found</h5>
                    <p class="text-muted">There are currently no deliverables in "Open" status.</p>
                </div>
            @endif
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
// document.addEventListener('DOMContentLoaded', function() {
//     const selectAll = document.getElementById('select_all');
//     const rowCheckboxes = document.querySelectorAll('.row-checkbox');

//     if (selectAll) {
//         selectAll.addEventListener('change', function() {
//             rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
//         });
//     }

//     rowCheckboxes.forEach(cb => {
//         cb.addEventListener('change', function() {
//             const allChecked = [...rowCheckboxes].every(c => c.checked);
//             const noneChecked = [...rowCheckboxes].every(c => !c.checked);

//             if (selectAll) {
//                 selectAll.checked = allChecked;
//                 selectAll.indeterminate = !allChecked && !noneChecked;
//             }
//         });
//     });
// });

document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#open tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

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
