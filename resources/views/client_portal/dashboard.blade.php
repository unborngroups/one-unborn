@extends('client_portal.layout')

@section('title', 'Dashboard') 

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Welcome, {{ $client->business_name }}</h4>

    <div class="card shadow">
        <div class="card-header fw-bold">Your Active Links</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead>
                <tr>
                    <th>Service ID</th>
                    <th>Link Type</th>
                    <th>Router</th>
                    <th>Bandwidth</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($links as $link)
                    <tr>
                        <td>{{ $link->service_id }}</td>
                        <td>{{ $link->link_type }}</td>
                        <td>{{ $link->router->router_name ?? '-' }}</td>
                        <td>{{ $link->bandwidth }} Mbps</td>
                        <td>
                            <a href="{{ route('client.link.details', $link->id) }}" class="btn btn-sm btn-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach

                @if(count($links) == 0)
                    <tr><td colspan="6" class="text-center text-muted">No links found</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
