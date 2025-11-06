@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Closed Feasibilities</h5>
                </div>

                <div class="card-body">
                    <!-- Check if there are records to display -->
                    @if($records->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <!-- Table headers -->
                                <thead class="table-dark-primary">
                                    <tr>
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
                                    <!-- Loop through each record and display in table rows -->
                                    @foreach($records as $index => $record)
                                        <tr>
                                            <!-- Display serial number -->
                                            <td>{{ $index + 1 }}</td>
                                            <!-- Display feasibility request ID -->
                                            <td>
                                                <span class="">{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                 <!-- Action buttons for View and Update -->
                                                <div class="btn-group" role="group">
                                                    <!-- View button with route to the view page -->
                                                    <a href="{{ route('operations.feasibility.view', $record->id) }}" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </div>
                                            </td>
                                            <!-- Display company name -->
                                            <td>{{ $record->feasibility->company->company_name ?? 'N/A' }}</td>
                                            <!-- Display client name -->
                                            <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>
                                            <!-- Display type of service -->
                                            <td>{{ $record->feasibility->type_of_service ?? 'N/A' }}</td>
                                            <!-- Display speed -->
                                            <td>{{ $record->feasibility->speed ?? 'N/A' }}</td>
                                            <!-- Display number of links -->
                                            <td>{{ $record->feasibility->no_of_links ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                    <!-- Message when no open feasibilities are found -->
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
@endsection