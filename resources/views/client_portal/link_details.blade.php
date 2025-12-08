@extends('client_portal.layout')

@section('content')
<div class="container mt-3">
    @if(!$link)
        <div class="alert alert-info">No links found for your account.</div>
    @else
        <h4 class="fw-bold">{{ $link->service_id }} — Link Details</h4>

    <div class="row mt-3">
        <div class="col-md-4">
            <table class="table table-bordered">
                <tr><th>Service ID</th><td>{{ $link->service_id }}</td></tr>
                <tr><th>Link Type</th><td>{{ $link->link_type }}</td></tr>
                <tr><th>Router</th><td>{{ $link->router->router_name ?? 'No Router Assigned'}}</td></tr>
                <tr><th>Bandwidth</th><td>{{ $link->bandwidth }} Mbps</td></tr>
                <tr><th>Status</th>
                    <td><span class="badge bg-success" id="live_status">Checking…</span></td>
                </tr>
            </table>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header fw-bold">Live Traffic</div>
                <div class="card-body">
                    <canvas id="liveChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
        <a href="{{ route('client.sla.reports', $link->id) }}" class="btn btn-secondary mt-3">View SLA Reports</a>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($link)
<script>
setInterval(() => {
    fetch("{{ route('client.live.traffic', $link->id) }}")
        .then(r => r.json())
        .then(data => {
            document.getElementById('live_status').innerHTML =
                data.link_up ? '<span class="badge bg-success">UP</span>' :
                               '<span class="badge bg-danger">DOWN</span>';
        });
}, 3000);
</script>
@endif
@endsection
