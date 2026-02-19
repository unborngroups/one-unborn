@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-x-octagon me-2"></i>Not-Feasible Feasibilities</h5>
                    <form id="searchForm" method="GET" class="d-flex align-items-center w-25">
                        <input type="text" name="search" class="form-control form-control-sm w-100" placeholder="Search..." value="{{ $search ?? '' }}" oninput="this.form.submit()">
                        <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                    </form>
                </div>
                <div class="card-body">
                    @if($records->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark-primary">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Action</th>
                                        <th>Request ID</th>
                                        <th>Company Name</th>
                                        <th>Name</th>
                                        <th>Area / State</th>
                                        <th>Type of Service</th>
                                        <th>Speed</th>
                                        <th>Links</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $index => $record)
                                        <tr>
                                            <td>{{ ($records->currentPage() - 1) * $records->perPage() + $loop->iteration }}</td>
                                            <td>
                                              <!-- View button with route to the view page -->
                                                     @if($permissions->can_view)

                                                    <a href="{{ route('operations.feasibility.view', $record->id) }}" 

                                                       class="btn btn-info btn-sm" title="View">

                                                        <i class="bi bi-eye"></i> 

                                                    </a>
                                                    @endif  

                                                    @if($record->status === 'Not-Feasible')
                                                        <form action="{{ route('operations.feasibility.makefeasible', $record->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" title="Mark as Feasible" onclick="return confirm('Mark this feasibility as Feasible?');">
                                                                <i class="bi bi-check-circle"></i> <!-- Feasible -->
                                                            </button>
                                                        </form>
                                                        
                                                    @endif
                                            </td>
                                            <td>{{ $record->feasibility->feasibility_request_id ?? 'N/A' }}</td>
                                            <td>{{ $record->feasibility->company->company_name ?? 'N/A' }}</td>
                                            <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>
                                            <td>{{ $record->feasibility->area ?? $record->feasibility->state ?? 'N/A' }}</td>
                                            <td>{{ $record->feasibility->type_of_service ?? 'N/A' }}</td>
                                            <td>{{ $record->feasibility->speed ?? 'N/A' }}</td>
                                            <td>{{ $record->feasibility->no_of_links ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $records->links() }}
                    @else
                        <div class="alert alert-info">No Not-Feasible records found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
