@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
   <h4 class="text-primary fw-bold mb-3">Termination Requests</h4>
   <div class="mb-3">
	   <a href="{{ route('operations.termination.create') }}" class="btn btn-success">
		   <i class="bi bi-plus-circle"></i> Create Termination
	   </a>
   </div>
   @if(session('success'))
	   <div class="alert alert-success">{{ session('success') }}</div>
   @endif
   <div class="card shadow border-0 p-4">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>S.No</th>
					<th>Action</th>
					<th>Circuit ID</th>
					<th>Company Name</th>
					<th>Address</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				@forelse($terminations as $i => $termination)
				<tr>
					<td>{{ $i+1 }}</td>
					<td>
						<a href="{{ route('termination.view', $termination->id) }}" class="btn btn-info btn-sm">View</a>
						<a href="{{ route('termination.edit', $termination->id) }}" class="btn btn-primary btn-sm">Edit</a>
					</td>
					<td>{{ $termination->circuit_id }}</td>
					<td>{{ $termination->company_name }}</td>
					<td>{{ $termination->address }}</td>
					<td>{{ $termination->status }}</td>
				</tr>
				@empty
				<tr><td colspan="6" class="text-center">No records found.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection