@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">HR - User Profiles</h2>

    @if($users->isEmpty())
        <div class="alert alert-info">No users found.</div>
    @else
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-dark-primary">
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Designation</th>
                            <th>Profile Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name ?? ($user->profile->fname.' '.$user->profile->lname ?? '-') }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ optional($user->profile)->designation ?? '-' }}</td>
                                <td>
                                    @if($user->profile_created && $user->profile)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->profile)
                                        <a href="{{ route('hr.view', $user->id) }}" class="btn btn-sm btn-warning">View</a>
                                        <a href="{{ route('hr.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    @else
                                        <span class="text-muted">No profile</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
