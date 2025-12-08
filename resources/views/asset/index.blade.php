@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="fw-bold text-primary mb-0">Asset</h3>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <form class="d-flex gap-2" method="GET" action="{{ route('asset.index') }}">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Asset/Vendor" value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
            </form>
            <a href="{{ route('asset.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @if($permissions->can_add)
                <a href="{{ route('asset.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle"></i> Create Asset
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow border-0">

        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark-primary text-center">
                    <tr>
                        <th>#</th>
                        <th>Asset ID</th>
                        <th>Procured From</th>
                        <th>Purchase Date</th>
                        <th>Warranty</th>
                        <th>PO Number</th>
                        <th>MRP</th>
                        <th>Purchase Cost</th>
                        <th>Make</th>
                        <th>Brand</th>
                        <th>MAC</th>
                        <th>Serial No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $index => $asset)
                        <tr>
                            <td class="text-center">{{ $assets->firstItem() ? $assets->firstItem() + $index : $index + 1 }}</td>
                            <td>{{ $asset->asset_id }}</td>
                            <td>{{ $asset->procured_from ?? '-' }}</td>
                            <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $asset->warranty ?? '-' }}</td>
                            <td>{{ $asset->po_number ?? '-' }}</td>
                            <td>{{ $asset->mrp ?? '-' }}</td>
                            <td>{{ $asset->purchase_cost ?? '-' }}</td>
                            <td>{{ optional($asset->make)->make_name ?? '-' }}</td>
                            <td>{{ $asset->brand ?? '-' }}</td>
                            <td>{{ $asset->mac_number ?? '-' }}</td>
                            <td>{{ $asset->serial_no ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $asset->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($permissions->can_view)
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('vendors.view', $asset->id) }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted">No assets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $assets->links() }}
            </div>
        @endif

    </div>

</div>

@endsection