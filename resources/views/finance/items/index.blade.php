@extends('layouts.app') 



@section('content') 



<div class="container-fluid py-4">

    {{-- ✅ Full-width Bootstrap container with padding on top and bottom --}}

    

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1 class="fw-bold text-primary">Finance-Items</h1>

        {{-- Add --}}

        @if($permissions->can_add)

        <a href="{{ route('finance.items.create') }}" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Items

        </a>

         @endif

    </div>



    @if(session('success'))

        {{-- ✅ Show success message after adding/updating/deleting --}}

        <div class="alert alert-success">{{ session('success') }}</div>

    @endif



    <div class="card shadow-lg border-0">

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

            <input type="text" id="search" class="form-control form-control-sm w-25" placeholder="Search...">
 {{--- Delete --}}
         @if($permissions->can_delete)
         <form id="bulkDeleteForm" action="{{ route('finance.items.bulk-delete') }}" method="POST" class="d-inline">
             @csrf
             <div id="bulkDeleteInputs"></div>
         </form>
         <button id="deleteSelectedBtn" class="btn btn-danger d-none">
             <i class="bi bi-trash"></i>
         </button>
         @endif
        </div>
        </div>

        <div class="card-body p-0 table-responsive">
            
            <table class="table table-hover table-bordered mb-0" id="itemsTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        {{-- ✅ Checkbox to select all rows --}}

                        <th>S.No</th>
                        <th>Action</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Rate</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    {{-- ✅ Table body: loop through user type records --}}

                    @foreach($items as $index => $item)

                        <tr>

                            <td><input type="checkbox" class="rowCheckbox" value="{{ $item->id }}"></td>

                            <td class="text-center">{{ $index+1 }}</td>

                            <td class="text-center d-flex justify-content-center gap-1">



                            {{-- Edit --}}

                                @if($permissions->can_edit)

                                {{-- ✏️ Edit button --}}

                                <a href="{{ route('finance.items.edit', $item->id) }}" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                @endif
                                 {{-- Delete --}}

                                 @if($permissions->can_delete)

                                {{-- 🗑 Delete button --}}

                                <form action="{{ route('finance.items.destroy', $item->id) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('DELETE') 

                                    {{-- ✅ Use DELETE HTTP verb --}}

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                 @endif

                                {{-- 🔁 Toggle Status button --}}

                                <form action="{{ route('finance.items.toggle-status', $item->id) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('PATCH')

                                    {{-- ✅ PATCH request to update status --}}

                                    <button type="submit" class="btn btn-sm {{ $item->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">

                                        {{ $item->status }}

                                    </button>

                                </form>

                                 {{-- View --}}

                                   @if($permissions->can_view)

                                   <a href="{{ route('finance.items.view', $item->id) }}" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     @endif


                                     
                            </td>

                            {{-- 🧾 Data columns --}}

                            <td class="col">{{ $item->item_name }}</td>
                            <td class="col">{{ $item->item_description ?? '-'}}</td>

                            <td class="col">{{ $item->item_rate ?? '-'}}</td>

                            {{-- ✅ Show description or “-” if null --}}



                            <td class="text-center">

                                {{-- ✅ Display status badge --}}

                                <span class="badge {{ $item->status=='Active'?'bg-success':'bg-secondary' }}">

                                    {{ $item->status }}

                                </span>

                            </td>

                        </tr>

                    @endforeach



                    {{-- ⚠️ If no records found --}}

                    @if($items->isEmpty())

                        <tr>

                            <td colspan="9" class="text-center text-muted">No items Found</td>

                        </tr>
                    @endif

                    

                </tbody>

            </table>

        </div>
<div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $items->firstItem() ?? 0 }}
                to
                {{ $items->lastItem() ?? 0 }}
                of
                {{ number_format($items->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($items->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $items->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $items->lastPage();
                            $current = $items->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $items->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $items->url($total) }}">{{ $total }}</a></li>
                        @endif
  
                        {{-- Next Page Link --}}
                        @if ($items->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $items->nextPageUrl() }}" rel="next">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        
    </div>

</div>



{{-- 🧠 Script for table search and select all functionality --}}

<script>

document.getElementById('search').addEventListener('keyup', function() {

    // ✅ Filter table rows by search value

    let value = this.value.toLowerCase();

    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});




// ✅ Select / Deselect all checkboxes

document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateDeleteButtonVisibility();
});

// Update Delete Button Visibility
document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected user type(s)?`)) {
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
// 
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





<style>

    .col {

    width: 130px;

    white-space: nowrap;

}

</style>

@endsection 

{{-- ✅ Ends the content section --}}

