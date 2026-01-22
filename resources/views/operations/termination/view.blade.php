@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Termination Request</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Circuit ID</th>

                <td style="text-align: left;">{{ $termination->circuit_id ?? '-' }}</td>

            </tr>
            <tr>

                <th>Company Name</th>

                <td style="text-align: left;">{{ $termination->company_name ?? '-' }}</td> 

            <tr>

                <th>Address</th>

                <td style="text-align: left;">{{ $termination->address ?? '-' }}</td>

            </tr>

            <tr>

                <th>Bandwidth</th>

                <td style="text-align: left;">{{ $termination->bandwidth ?? '-' }}</td>

            </tr>

            <tr>

                <th>Asset ID</th>

                <td style="text-align: left;">{{ $termination->asset_id ?? '-' }}</td>

            <tr>
               
            <tr>
                <th>Asset MAC</th>
                <td style="text-align: left;">{{ $termination->asset_mac ?? '-' }}</td>
            </tr>
            <tr>
                <th>Asset Serial</th>
                <td style="text-align: left;">{{ $termination->asset_serial ?? '-' }}</td>
            <tr>


                <th>Date of Activation</th>
                <td style="text-align: left;">{{ $termination->date_of_activation ?? '-' }}</td>
            </tr>

            <tr>

                <th>Date of Last Renewal</th>

                <td style="text-align: left;">{{ $termination->date_of_last_renewal ?? '-' }}</td>

            </tr>

            <tr>

                <th>Date of Expiry</th>

                <td style="text-align: left;">{{ $termination->date_of_expiry ?? '-' }}</td>

            </tr>

            <tr>

                <th>Termination Request Date</th>

                <td style="text-align: left;">{{ $termination->termination_request_date ?? '-' }}</td>

            </tr>

            <tr>

                <th>Termination Requested By</th>

                <td style="text-align: left;">{{ $termination->termination_requested_by ?? '-' }}</td>

            </tr>

            <tr>

                <th>Termination Request Document</th>

                <td style="text-align: left;">{{ $termination->termination_request_document ?? '-' }}</td>

            </tr>

            <tr>

                <th>Termination Date</th>

                <td style="text-align: left;">{{ $termination->termination_date ?? '-' }}</td>

            </tr>

            <tr>

                <th>Status</th>

                <td style="text-align: left;">{{ $termination->status ?? '-' }}</td>

            </tr>

        </table>

        <div class="text-end">

            <a href="{{ route('operations.termination.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<style>
    td{
        text-align: left;
    
    }
</style>

@endsection