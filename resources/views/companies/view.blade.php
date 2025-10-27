@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-primary">View Company</h3>

    <div class="card shadow border-0 p-4">
        <table class="table table-bordered">
            <tr>
                <th>Trade / Brand Name</th>
                <td>{{ $company->brand_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Company Name</th>
                <td>{{ $company->company_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Business Number (CIN / LLPIN)</th>
                <td>{{ $company->cin_llpin ?? '-' }}</td>
            </tr>
            <tr>
                <th>Company Phone</th>
                <td>{{ $company->company_phone ?? '-' }}</td>
            </tr>
            <tr>
                <th>Company Email</th>
                <td>{{ $company->company_email ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alternative Contact Number</th>
                <td>{{ $company->alt_contact ?? '-' }}</td>
            </tr>
            <tr>
                <th>GST Number</th>
                <td>{{ $company->gst_no ?? '-' }}</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>{{ $company->address ?? '-' }}</td>
            </tr>
            <tr>
                <th>Pincode</th>
                <td>{{ $company->pincode ?? '-' }}</td>
            </tr>
            <tr>
                <th>Area</th>
                <td>{{ $company->area ?? '-' }}</td>
            </tr>
            <tr>
                <th>District</th>
                <td>{{ $company->district ?? '-' }}</td>
            </tr>
            <tr>
                <th>State</th>
                <td>{{ $company->state ?? '-' }}</td>
            </tr>
            <tr>
                <th>Website</th>
                <td>{{ $company->website ?? '-' }}</td>
            </tr>
            <tr>
                <th>Branch Location</th>
                <td>{{ $company->branch_location ?? '-' }}</td>
            </tr>
            <tr>
                <th>Instagram</th>
                <td>{{ $company->instagram ?? '-' }}</td>
            </tr>
            <tr>
                <th>Youtube</th>
                <td>{{ $company->youtube ?? '-' }}</td>
            </tr>
            <tr>
                <th>Facebook</th>
                <td>{{ $company->facebook ?? '-' }}</td>
            </tr>
            <tr>
                <th>LinkedIn</th>
                <td>{{ $company->linkedin ?? '-' }}</td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td>{{ $company->pan_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>TAN Number</th>
                <td>{{ $company->tan_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Bank Name</th>
                <td>{{ $company->bank_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Branch Name</th>
                <td>{{ $company->branch_name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Account Number</th>
                <td>{{ $company->account_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>IFSC Code</th>
                <td>{{ $company->ifsc_code ?? '-' }}</td>
            </tr>
            <tr>
                <th>UPI ID</th>
                <td>{{ $company->upi_id ?? '-' }}</td>
            </tr>
            <tr>
                <th>UPI Number</th>
                <td>{{ $company->upi_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Opening Balance</th>
                <td>{{ $company->opening_balance ?? '-' }}</td>
            </tr>

            {{-- Billing Logo --}}
            <tr>
                <th>Billing Logo</th>
                <td>
                    @if(!empty($company->billing_logo))
                        <img src="{{ asset('images/logos/'.$company->billing_logo) }}" width="100" class="border rounded">
                    @else
                        -
                    @endif
                </td>
            </tr>

            {{-- Normal Sign --}}
            <tr>
                <th>Normal Sign</th>
                <td>
                    @if(!empty($company->billing_sign_normal))
                        <img src="{{ asset('images/n_signs/'.$company->billing_sign_normal) }}" width="100" class="border rounded">
                    @else
                        -
                    @endif
                </td>
            </tr>

            {{-- Digital Sign --}}
            <tr>
                <th>Digital Sign</th>
                <td>
                    @if(!empty($company->billing_sign_digital))
                        <img src="{{ asset('images/d_signs/'.$company->billing_sign_digital) }}" width="100" class="border rounded">
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge {{ $company->status === 'Active' ? 'bg-success' : 'bg-danger' }}">
                        {{ $company->status }}
                    </span>
                </td>
            </tr>
        </table>

        <div class="text-end">
            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
