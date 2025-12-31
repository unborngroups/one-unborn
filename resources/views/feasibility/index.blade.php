@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Manage feasibility</h3>

       {{-- Add --}}
       @if($permissions && $permissions->can_add)
           <a href="{{ route('feasibility.create') }}" class="btn btn-success">Add Feasibility</a>
       @endif

    </div>

    @if(session('success'))

        <div class="alert alert-success">{{ session('success') }}</div>

    @endif



    <div class="card shadow border-0">

        <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->
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

        <div class="card-body table-responsive">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

            <table class="table table-bordered table-hover align-middle" id="feasibility">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th>Action</th>

                        <th class="col">Feasibility Request ID</th>

                        <th class="col">Type of Service</th>

                        <th class="col">Company Name</th>

                        <th class="col">Client Name</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($feasibilities as $index => $feasibility)

                        <tr>

                            <td><input type="checkbox" class="rowCheckbox" value="{{ $feasibility->id }}"></td>

                            <td class="text-center">{{ $index+1 }}</td>

                             <td class="text-center">

                                <div class="d-flex justify-content-center gap-1">

                                {{-- Edit --}}

                                    @if($permissions && $permissions->can_edit)
                                        <a href="{{ route('feasibility.edit', $feasibility) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif


                                 {{-- View --}}

                                   @if($permissions && $permissions->can_view)
                                       <a href="{{ route('feasibility.show', $feasibility->id) }}" class="btn btn-sm btn-info" title="View">
                                           <i class="bi bi-eye"></i>
                                       </a>
                                   @endif


                                


                                </div>

                            </td>

                            <td class="col">{{ $feasibility->feasibility_request_id ?? 'N/A' }}</td>

                            <td class="col">{{ $feasibility->type_of_service ?? 'N/A' }}</td>

                            <td class="col">{{ $feasibility->company->company_name ?? 'N/A' }}</td>

                            <td class="col">{{ $feasibility->client->client_name ?? 'N/A' }}</td>

                            <td>

                                <span class="badge bg-success">

                                    Created

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr><td colspan="8" class="text-center">No Feasibility Found</td></tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        
        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $feasibilities->firstItem() ?? 0 }}
                to
                {{ $feasibilities->lastItem() ?? 0 }}
                of
                {{ number_format($feasibilities->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($feasibilities->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $feasibilities->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $feasibilities->lastPage();
                            $current = $feasibilities->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $feasibilities->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $feasibilities->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $feasibilities->url($total) }}">{{ $total }}</a></li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($feasibilities->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $feasibilities->nextPageUrl() }}" rel="next">Next</a></li>
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

    document.querySelectorAll('#feasibility tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

document.getElementById('selectAll').addEventListener('change', function(){

    let isChecked = this.checked;

    document.querySelectorAll('.rowCheckbox').forEach(cb => cb.checked = isChecked);

});





</script>





<style>

    .col {

    width: 130px;

    white-space: nowrap;

}

</style>

@endsection

