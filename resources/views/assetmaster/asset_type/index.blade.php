@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="row">

        <div class="col-md-6">
    <h3 class="mb-3 text-primary float-start">Asset Type List</h3>
</div>

<div class="col-md-6">
    <a href="{{ route('assetmaster.asset_type.create') }}" class="btn btn-success mb-3 float-end">+ Add Asset Type</a>
</div>

</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<div class="row">
    <!-- <div class="card-header bg-light d-flex flex-wrap align-items-center gap-2"> -->
        <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100 float-start mb-3">
            <label for="entriesSelect" class="mb-0">Show</label>
            <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25 float-end" placeholder="Search...">

        </form>

             {{--- Delete --}}
         @if($permissions->can_delete)
         <form id="bulkDeleteForm" action="{{ route('assetmaster.asset_type.bulk-delete') }}" method="POST" class="d-inline">
             @csrf
             <div id="bulkDeleteInputs"></div>
         </form>
<button id="deleteSelectedBtn" class="btn btn-danger d-none">
    <i class="bi bi-trash"></i>
</button>
@endif
        <!-- </div> -->
        </div>

    <table class="table table-bordered table-striped" id="assetTypeTable">
        <thead class="table-dark-primary">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>S.No</th>
                <!-- <th>Company</th> -->
                <th>Asset Type</th>
                <th>Created Date</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assetTypes as $key => $at)
                <tr>
                    <td><input type="checkbox" class="rowCheckbox" value="{{ $at->id }}"></td>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $at->type_name }}</td>
                    <td>{{ $at->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('assetmaster.asset_type.edit', $at->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('assetmaster.asset_type.destroy', $at->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>

</div>
<!--  -->
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
    
    {{-- Left text --}}
    <div class="text-muted small">
        Showing 
        {{ $assetTypes->firstItem() ?? 0 }} 
        to 
        {{ $assetTypes->lastItem() ?? 0 }} 
        of 
        {{ number_format($assetTypes->total()) }} entries
    </div>

    {{-- Right pagination --}}
    <div>
        @if ($assetTypes->hasPages())
            <nav>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($assetTypes->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $assetTypes->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $total = $assetTypes->lastPage();
                        $current = $assetTypes->currentPage();
                        $max = 5; // Number of page links to show
                        $start = max(1, $current - floor($max / 2));
                        $end = min($total, $start + $max - 1);
                        if ($end - $start < $max - 1) {
                            $start = max(1, $end - $max + 1);
                        }
                    @endphp

                    @if ($start > 1)
                        <li class="page-item"><a class="page-link" href="{{ $assetTypes->url(1) }}">1</a></li>
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $current)
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $assetTypes->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($end < $total)
                        @if ($end < $total - 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $assetTypes->url($total) }}">{{ $total }}</a></li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($assetTypes->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $assetTypes->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>

</div>

<script>
    document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#assetTypeTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

//checkall functionality 

document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateDeleteButtonVisibility();
});

// Bulk Delete Functionality
document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected client(s)?`)) {
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

function updateDeleteButtonVisibility() {
    const totalChecked = document.querySelectorAll('.rowCheckbox:checked').length;
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    if (!deleteBtn) {
        return;
    }
    if (totalChecked > 0) {
        deleteBtn.classList.remove('d-none');
    } else {
        deleteBtn.classList.add('d-none');
    }
}

document.querySelectorAll('.rowCheckbox').forEach(cb => {
    cb.addEventListener('change', updateDeleteButtonVisibility);
});

// Keep the delete button state correct on page load
updateDeleteButtonVisibility();
</script>
@endsection
