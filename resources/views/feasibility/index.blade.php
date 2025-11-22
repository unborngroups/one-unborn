@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h3 class="fw-bold text-primary">Manage feasibility</h3>

       {{-- Add --}}

    </div>


    @if(session('success'))

        <div class="alert alert-success">{{ session('success') }}</div>

    @endif



    <div class="card shadow border-0">

        <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->

            <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search...">

        </div>

        <div class="card-body table-responsive">

            <!-- <input type="text" id="tableSearch" class="form-control form-control-sm w-25" placeholder="Search..."> -->

            <table class="table table-bordered table-hover align-middle" id="userTable">

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

                                <a href="{{ route('feasibility.edit', $feasibility) }}" class="btn btn-sm btn-primary" title="Edit">

                                    <i class="bi bi-pencil"></i>

                                </a>

                                 {{-- View --}}

                                   <a href="{{ route('feasibility.show', $feasibility->id) }}" class="btn btn-sm btn-info" title="View">

                                    <i class="bi bi-eye"></i>

                                    </a>


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

    </div>  

</div>

<script>

document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#userTable tbody tr').forEach(row => {

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

