@extends('layouts.app')



@section('content')

<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h4 class="mb-0">

                        <i class="bi bi-receipt"></i> Purchase Orders

                    </h4>

                     @if($permissions->can_add)
                    <a href="{{ route('sm.purchaseorder.create') }}" class="btn btn-success">

                            <i class="bi bi-plus-circle"></i> Create New Purchase Order

                        </a>

         @endif

                </div>

                 {{--- Delete --}}
         @if($permissions->can_delete)
         <form id="bulkDeleteForm" action="{{ route('sm.purchaseorder.bulk-delete') }}" method="POST" class="d-inline">
             @csrf
             <div id="bulkDeleteInputs"></div>
         </form>
<button id="deleteSelectedBtn" class="btn btn-danger d-none">
    <i class="bi bi-trash"></i>
</button>
@endif

                <div class="card-body">
                    

              



                    {{-- Success/Error Messages --}}

                    @if(session('success'))

                        <div class="alert alert-success alert-dismissible fade show" role="alert">

                            {{ session('success') }}

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                        </div>

                    @endif



                    @if(session('error'))

                        <div class="alert alert-danger alert-dismissible fade show" role="alert">

                            {{ session('error') }}

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                        </div>

                    @endif

<!--  -->
                    <div class="">
                        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </form>
                    </div>

                    {{-- Purchase Orders Table --}}

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered" id="purchaseorder">

                            <thead class="table-dark-primary">

                                <tr>
                                <th width="50"><input type="checkbox" id="select_all"></th>


                                    <th>S.No</th>

                                    <th>Actions</th>

                                    <th>PO Number</th>

                                    <th>PO Date</th>

                                    <th>Client Name</th>

                                    <th>Feasibility ID</th>

                                    <th>No. of Links</th>

                                    <th>Total Cost</th>

                                    <th>Status</th>

                                    

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($purchaseOrders as $index => $po)

                                    <tr>
                                        <td>
                                    <input type="checkbox" class="row-checkbox" value="{{ $po->id }}">
                                </td>

                                        <td>{{ $index + 1 }}</td>

                                        <td class="text-center d-flex justify-content-center gap-1">

                                {{-- Edit --}}

                                @if($permissions->can_edit)

                               <a href="{{ route('sm.purchaseorder.edit', $po->id) }}" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                @endif



                                {{-- Toggle Status --}}

                                <form action="{{ route('sm.purchaseorder.toggle-status', $po->id) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('PATCH')

                                     <button type="submit" class="btn btn-sm {{ $po->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">

                                {{ $po->status }}

                                    </button>

                                </form>



                                 {{-- Delete --}}

                                 @if($permissions->can_delete)

                                 <form action="{{ route('sm.purchaseorder.destroy',$po->id) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('DELETE') 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Purchase Order?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   @endif

                                {{-- View --}}

                                   @if($permissions->can_view)

                                   <a href="{{ route('sm.purchaseorder.view', $po->id) }}" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     @endif



                            </td>

                                       

                                        <td>

                                            <strong class="text-primary">{{ $po->po_number }}</strong>

                                        </td>

                                        <td>{{ $po->po_date->format('Y-m-d') }}</td>

                                        <td>{{ $po->feasibility->client->client_name ?? 'N/A' }}</td>

                                        <td>{{ $po->feasibility->feasibility_request_id ?? 'N/A' }}</td>

                                        <td>{{ $po->no_of_links }}</td>

                                        <td>

                                            ₹{{ number_format(($po->arc_per_link + $po->otc_per_link + $po->static_ip_cost_per_link) * $po->no_of_links, 2) }}

                                        </td>

                        <td>

                            @if($po->status === 'Active')

                                <span class="badge bg-success">{{ $po->status }}</span>

                            @else

                                <span class="badge bg-danger">{{ $po->status }}</span>

                            @endif

                        </td>                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="9" class="text-center text-muted">

                                            <i class="bi bi-inbox"></i> No Purchase Orders found. 

                                            <a href="{{ route('sm.purchaseorder.create') }}" class="text-decoration-none">Create your first Purchase Order</a>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
    
    {{-- Left text --}}
    <div class="text-muted small">
        Showing 
        {{ $purchaseOrders->firstItem() ?? 0 }} 
        to 
        {{ $purchaseOrders->lastItem() ?? 0 }} 
        of 
        {{ number_format($purchaseOrders->total()) }} entries
    </div>

    {{-- Right pagination --}}
    <div>
        @if ($purchaseOrders->hasPages())
            <nav>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($purchaseOrders->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $purchaseOrders->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $total = $purchaseOrders->lastPage();
                        $current = $purchaseOrders->currentPage();
                        $max = 5; // Number of page links to show
                        $start = max(1, $current - floor($max / 2));
                        $end = min($total, $start + $max - 1);
                        if ($end - $start < $max - 1) {
                            $start = max(1, $end - $max + 1);
                        }
                    @endphp

                    @if ($start > 1)
                        <li class="page-item"><a class="page-link" href="{{ $purchaseOrders->url(1) }}">1</a></li>
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $current)
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $purchaseOrders->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($end < $total)
                        @if ($end < $total - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $purchaseOrders->url($total) }}">{{ $total }}</a></li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($purchaseOrders->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $purchaseOrders->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>

</div>


            </div>

        </div>

    </div>

</div>

<script>
    // 
     document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#purchaseorder tbody tr').forEach(row => {

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

    
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#purchaseorder tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});


document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected purchase order(s)?`)) {
        return;
    }

    const inputsContainer = document.getElementById('bulkDeleteInputs');
    inputsContainer.innerHTML = '';
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        inputsContainer.appendChild(input);
    });

    document.getElementById('bulkDeleteForm')?.submit();
});


</script>
<style>
    .table th,  .table td {
    width: 230px;
    white-space: nowrap;
    text-align: center;
    }
</style>
@endsection

