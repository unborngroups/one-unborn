@extends('layouts.app')



@section('content')

<div class="container py-4">

    <h3 class="mb-3 text-primary">View feasibility</h3>



    <div class="card shadow border-0 p-4">

        <table class="table table-bordered">

            <tr>

                <th>Feasibility Request ID</th>

                <td>{{ $feasibility->feasibility_request_id ?? '-' }}</td>

            </tr>

            <tr>

                <th>Type of Service </th>

                <td>{{ $feasibility->type_of_service ?? '-' }}</td>

            </tr>

            <tr>

                <th>Company Name</th>

                <td>{{ $feasibility->company->company_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Client Name </th>

                <td>{{ $feasibility->client->client_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Pincode</th>

                <td>{{ $feasibility->pincode ?? '-' }}</td>

            </tr>

            <tr>

                <th>State</th>

                <td>{{ $feasibility->state ?? '-' }}</td>

            </tr>

            <tr>

                <th>District</th>

                <td>{{ $feasibility->district ?? '-' }}</td>

            </tr>

            <tr>

                <th>Area</th>

                <td>{{ $feasibility->area ?? '-' }}</td>

            </tr>

            <tr>

                <th>Address</th>

                <td>{{ $feasibility->address ?? '-' }}</td>

            </tr>

            <tr>

                <th>SPOC Name </th>

                <td>{{ $feasibility->spoc_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>SPOC Contact 1 </th>

                <td>{{ $feasibility->spoc_contact1 ?? '-' }}</td>
            </tr>

            <tr>

                <th>SPOC Contact 2 </th>

                <td>{{ $feasibility->spoc_contact2 ?? '-' }}</td>
            </tr>

            <tr>

                <th>SPOC Email </th>

                <td>{{ $feasibility->spoc_email ?? '-' }}</td>
            </tr>

              <tr>

                <th>No. Of Links </th>

                <td>{{ $feasibility->no_of_links ?? '-' }}</td>
            </tr>

            <tr> 

                <th>Vendor Type</th>

                <td>{{ $feasibility->vendor_type ?? '-' }}</td>

            </tr>

            <tr>

                <th>Speed</th>

                <td>{{ $feasibility->speed ?? '-' }}</td>

            </tr>

            <tr>

                <th>Static IP</th>

                <td>{{ $feasibility->static_ip ?? '-' }}</td>

            </tr>

            <tr>

                <th>Static IP Subnet</th>

                <td>{{ $feasibility->static_ip_subnet ?? '-' }}</td>

            </tr>

            <tr>

                <th>Expected Delivery</th>

                <td>{{ $feasibility->expected_delivery ?? '-' }}</td>

            </tr>

            <tr>

                <th>Expected Activation</th>

                <td>{{ $feasibility->expected_activation ?? '-' }}</td>

            </tr>

            <tr>

                <th>Hardware Required</th>

                <td>{{ $feasibility->hardware_required ?? '-' }}</td>

            </tr>

            <tr>

                <th>Hardware Model Name</th>

                <td>{{ $feasibility->hardware_model_name ?? '-' }}</td>

            </tr>

            <tr>

                <th>Status</th>

                <td>

                    <span class="badge {{ $feasibility->status === 'Active' ? 'bg-success' : 'bg-danger' }}">

                        {{ $feasibility->status }}

                    </span>

                </td>

            </tr>

        </table>

        <div class="text-end">

            <a href="{{ route('feasibility.index') }}" class="btn btn-secondary">Back</a>

        </div>

    </div>

</div>

@endsection

