@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">User Login Report</h3>

    <div class="card shadow p-3 border-0">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>S.No</th>
                    <th>User Name</th>
                    <th>Login Time</th>
                    <th>Logout Time</th>
                    <th>Total Minutes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $key => $log)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $log->user->name }}</td>
                    <td>{{ $log->login_time }}</td>
                    <td>{{ $log->logout_time ?? 'Active Now' }}</td>
                    <td>{{ $log->total_minutes ?? 'Calculating...' }}</td>
                    <td>
                        @if($log->status === 'Online')
                            <span class="badge bg-success">Online</span>
                        @else
                            <span class="badge bg-secondary">Offline</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
