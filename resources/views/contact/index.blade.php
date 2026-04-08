@extends('layouts.app')

@section('title', ucfirst($type) . ' Contact')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ ucfirst($type) }} Contact</h4>
        @if($permissions->can_add)
            <a href="{{ route('contacts.create', $type) }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Create
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark-primary">
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Area</th>
                        <th>State</th>
                        <th>Contact1</th>
                        <th>Contact2</th>
                        <th width="260">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td>{{ $contacts->firstItem() + $loop->index }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->area ?: '-' }}</td>
                            <td>{{ $contact->state ?: '-' }}</td>
                            <td>{{ $contact->contact1 }}</td>
                            <td>{{ $contact->contact2 ?: '-' }}</td>
                            <td>
                                @if($permissions->can_edit)
                                    <a href="{{ route('contacts.edit', [$type, $contact->id]) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif

                                @if($permissions->can_view)
                                    <a href="{{ route('contacts.show', [$type, $contact->id]) }}" class="btn btn-info btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @endif

                                @if($permissions->can_delete)
                                    <form action="{{ route('contacts.destroy', [$type, $contact->id]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this contact?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($permissions->can_edit)
                                    <form action="{{ route('contacts.toggle-status', [$type, $contact->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ strtolower($contact->status) === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                            {{ strtolower($contact->status) === 'active' ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No contacts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
