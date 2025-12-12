@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0 float-start"><i class="bi bi-check-circle me-2"></i>Delivered / Closed Deliverables</h5>
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25 float-end" placeholder="Search...">

        </div>
        <div class="card-header bg-light d-flex justify-content-between">

            <!-- <h5 class="mb-0 text-danger">MANAGE USER</h5> -->


        </div>
        
        <div class="card-body">
            @if($records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="delivery">
                        <thead class="table-dark">
                            <tr>
                                <!-- <th width="50"><input type="checkbox" id="select_all"></th> -->
                                <th width="50">S.No</th>
                                <th width="150">Action</th>
                                <th class="col">PO Number</th>
                                <th class="col">Feasibility ID</th>
                                <th class="col">Client Name</th>
                                <th class="col">Address</th>
                                <th class="col">Speed</th>
                                <th class="col">No. of Links</th>
                                <th class="col">Vendor</th>
                                <!-- <th class="col">Delivered At</th> -->
                                <th class="col">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($records as $index => $record)
                            <tr>
                                <!-- <td>
                                    <input type="checkbox" class="row-checkbox" value="{{ $record->id }}">
                                </td> -->

                                <td>{{ $index + 1 }}</td>

                                <td>
                                    @if($permissions->can_view)
                                    <a href="{{ route('operations.deliverables.view', $record->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    @endif
                                </td>

                                <td>{{ $record->po_number ?? 'N/A' }}</td>

                                <td>{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</td>

                                <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>

                                <td>{{ $record->site_address ?? 'N/A' }}</td>

                                <td>{{ $record->speed_in_mbps ?? 'N/A' }}</td>

                                <td>{{ $record->no_of_links ?? 'N/A' }}</td>

                                <td>{{ $record->vendor ?? 'N/A' }}</td>
<!-- 
                                <td>
                                    {{ $record->delivered_at 
                                        ? \Carbon\Carbon::parse($record->delivered_at)->format('d-m-Y') 
                                        : 'N/A' }}
                                </td> -->

                                <td>
                                    <span class="badge bg-success">Delivered</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No Delivered Deliverables Found</h5>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// document.addEventListener('DOMContentLoaded', function() {
//     const selectAll = document.getElementById('select_all');
//     const rowCheckboxes = document.querySelectorAll('.row-checkbox');

//     selectAll.addEventListener('change', function() {
//         rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
//     });

//     rowCheckboxes.forEach(cb => {
//         cb.addEventListener('change', function() {
//             const allChecked = [...rowCheckboxes].every(x => x.checked);
//             const noneChecked = [...rowCheckboxes].every(x => !x.checked);

//             selectAll.checked = allChecked;
//             selectAll.indeterminate = !allChecked && !noneChecked;
//         });
//     });
// });

document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#delivery tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});


</script>

<style>
.col {
    width: 130px;
    white-space: nowrap;
}
.table th,  .table td {

    width: 130px;

    white-space: nowrap;

}
</style>

@endsection
