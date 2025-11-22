@extends('layouts.app')



@section('content')

<div class="container-fluid py-4">

    <div class="row">

        <div class="col-12">

            <div class="card shadow border-0">

                {{-- ✅ Header Section --}}

                <div class="card-header text-dark d-flex justify-content-between align-items-center">

                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Closed Feasibilities</h5>

                </div>



                <div class="card-body">

                    {{-- ✅ Check if any feasibility records exist --}}

                    @if($records->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-striped table-hover">

                                {{-- ✅ Table Headers --}}

                                <thead class="table-dark-primary">

                                    <tr>
                                        <th width="50" class="text-center"><input type="checkbox" id="select_all" style="width: 18px; height: 18px; cursor: pointer;"></th>

                                        <th>S.No</th>

                                        <th>Request ID</th>

                                        <th>Action</th>

                                        <th>Company Name</th>

                                        <th>Name</th>

                                        <th>Type of Service</th>

                                        <th>Speed</th>

                                        <th>Links</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    {{-- ✅ Loop through all records --}}

                                    @foreach($records as $index => $record)

                                        <tr>
                                            <td class="text-center">
                                    <input type="checkbox" class="row-checkbox" value="{{ $record->id }}" style="width: 18px; height: 18px; cursor: pointer;">
                                </td>

                                            {{-- Serial No --}}

                                            <td>{{ $index + 1 }}</td>

                                            {{-- Request ID --}}

                                            <td>

                                                <span class="">{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</span>

                                            </td>

                                            {{-- Action Buttons (View only) --}}

                                            <td>

                                                <div class="btn-group" role="group">

                                                    {{-- View button --}}

                                                    <a href="{{ route('sm.feasibility.view', $record->id) }}" 

                                                       class="btn btn-info btn-sm" title="View">

                                                        <i class="bi bi-eye"></i> View

                                                    </a>

                                                

                                                </div>

                                            </td>

                                            {{-- Company Name --}}

                                            <td>{{ $record->feasibility->company->company_name ?? 'N/A' }}</td>

                                            {{-- Client Name --}}

                                            <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>

                                            {{-- Type of Service --}}

                                            <td>{{ $record->feasibility->type_of_service ?? 'N/A' }}</td>

                                            {{-- Speed --}}

                                            <td>{{ $record->feasibility->speed ?? 'N/A' }}</td>

                                            {{-- No of Links --}}

                                            <td>{{ $record->feasibility->no_of_links ?? 'N/A' }}</td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                    {{-- ✅ Show message when no records found --}}

                        <div class="text-center py-4">

                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>

                            <h5 class="text-muted mt-3">No closed feasibilities found</h5>

                            <p class="text-muted">No feasibilities have been completed yet.</p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select_all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    selectAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = [...rowCheckboxes].every(x => x.checked);
            const noneChecked = [...rowCheckboxes].every(x => !x.checked);

            selectAll.checked = allChecked;
            selectAll.indeterminate = !allChecked && !noneChecked;
        });
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