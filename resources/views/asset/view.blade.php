@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Asset</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Asset ID</th>

                <td>{{ $asset->asset_id ?? '-' }}</td>

            </tr>

            <tr>

                <th>Company</th>

                <td>{{ $asset->company->company_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Asset Type</th>

                <td>{{ $asset->assetType->type_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Make</th>

                <td>{{ $asset->makeType->make_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Model</th>

                <td>{{ $asset->model ?? '-' }}</td>

            </tr>

            <tr>

                <th>Brand</th>

                <td>{{ $asset->brand ?? '-' }}</td>

            </tr>

            <tr>

                <th>Serial No</th>

                <td>{{ $asset->serial_no ?? '-' }}</td>

            </tr>

            <tr>

                <th>MAC No</th>

                <td>{{ $asset->mac_no ?? '-' }}</td>

            </tr>

            <tr>

                <th>Procured From</th>

                <td>{{ $asset->procured_from ?? '-' }}</td>

            </tr>

            <tr>

                <th>Purchase Date</th>

                <td>{{ $asset->purchase_date ?? '-' }}</td>

            </tr>

            <tr>

                <th>Warranty (year)</th>

                <td>{{ $asset->warranty ?? '-' }}</td>

            </tr>

            <tr>

                <th>Po No</th>

                <td>{{ $asset->po_no ?? '-' }}</td>

            </tr>

            <tr>

                <th>MRP</th>

                <td>{{ $asset->mrp ?? '-' }}</td>

            </tr>

            <tr>

                <th>Purchase Cost</th>

                <td>{{ $asset->purchase_cost ?? '-' }}</td>

            </tr>

        </table>



        <div class="text-end">

            <a href="{{ route('asset.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

@endsection