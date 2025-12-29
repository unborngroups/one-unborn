@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Asset</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

            <!-- Asset ID -->
                <th>Asset ID</th>

                <td>{{ $asset->asset_id ?? '-' }}</td>

            </tr>

            <tr>

            <!-- Company name in company master -->
                <th>Company</th>

                <td>{{ $asset->company->company_name ?? '-' }}</td>

            </tr>

            <tr>
                <!-- asset type in asset_type master -->

                <th>Asset Type</th>

                <td>{{ $asset->assetType->type_name ?? '-' }}</td>

            </tr>

            <tr>
                <!-- make type in make_type master -->

                <th>Make</th>

                <td>{{ $asset->makeType->make_name ?? '-' }}</td>

            </tr>

            <tr>
                <!-- model -->

                <th>Model</th>

                <td>{{ $asset->model ?? '-' }}</td>

            </tr>

            <tr>
                <!-- brand -->

                <th>Brand</th>

                <td>{{ $asset->brand ?? '-' }}</td>

            </tr>

            <tr>

            <!-- Serial number -->
                <th>Serial No</th>

                <td>{{ $asset->serial_no ?? '-' }}</td>

            </tr>

            <tr>
            <!-- MAC number -->

                <th>MAC No</th>

                <td>{{ $asset->mac_no ?? '-' }}</td>

            </tr>

            <tr>
            <!-- Procured from -->

                <th>Procured From</th>

                <td>{{ $asset->procured_from ?? '-' }}</td>

            </tr>

            <tr>

                <!-- Purchase Date -->
                <th>Purchase Date</th>

                <td>{{ $asset->purchase_date ?? '-' }}</td>

            </tr>

            <tr>
                <!-- Warranty (year) -->

                <th>Warranty (year)</th>

                <td>{{ $asset->warranty ?? '-' }}</td>

            </tr>

            <tr>
                <!-- Purchase Order Number -->

                <th>Po No</th>

                <td>{{ $asset->po_no ?? '-' }}</td>

            </tr>

            <tr>
                <!-- MRP -->

                <th>MRP</th>

                <td>{{ $asset->mrp ?? '-' }}</td>

            </tr>

            <tr>
                <!-- Purchase Cost -->

                <th>Purchase Cost</th>

                <td>{{ $asset->purchase_cost ?? '-' }}</td>

            </tr>

        </table>



        <div class="text-end">

            <a href="{{ route('operations.asset.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

@endsection