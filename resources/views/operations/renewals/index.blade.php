@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Upcoming Renewals</h3>

        {{-- Add --}}

        @if($permissions->can_add)

        <a href="{{ route('operations.renewals.create') }}" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Renewal

        </a>

         @endif
        


    </div>


    {{-- Success Message --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


   <div class="card-header bg-light d-flex justify-content-between">
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


    {{-- Client Table --}}

    <div class="card shadow border-0">

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle" id="renewalTable">

                <thead class="table-dark-primary text-center">

                    <tr>
                        <th>S.No</th>
                        <th>Action</th>
                        <th>Client Name</th>
                        <th>Area State</th>
                        <th>Circuit_ID</th>
                        <th>Date of Renewal(Y-m-d)</th>
                        <th>Date of Expiry(d-m-Y)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($renewals as $key => $renewal)
                        @php
                            $plan = optional($renewal->deliverable)
                                ?->deliverablePlans
                                ?->sortBy('link_number')
                                ->first();
                            $displayCircuitId = $plan->circuit_id ?? $renewal->circuit_id ?? '-';
                        @endphp
                        <tr data-renewal-row-id="{{ $renewal->id }}">
                            <td class="text-center">{{ $key+1 }}</td>
                            <td class="text-center d-flex justify-content-center gap-1">
                                {{-- Edit --}}
                                @if($permissions->can_edit && $renewal->id)
                                    <a href="{{ route('operations.renewals.edit', $renewal) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                                {{-- Renew --}}
                                <form action="{{ route('operations.renewals.quick-renew', $renewal->id) }}" method="POST" class="d-inline quick-renew-form" data-renewal-id="{{ $renewal->id }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success quick-renew-btn">
                                        <i class="bi bi-arrow-repeat"></i> Renew
                                    </button>
                                </form>
                                {{-- Delete --}}
                                @if($permissions->can_delete && $renewal->id)
                                    <form action="{{ route('operations.renewals.destroy',$renewal) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                {{-- Toggle Status --}}
                                @if($renewal->id)
                                <form action="{{ route('operations.renewals.toggle-status', $renewal->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $renewal->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $renewal->status ?? 'Inactive' }}
                                    </button>
                                </form>
                                @endif
                                {{-- View --}}
                                @if($permissions->can_view && $renewal->id)
                                    <a href="{{ route('operations.renewals.view', $renewal->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endif
                            </td>
                                
                            <td>{{ $renewal->deliverable->feasibility->client->client_name ?? '-' }}</td>
                            <td>{{ $renewal->deliverable->feasibility->area ?? '-' }}, {{ $renewal->deliverable->feasibility->state ?? '-' }}</td>
                            <td>{{ $displayCircuitId }}</td>
                            <td class="renewal-date-cell">{{ $renewal->date_of_renewal ? \Carbon\Carbon::parse($renewal->date_of_renewal)->format('Y-m-d') : '-' }}</td>
                    
                            <td class="renewal-expiry-cell">
                                    @if($renewal->new_expiry_date)
                                        <span class="text-green-600">{{ \Carbon\Carbon::parse($renewal->new_expiry_date)->format('d-m-Y') }}</span>
                                    @elseif(isset($renewal->deliverablePlan) && $renewal->deliverablePlan->expiry_date)
                                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($renewal->deliverablePlan->expiry_date)->format('d-m-Y') }}</span>
                                    @else
                                        <span class="text-red-500">-</span>
                                    @endif
                                </td>
                           
                            <td class="renewal-status-cell">
                                @if($renewal->id)
                                    <form action="{{ route('operations.renewals.toggle-status', $renewal->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $renewal->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $renewal->status ?? 'Inactive' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted">No renewals found.</td>
                            </tr>
                        @endforelse

       <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $renewals->firstItem() ?? 0 }}
                to
                {{ $renewals->lastItem() ?? 0 }}
                of
                {{ number_format($renewals->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($renewals->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $renewals->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $renewals->lastPage();
                            $current = $renewals->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $renewals->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $renewals->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $renewals->url($total) }}">{{ $total }}</a></li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($renewals->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $renewals->nextPageUrl() }}" rel="next">Next</a></li>
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

    let value = this.value.toLowerCase();

    document.querySelectorAll('#renewalTable tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});

const selectAll = document.getElementById('selectAll');
if (selectAll) {
    selectAll.addEventListener('change', function(){
        let isChecked = this.checked;
        document.querySelectorAll('.rowCheckbox').forEach(cb => {
            cb.checked = isChecked;
        });
        updateDeleteButtonVisibility();
    });
}

document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected renewal(s)?`)) {
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

document.querySelectorAll('.quick-renew-form').forEach(form => {
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn = form.querySelector('.quick-renew-btn');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Renewing...';

        const tokenInput = form.querySelector('input[name="_token"]');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': tokenInput ? tokenInput.value : ''
                }
            });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            const data = await response.json();
            if (!data.ok) {
                throw new Error(data.message || 'Unable to renew');
            }

            const row = form.closest('tr');
            if (!row) return;

            const dateCell = row.querySelector('.renewal-date-cell');
            const expiryCell = row.querySelector('.renewal-expiry-cell');
            const statusCell = row.querySelector('.renewal-status-cell button');

            if (dateCell) {
                dateCell.textContent = data.date_of_renewal || '-';
            }

            if (expiryCell) {
                expiryCell.innerHTML = '<span class="text-green-600">' + (data.new_expiry_date || '-') + '</span>';
            }

            if (statusCell) {
                statusCell.textContent = data.status || 'Active';
                statusCell.classList.remove('btn-secondary');
                statusCell.classList.add('btn-success');
            }
        } catch (error) {
            alert('Direct renew failed. Please refresh and try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    });
});
</script>



@endsection