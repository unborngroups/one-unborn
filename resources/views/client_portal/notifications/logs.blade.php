@extends('client_portal.layout')

@section('content')
<div class="container mt-3">
    <h4 class="fw-bold">Notification Logs</h4>

    <table class="table table-bordered table-striped mt-3">
        <thead>
        <tr>
            <th>Sent At</th>
            <th>Alert Type</th>
            <th>Email</th>
            <th>Status</th>
            <th>Message</th>
        </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->sent_at }}</td>
                <td>{{ $log->alert_type }}</td>
                <td>{{ $log->sent_to_email }}</td>
                <td>
                    @if($log->status)
                        <span class="badge bg-success">Sent</span>
                    @else
                        <span class="badge bg-danger">Failed</span>
                    @endif
                </td>
                <td>{{ $log->message }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
