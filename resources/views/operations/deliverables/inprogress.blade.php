@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="bi bi-hourglass-half me-2 float-start"></i>In Progress Deliverables
            <input type="text" id="tableSearch" class="form-control form-control-sm w-25 float-end" placeholder="Search...">

            </h5>
        </div>
        
        <div class="card-body">
            @if($records->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center" id="inprogress">
                        <thead class="table-dark text-center align-middle">
                            <tr>
                                <!-- <th width="50">
                                    <input type="checkbox" id="select_all" class="form-check-input">
                                </th> -->
                                <th width="50">S.No</th>
                                <th width="150">Action</th>
                                <th>PO Number</th>
                                <th>PO Date</th>
                                <th>Client Name</th>
                                <th>No. of Links</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($records as $index => $record)
                            <tr>
                                <!-- <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" value="{{ $record->id }}">
                                </td> -->

                                <td>{{ $index + 1 }}</td>

                                <td>
                                    @if($permissions->can_edit)
                                    <a href="{{ route('operations.deliverables.edit', $record->id) }}" 
                                       class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    @endif
                                    @if($permissions->can_view)
                                    <a href="{{ route('operations.deliverables.view', $record->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    @endif
                                </td>

                                <td>{{ $record->po_number ?? 'N/A' }}</td>

                                <td>
                                    {{ $record->po_date ? \Carbon\Carbon::parse($record->po_date)->format('d-m-Y') : 'N/A' }}
                                </td>

                                <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>

                                <td>{{ $record->no_of_links ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                    <h5 class="text-muted mt-3">No In Progress Deliverables Found</h5>
                    <p class="text-muted">There are currently no deliverables in "In Progress" status.</p>
                </div>
            @endif
        </div>
    </div>
</div>


<script>
// document.addEventListener('DOMContentLoaded', function() {

//     const selectAll = document.getElementById('select_all');
//     const rowCheckboxes = document.querySelectorAll('.row-checkbox');

//     if (selectAll) {
//         selectAll.addEventListener('change', function() {
//             rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
//         });
//     }

//     rowCheckboxes.forEach(cb => {
//         cb.addEventListener('change', function() {
//             const allChecked = [...rowCheckboxes].every(c => c.checked);
//             const noneChecked = [...rowCheckboxes].every(c => !c.checked);

//             if (selectAll) {
//                 selectAll.checked = allChecked;
//                 selectAll.indeterminate = !allChecked && !noneChecked;
//             }
//         });
//     });
// });
document.getElementById('tableSearch').addEventListener('keyup', function() {

    let value = this.value.toLowerCase();

    document.querySelectorAll('#inprogress tbody tr').forEach(row => {

        row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';

    });

});
</script>
<style>
.table th,  .table td {

    width: 130px;

    white-space: nowrap;

}
</style>
@endsection
