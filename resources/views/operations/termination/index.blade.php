@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">

   <h4 class="text-primary fw-bold mb-3">Termination Requests</h4>
   @if($permissions->can_add)
	   <a href="{{ route('operations.termination.create') }}" class="btn btn-success">
		   <i class="bi bi-plus-circle"></i> Create Termination
	   </a>
   @endif
	</div>


   @if(session('success'))
	   <div class="alert alert-success">{{ session('success') }}</div>
   @endif
   <div class="card shadow border-0 p-4">
	{{-- 🔍 Search box --}}


        <div class="card-header bg-light d-flex justify-content-between">
            <form id="filterForm" method="GET" class="d-flex align-items-center gap-2 w-100">
                <label for="entriesSelect" class="mb-0">Show</label>
                <select id="entriesSelect" name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm w-25" placeholder="Search..." onkeyup="this.form.submit();">
            </form>

            {{-- Delete --}}
            @if($permissions->can_delete)
            <form id="bulkDeleteForm" action="{{ route('operations.termination.bulk-delete') }}" method="POST" class="d-inline">
                @csrf
                <div id="bulkDeleteInputs"></div>
            </form>
            <button id="deleteSelectedBtn" class="btn btn-danger d-none">
                <i class="bi bi-trash"></i>
            </button>
            @endif
        </div>

		<div class="card-body table-responsive">
		<table class="table table-bordered table-hover align-middle" id="TerminationTable">
			<thead class="table-dark-primary text-center">
				<tr>
                    <th><input type="checkbox" id="selectAll"></th>
					<th>S.No</th>
					<th>Action</th>
					<th>Circuit ID</th>
					<th>Company Name</th>
					<th>Address</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				@forelse($terminations as $i => $termination)
				<tr>
                    <td><input type="checkbox" class="rowCheckbox" value="{{ $termination->id }}"></td>
					<td>{{ $i+1 }}</td>
					<td>
						<a href="{{ route('operations.termination.view', $termination->id) }}" class="btn btn-info btn-sm">View</a>
						<a href="{{ route('operations.termination.edit', $termination->id) }}" class="btn btn-primary btn-sm">Edit</a>
						<form action="{{ route('operations.termination.destroy', $termination->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Are you sure you want to delete this termination?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> </button>
						</form>
					</td>
					<td>{{ $termination->circuit_id }}</td>
					<td>{{ $termination->company_name }}</td>
					<td>{{ $termination->address }}</td>
					<td>
						@if($termination->termination_date)
							<strong class="text-success">Terminated</strong>
						@elseif($termination->termination_request_date)
							<strong class="text-warning">Pending</strong>
						@else
							-
						@endif
					</td>
				</tr>
				@empty
				<tr><td colspan="6" class="text-center">No records found.</td></tr>
				@endforelse
			</tbody>
		</table>
		</div>

		<!--  -->

		<div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $terminations->firstItem() ?? 0 }}
                to
                {{ $terminations->lastItem() ?? 0 }}
                of
                {{ number_format($terminations->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($terminations->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $terminations->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $terminations->lastPage();
                            $current = $terminations->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $terminations->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $terminations->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $terminations->url($total) }}">{{ $total }}</a></li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($terminations->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $terminations->nextPageUrl() }}" rel="next">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>

		<!--  -->

	</div>
</div>


<script>


// (No client-side search, now server-side search is used)



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

    if (!confirm(`Delete ${selectedIds.length} selected termination(s)?`)) {
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

@endsection