@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Client Master</h3>

        {{-- Add --}}

        @if($permissions->can_add)

        <a href="{{ route('clients.create') }}" class="btn btn-success">

            <i class="bi bi-plus-circle"></i> Add New Client

        </a>

         @endif
        


    </div>


    {{-- Success Message --}}

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif



    {{-- Client Table --}}

    <div class="card shadow border-0">

   <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

             {{--- Delete --}}
         @if($permissions->can_delete)
         <form id="bulkDeleteForm" action="{{ route('clients.bulk-delete') }}" method="POST" class="d-inline">
             @csrf
             <div id="bulkDeleteInputs"></div>
         </form>
<button id="deleteSelectedBtn" class="btn btn-danger d-none">
    <i class="bi bi-trash"></i>
</button>
@endif
        </div>

        <div class="card-body table-responsive">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

            <table class="table table-bordered table-hover align-middle" id="clientTable">

                <thead class="table-dark-primary text-center">

                    <tr>

                        <th><input type="checkbox" id="selectAll"></th>

                        <th>S.No</th>

                        <th class="col">Action</th>

                        <th class="col">Client Code</th>

                        <th class="col">Client Name</th>

                        <th class="col">Business Name</th>

                        <th class="col">Technical SPOC</th>
                        <th class="col">Technical SPOC Email</th>
                        <th class="col">Technical SPOC Mobile</th>


                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($clients as $key => $client)

                        <tr>

                              <td><input type="checkbox" class="rowCheckbox" value="{{ $client->id }}"></td>

                            <td class="text-center">{{ $key+1 }}</td>

                             <td class="text-center d-flex justify-content-center gap-1">

                                {{-- Edit --}}

                                @if($permissions->can_edit)

                               <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-primary">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                @endif
                                 {{-- Delete --}}

                                 @if($permissions->can_delete)

                                 <form action="{{ route('clients.destroy',$client) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('DELETE') 

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Client?')">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                                   @endif
                                   {{-- Toggle Status --}}

                                <form action="{{ route('clients.toggle-status', $client->id) }}" method="POST" class="d-inline">

                                    @csrf

                                    @method('PATCH')

                                     <button type="submit" class="btn btn-sm {{ $client->status == 'Active' ? 'btn-success' : 'btn-secondary' }}">

                                {{ $client->status }}

                                    </button>

                                </form>
                                {{-- View --}}

                                   @if($permissions->can_view)

                                   <a href="{{ route('clients.view', $client->id) }}" class="btn btn-sm btn-warning">

                                    <i class="bi bi-eye"></i>

                                    </a>

                                     @endif
                            </td>

                            <!-- <td>{{ $key+1 }}</td> -->

                            <td>{{ $client->client_code }}</td>

                            <td class="col">{{ $client->client_name }}</td>

                            <td class="col">{{ $client->business_display_name ?? '-' }}</td>

                            <td class="col">{{ $client->support_spoc_name ?? '-' }}</td>
                            <td class="col">{{ $client->support_spoc_email ?? '-' }}</td>
                            <td class="col">{{ $client->support_spoc_mobile ?? '-' }}</td>

                            <td>

                                <span class="badge {{ $client->status == 'Active' ? 'bg-success' : 'bg-danger' }}">

                                {{ $client->status }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="13" class="text-center text-muted">No clients found.</td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<script>
// 
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#clientTable tbody tr').forEach(row => {

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



<style>

    .col {

    width: 130px;

    white-space: nowrap;

}

</style>

@endsection

