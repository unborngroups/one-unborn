@extends('layouts.app')

@section('content')

<div class="container py-4">
    <h3 class="mb-3 text-primary float-start">Asset Type List</h3>

    <a href="{{ route('assetmaster.asset_type.create') }}" class="btn btn-success mb-3 float-end">+ Add Asset Type</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Company</th>
                <th>Asset Type</th>
                <th>Created Date</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assetTypes as $key => $at)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $at->company->company_name }}</td>
                    <td>{{ $at->type_name }}</td>
                    <td>{{ $at->created_at->format('d-m-Y') }}</td>
                    <td>
                        <a href="{{ route('assetmaster.asset_type.edit', $at->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('assetmaster.asset_type.destroy', $at->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
