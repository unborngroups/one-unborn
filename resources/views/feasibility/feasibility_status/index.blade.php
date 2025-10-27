@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary">Feasibility Status - {{ ucfirst($status) }}</h4>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        @foreach($statuses as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $tab == $status ? 'active' : '' }}"
                   href="{{ route('feasibility.status.index', $tab) }}">
                   {{ $tab }}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>S.No</th>
                        <th>Client</th>
                        <th>Vendor</th>
                        <th>ARC</th>
                        <th>OTC</th>
                        <th>Delivery Timeline</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $key => $record)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>
                            <td>{{ $record->vendor_name ?? '-' }}</td>
                            <td>{{ $record->arc ?? '-' }}</td>
                            <td>{{ $record->otc ?? '-' }}</td>
                            <td>{{ $record->delivery_timeline ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $record->status == 'Closed' ? 'success' : ($record->status == 'InProgress' ? 'warning' : 'secondary') }}">
                                    {{ $record->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('feasibility.status.view', $record->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Update
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
