@extends('layouts.app') 



@section('content') 



<div class="container-fluid py-4">

    {{-- ✅ Full-width Bootstrap container with padding on top and bottom --}}

    

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Leave Type</h3>

        {{-- Add --}}
         @if($permissions->can_add)
        <a href="{{ route('hr.leavetype.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add New Leave Type
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

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">
 {{--- Delete --}}
         <form id="bulkDeleteForm" action="{{ route('hr.leavetype.bulk-delete') }}" method="POST" class="d-inline">
             @csrf
             <div id="bulkDeleteInputs"></div>
         </form>
         <button id="deleteSelectedBtn" class="btn btn-danger d-none">
             <i class="bi bi-trash"></i>
         </button>
        </div>
        </div>

        <div class="card-body p-0 table-responsive">
            
            <table class="table table-hover table-bordered mb-0" id="leaveTypeTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        {{-- ✅ Checkbox to select all rows --}}

                        <th>S.No</th>
                        <th>Actions</th>   

                        

                        <th>Leavetype</th>
                        <th>Shortcode</th>

                        

                    </tr>

                </thead>

                <tbody>

                    {{-- ✅ Table body: loop through leave type records --}}
                    @forelse($leavetypetable as $index => $leavetypedata)
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="{{ $leavetypedata->id }}"></td>
                            <td class="text-center">{{ $index+1 }}</td>

                            <td class="text-center d-flex justify-content-center gap-1">
                                 @if($permissions->can_edit)
                                <a href="{{ route('hr.leavetype.edit', $leavetypedata) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                 @if($permissions->can_delete)
                                <form action="{{ route('hr.leavetype.destroy', $leavetypedata) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE') 
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this leave type?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif

                                {{--
                                <form action="{{ route('hr.leavetype.toggle-status', $leavetypedata->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $leavetypedata->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $leavetypedata->status }}
                                    </button>
                                </form>
                                --}}
                                 @if($permissions->can_view)
                                <a href="{{ route('hr.leavetype.view', $leavetypedata->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @endif
                            </td>

                            <td class="col">{{ $leavetypedata->leavetype ?? '-' }}</td>
                            <td class="col">{{ $leavetypedata->shortcode ?? '-' }}</td>
                                @php
                                    $role = strtolower(auth()->user()->leaveType->name ?? '');
                                @endphp
                                {{--
                                <a href="#" class="btn btn-sm btn-info" title=" Leave Type Default Privileges (route not defined)">
                                    <i class="bi bi-gear"></i>
                                </a>
                                --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No Leave Types Found</td>
                        </tr>

                    @endforelse

                                </span>

                            </td>

                        </tr>


                </tbody>

            </table>

        </div>
<div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $leavetypetable->firstItem() ?? 0 }}
                to
                {{ $leavetypetable->lastItem() ?? 0 }}
                of
                {{ number_format($leavetypetable->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($leavetypetable->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $leavetypetable->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $leavetypetable->lastPage();
                            $current = $leavetypetable->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $leavetypetable->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $leavetypetable->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $leavetypetable->url($total) }}">{{ $total }}</a></li>
                        @endif
  
                        {{-- Next Page Link --}}
                        @if ($leavetypetable->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $leavetypetable->nextPageUrl() }}" rel="next">Next</a></li>
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

document.getElementById('tableSearch').addEventListener('keyup', function() {

    // ✅ Filter table rows by search value

    let value = this.value.toLowerCase();

    document.querySelectorAll('#leaveTypeTable tbody tr').forEach(row => {

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