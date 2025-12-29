@extends('client_portal.layout')

@section('content')
<div class="container mt-3">

    @if(!$link)
        <div class="alert alert-info">
            No links found for your account.
        </div>
    @else

        <h4 class="fw-bold mb-3">
            {{ $link->service_id }} — Link Details
        </h4>

        <div class="row">

            {{-- LEFT PANEL --}}
            <div class="col-md-4">

                {{-- LINK DETAILS --}}
                <table class="table table-bordered">
                    <tr>
                        <th>Service ID</th>
                        <td>{{ $link->service_id }}</td>
                    </tr>
                    <tr>
                        <th>Link Type</th>
                        <td>{{ $link->link_type }}</td>
                    </tr>
                    <tr>
                        <th>Router</th>
                        <td>{{ $link->router->router_name ?? 'No Router Assigned' }}</td>
                    </tr>
                    <tr>
                        <th>Bandwidth</th>
                        <td>{{ $link->bandwidth }} Mbps</td>
                    </tr>
                    <tr>
                        <th>Live Status</th>
                        <td>
                            <span class="badge bg-secondary" id="live_status">
                                Checking…
                            </span>
                        </td>
                    </tr>
                </table>

                {{-- SLA SUMMARY --}}
                @if(isset($sla))
                <div class="card mt-3">
                    <div class="card-header fw-bold">
                        SLA Summary ({{ $sla['period'] }})
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Availability:</strong>
                            {{ $sla['availability'] }} %
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <span class="badge 
                                {{ $sla['status'] === 'PASS' ? 'bg-success' : 'bg-danger' }}">
                                {{ $sla['status'] }}
                            </span>
                        </p>

                        <p class="text-muted mb-0">
                            SLA Target: {{ $sla['sla_target'] }} %
                        </p>
                    </div>
                </div>
                @else
                <div class="alert alert-warning mt-3">
                    SLA not calculated yet.
                </div>
                @endif

                {{-- SLA REPORT LINK --}}
                <a href="{{ route('client.sla.reports', $link->id) }}"
                   class="btn btn-secondary w-100 mt-3">
                    View SLA Reports
                </a>

            </div>

            {{-- RIGHT PANEL --}}
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header fw-bold">
                        Live Traffic
                    </div>
                    <div class="card-body">
                        <canvas id="liveChart" height="150"></canvas>
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($link)
<script>
    setInterval(() => {
        fetch("{{ route('client.live.traffic', $link->id) }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('live_status').innerHTML =
                    data.link_up
                        ? '<span class="badge bg-success">UP</span>'
                        : '<span class="badge bg-danger">DOWN</span>';
            })
            .catch(() => {
                document.getElementById('live_status').innerHTML =
                    '<span class="badge bg-warning">UNKNOWN</span>';
            });
    }, 3000);
</script>
@endif

@endsection
