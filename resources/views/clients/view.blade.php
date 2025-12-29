@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View Company</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Client Name</th>

                <td style="text-align: left;">{{ $client->client_name ?? '-' }}</td>

            </tr>
            <tr>

                <th>Short Name</th>

                <td style="text-align: left;">{{ $client->short_name ?? '-' }}</td> 

            <tr>

                <th>Client Code</th>

                <td style="text-align: left;">{{ $client->client_code ?? '-' }}</td>

            </tr>

            <tr>

                <th>Business Display Name</th>

                <td style="text-align: left;">{{ $client->business_display_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Office Type</th>

                <td style="text-align: left;">{{ ucfirst($client->office_type) ?? '-' }}</td>

            <tr>
               

            <tr>
                <th>Head Office</th>
                <td style="text-align: left;">
                    @if($client->office_type === 'Branch' && $client->headOffice)
                        {{ $client->headOffice->client_name }} ({{ $client->headOffice->client_code }})
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td style="text-align: left;">{{ $client->pan_number ?? '-' }}</td>
            </tr>

            <tr>

                <th>Address 1</th>

                <td style="text-align: left;">{{ $client->address1 ?? '-' }}</td>

            </tr>

            <tr>

                <th>Address 2</th>

                <td style="text-align: left;">{{ $client->address2 ?? '-' }}</td>

            </tr>

            <tr>

                <th>Address 3</th>

                <td style="text-align: left;">{{ $client->address3 ?? '-' }}</td>

            </tr>

            <tr>

                <th>City</th>

                <td style="text-align: left;">{{ $client->city ?? '-' }}</td>

            </tr>

            <tr>

                <th>State</th>

                <td style="text-align: left;">{{ $client->state ?? '-' }}</td>

            </tr>

            <tr>

                <th>Country</th>

                <td style="text-align: left;">{{ $client->country ?? '-' }}</td>

            </tr>

            <tr>

                <th>Pincode</th>

                <td style="text-align: left;">{{ $client->pincode ?? '-' }}</td>

            </tr>

            <tr>

                <th>Billing SPOC Name</th>

                <td style="text-align: left;">{{ $client->billing_spoc_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Contact Number</th>

                <td style="text-align: left;">{{ $client->billing_spoc_contact ?? '-' }}</td>

            </tr>

            <tr>

                <th>Email</th>

                <td style="text-align: left;">{{ $client->billing_spoc_email ?? '-' }}</td>

            </tr>

            <tr>

                <th>GSTIN</th>

                <td style="text-align: left;">{{ $client->gstin ?? '-' }}</td>

            </tr>

            <tr>

                <th>Invoice Email</th>

                <td style="text-align: left;">{{ $client->invoice_email ?? '-' }}</td>

            </tr>

            <tr>

                <th>Invoice CC</th>

                <td style="text-align: left;">{{ $client->invoice_cc ?? '-' }}</td>

            </tr>

            <tr>

                <th>SPOC Name</th>

                <td style="text-align: left;">{{ $client->support_spoc_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Mobile</th>

                <td style="text-align: left;">{{ $client->support_spoc_mobile ?? '-' }}</td>

            </tr>

            <tr>

                <th>Email</th>

                <td style="text-align: left;">{{ $client->support_spoc_email ?? '-' }}</td>

            </tr>



            <tr>

                <th>Status</th>

                <td style="text-align: left;">

                    <span class="badge {{ $client->status === 'Active' ? 'bg-success' : 'bg-danger' }}">

                        {{ $client->status }}

                    </span>

                </td>

            </tr>

        </table>



        <div class="text-end">

            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

<style>
    td{
        text-align: left;
    
    }
</style>

@endsection