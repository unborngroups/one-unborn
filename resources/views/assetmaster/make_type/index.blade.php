@extends('layouts.app')

@section('content')

<div class="container py-4">
    <h3 class="mb-3 text-dark-primary float-start">Make Type List</h3>

    <a href="{{ route('assetmaster.make_type.create') }}" class="btn btn-success mb-3 float-end">+ Add Make Type</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th width="140">Actions</th>
                <!-- <th>Company</th> -->
                <th>Make Name</th>
                <th>Created Date</th>
                
            </tr>
        </thead>
        <tbody>
            @forelse($makeTypes as $key => $mk)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <a href="{{ route('assetmaster.make_type.edit', $mk->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('assetmaster.make_type.destroy', $mk->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                    <td>{{ $mk->make_name }}</td>
                    <td>{{ $mk->created_at->format('d-m-Y') }}</td>
                    
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">No records found</td></tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
