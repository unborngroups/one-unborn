@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Vendor Master</h3>

         {{-- ✅ Show “Add Vendor” button only if user has permission --}}

        @if($permissions->can_add)

        <a href="{{ route('vendors.create') }}" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Vendor

        </a>

         @endif

    </div>



    {{-- ✅ Success message after create/update/delete --}}

    {{-- Success Message --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif



     {{-- 🧾 Vendor Table Section --}}

    <div class="card shadow border-0">

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
            <form id="bulkDeleteForm" action="{{ route('vendors.bulk-delete') }}" method="POST" class="d-inline">
                @csrf
                <div id="bulkDeleteInputs"></div>
            </form>
            <button id="deleteSelectedBtn" class="btn btn-danger d-none">
                <i class="bi bi-trash"></i>
            </button>
            @endif
        </div>


            

        {{-- 📊 Table with vendor data --}}

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle" id="vendorTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        {{-- ✅ Checkbox for bulk select --}}

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th>Action</th>

                        <th class="col">Vendor Code</th>

                        <th class="col">Vendor Name</th>

                        <th class="col">Business Name</th>

                        <th class="col">Contact Person</th>

                        <th class="col">Contact Email</th>

                        <th class="col">Contact Mobile</th>

                        <!-- <th>GSTIN</th> -->

                        <!-- <th>PAN No</th> -->

                        <!-- <th>bank_account_no</th> -->

                        <!-- <th>ifsc_code</th> -->

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>


                    @forelse($vendors as $key => $vendor)
                        <tr>
                            <td><input type="checkbox" class="rowCheckbox" value="{{ $vendor->id }}"></td>
                            <td class="text-center">{{ ($vendors->currentPage() - 1) * $vendors->perPage() + $key + 1 }}</td>

                            <td class="text-center d-flex justify-content-center gap-1">

                                 {{-- ✏️ Edit button (only if permission allowed) --}}

                                @if($permissions->can_edit)

                                <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                @endif



                               {{-- 🗑️ Delete button --}}

                                 @if($permissions->can_delete)

                                <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('DELETE') 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Vendor?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                @endif



                                {{-- 🔁 Toggle Active/Inactive --}}

                                <form action="{{ route('vendors.toggle-status', $vendor->id) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('PATCH')

                                    <button type="submit" class="btn btn-sm {{ $vendor->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">

                                        {{ $vendor->status }}

                                    </button>

                                </form>

                                 {{-- View button --}}

                                   @if($permissions->can_view)

                                <!-- view path -->

                                   <a href="{{ route('vendors.view', $vendor->id) }}" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                    @endif

                                

                            </td>

                            <td>{{ $vendor->vendor_code }}</td>

                            <td class="col">{{ $vendor->vendor_name }}</td>

                            <td class="col">{{ $vendor->business_display_name ?? '-' }}</td>

                            <td class="col">{{ $vendor->contact_person_name ?? '-' }}</td>

                            <td>{{ $vendor->contact_person_email ?? '-' }}</td>

                            <td>{{ $vendor->contact_person_mobile ?? '-' }}</td>

                            <!-- <td>{{ $vendor->gstin ?? '-' }}</td> -->

                            <!-- <td>{{ $vendor->pan_no ?? '-' }}</td> -->

                            <!-- <td>{{ $vendor->bank_account_no ?? '-'}}</td> -->

                            <!-- <td>{{ $vendor->ifsc_code ?? '-'}}</td> -->

                            {{-- 🟢🔴 Status Badge --}}

                            <td>

                                <span class="badge {{ $vendor->status == 'Active' ? 'bg-success' : 'bg-danger' }}">

                                    {{ $vendor->status }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="12" class="text-center text-muted">No vendors found.</td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap">
            <div class="text-muted small">
                Showing
                {{ $vendors->firstItem() ?? 0 }}
                to
                {{ $vendors->lastItem() ?? 0 }}
                of
                {{ number_format($vendors->total()) }} entries
            </div>
            <div class="ms-auto">
                <nav>
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($vendors->onFirstPage())
                            <li class="page-item disabled"><span class="page-link">Previous</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $vendors->previousPageUrl() }}" rel="prev">Previous</a></li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $total = $vendors->lastPage();
                            $current = $vendors->currentPage();
                            $max = 5; // Number of page links to show
                            $start = max(1, $current - floor($max / 2));
                            $end = min($total, $start + $max - 1);
                            if ($end - $start < $max - 1) {
                                $start = max(1, $end - $max + 1);
                            }
                        @endphp

                        @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="{{ $vendors->url(1) }}">1</a></li>
                            @if ($start > 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            @if ($i == $current)
                                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $vendors->url($i) }}">{{ $i }}</a></li>
                            @endif
                        @endfor

                        @if ($end < $total)
                            @if ($end < $total - 1)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $vendors->url($total) }}">{{ $total }}</a></li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($vendors->hasMorePages())
                            <li class="page-item"><a class="page-link" href="{{ $vendors->nextPageUrl() }}" rel="next">Next</a></li>
                        @else
                            <li class="page-item disabled"><span class="page-link">Next</span></li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>

    </div>

</div>

 {{-- 🔍 Table Search & Select All Script --}}

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