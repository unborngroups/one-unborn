@extends('layouts.app')
@php use Milon\Barcode\Facades\DNS1DFacade as DNS1D; @endphp
@section('content')

<div class="container">
<h3 class="mb-3">Asset List</h3>

    @if($permissions->can_add)

        <a href="{{ route('asset.create') }}" class="btn btn-success float-end mb-3">

            <i class="bi bi-plus-circle"></i> Add New Asset

        </a>

         @endif
{{-- 🔍 Search box --}}

        <div class="card-header bg-light d-flex justify-content-between align-items-center">

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

            <div class="d-flex align-items-center gap-2">
                @if($permissions->can_delete)
                    <form id="bulkDeleteForm" action="{{ route('asset.bulk-delete') }}" method="POST" class="d-inline">
                        @csrf
                        <div id="bulkDeleteInputs"></div>
                    </form>
                    <button id="deleteSelectedBtn" class="btn btn-danger d-none">
                        <i class="bi bi-trash"></i>
                    </button>
                @endif

                @if($permissions->can_view)
                    <form id="bulkPrintForm" action="{{ route('asset.bulk-print') }}" method="POST" target="_blank" class="d-inline">
                        @csrf
                        <div id="bulkPrintInputs"></div>
                    </form> 
                    <button id="printSelectedBtn" class="btn btn-dark d-none">
                        <i class="bi bi-printer"></i>
                    </button>
                @endif
            </div>
        </div>

<table class="table table-bordered" id="asset">
<thead class="table-dark-primary">
<tr>
    <th><input type="checkbox" id="selectAll"></th>
    <th>S.No</th>
    <th>Action</th>
    <th>Asset ID / Barcode</th>
    <th>Brand</th>
    <th>Model</th>
    <th>Serial No</th>
    <th>Purchase Date</th>
    <th>Print</th>
    
</tr>
</thead>

<tbody>
@foreach($assets as $a)
<tr>
      <td><input type="checkbox" class="rowCheckbox" value="{{ $a->id }}"></td>

    <td>
        {{ $assets instanceof \Illuminate\Pagination\LengthAwarePaginator 
            ? $loop->iteration + ($assets->currentPage() - 1) * $assets->perPage() 
            : $loop->iteration }}
    </td>
    <td>
        <!-- edit action -->
    @if ($permissions->can_edit)
    <a href="{{ route('asset.edit', $a->id) }}" class="btn btn-sm btn-primary">Edit</a>
    @endif

        <!-- delete action -->
    @if ($permissions->can_delete)
            <form action="{{ route('asset.destroy', $a->id) }}" method="POST" style="display:inline-block;">
    @csrf
    @method('DELETE')
    <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
</form>
        @endif
        
 {{-- View --}}

                                   @if($permissions->can_view)

                                   <a href="{{ route('asset.view', $a->id) }}" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     @endif
           
    </td>

   <td>
   <img src="/barcode.php?code={{ $a->asset_id }}" height="2px" >
<p>{{ $a->asset_id }}</p>

</td>


    <td>{{ $a->brand }}</td>
    <td>{{ $a->model }}</td>
    <td>{{ $a->serial_no }}</td>
    <td>{{ $a->purchase_date }}</td>
    <!-- print button -->
    <td>
        <a href="{{ route('asset.print', $a->id) }}" class="btn btn-dark" target="_blank">
    <i class="bi bi-printer"></i> Print
</a>
    </td>
    
</tr>
@endforeach
</tbody>
</table>
</div>
<script>
    
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#asset tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});


document.getElementById('selectAll').addEventListener('change', function(){
    let isChecked = this.checked;
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = isChecked;
    });
    updateDeleteButtonVisibility();
});

document.getElementById('deleteSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    if (!confirm(`Delete ${selectedIds.length} selected asset(s)?`)) {
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

document.getElementById('printSelectedBtn')?.addEventListener('click', function () {
    const selectedIds = Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
    if (!selectedIds.length) {
        return;
    }

    const inputsContainer = document.getElementById('bulkPrintInputs');
    inputsContainer.innerHTML = '';
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        inputsContainer.appendChild(input);
    });

    document.getElementById('bulkPrintForm')?.submit();
});


function updateDeleteButtonVisibility() {
    const totalChecked = document.querySelectorAll('.rowCheckbox:checked').length;
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const printBtn = document.getElementById('printSelectedBtn');

    if (deleteBtn) {
        if (totalChecked > 0) {
            deleteBtn.classList.remove('d-none');
        } else {
            deleteBtn.classList.add('d-none');
        }
    }

    if (printBtn) {
        if (totalChecked > 0) {
            printBtn.classList.remove('d-none');
        } else {
            printBtn.classList.add('d-none');
        }
    }
}

document.querySelectorAll('.rowCheckbox').forEach(cb => {
    cb.addEventListener('change', updateDeleteButtonVisibility);
});

// Keep the delete button state correct on page load
updateDeleteButtonVisibility();

</script>

@endsection
