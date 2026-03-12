@extends('layouts.app')

@section('content')

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Purchase Invoice Details</h4>

        <div>
            <a href="{{ route('finance.purchases.print', $purchase->id) }}"
               class="btn btn-secondary">
                <i class="bi bi-printer me-1"></i> Print
            </a>

            <a href="{{ route('finance.purchases.edit', $purchase->id) }}"
               class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>

            <a href="{{ route('finance.purchases.index') }}"
               class="btn btn-dark">
                Back
            </a>
        </div>
    </div>

    <div class="card shadow p-1">
        <div class="card-body">

            {{-- Header --}}
            <div class="row">
                <div class="col-md-2">
                    @php
                        $logo = $feasibility->company->company_logo ?? $company->company_logo ?? null;
                    @endphp
                    @if($logo && file_exists(public_path('images/companylogos/' . $logo)))
                        <img src="{{ asset('images/companylogos/' . $logo) }}" alt="Company Logo" style="max-width: 100px; max-height: 100px;">
                    @else
                        <div style="width:100px; height:100px; background:#ccc; display:flex; align-items:center; justify-content:center;">No Logo</div>
                    @endif
                </div>

                <div class="col-md-7 text-center" style="width: 350px;">
                    <h5 class="mb-1"><strong>{{ $feasibility->company->company_name ?? '' }}</strong></h5>
                    Company ID: {{ $feasibility->company->company_id ?? '' }} <br>
                    {{ $feasibility->company->address ?? '' }} <br>
                    GSTIN: {{ $feasibility->company->gstin ?? '' }} <br>
                    Phone: 04341222226 / 9688862676 <br>
                    Email: {{ $feasibility->company->company_email ?? '' }}
                </div>

                <div class="col-md-3 text-end">
                    <h3 class="fw-bold">TAX INVOICE</h3>
                </div>
            </div>

            <hr>

            {{-- Invoice Details --}}
            <table class="table table-bordered ">
    <tr>
        
        <!-- LEFT SIDE -->
        <td style="width:50%; text-align: left;" >
            
            <strong>Invoice #</strong> : <span>{{ $purchase->invoice_no }}</span> <br>
            <strong>Invoice Date</strong> : <span>{{ $purchase->invoice_date }} </span> <br>
            <strong>Terms</strong> : <span>Net 30</span><br>
            <strong>Due Date</strong> : <span>{{ $purchase->due_date }}</span> <br>
            <strong>P.O #</strong> : <span>{{ $deliverables->purchaseOrder->po_number ?? '' }}</span>
        </td>

        <!-- RIGHT SIDE -->
        <td style="width:50%; text-align: left;">
            <strong>Place Of Supply</strong> : {{ $feasibility->vandor->state ?? '' }} <br>
            <strong>Service ID</strong> : {{ $purchase->service_id ?? '' }} <br>
            <strong>UNBORN Service ID/
                Circuit_id</strong> : {{ $purchase->service_id ?? '' }} | {{ $deliverablePlan->circuit_id ?? '' }} <br>
            <strong>Feasibility ID</strong> : {{ $deliverables->feasibility->feasibility_request_id ?? '' }} <br>
            <strong>Vendor ID</strong> : {{ $deliverablePlan->vendor_code ?? '' }}
        </td>
    </tr>

    <!-- BILL TO / SHIP TO HEADER -->
    <tr class="table-secondary">
        <td><strong>Bill To</strong></td>
        <td><strong>Ship To</strong></td>
    </tr>

    <!-- BILL TO / SHIP TO DATA -->
    <tr>
        <td class="Bill">
            <strong>{{ $feasibility->vandor->vandor_name ?? '' }}</strong><br>
            {{ $feasibility->vandor->vandor_name ?? '' }}<br>
            {{ $feasibility->vandor->address1 ?? '' }}<br>
            {{ $feasibility->vandor->city ?? '' }},
            {{ $feasibility->vandor->state ?? '' }} -
            {{ $feasibility->vandor->pincode ?? '' }}<br>
            GSTIN: {{ $feasibility->vandor->gstin ?? '' }}
        </td>

        <td class="Ship">
            {{ $feasibility->vandor->vandor_name ?? '' }}<br>
            {{ $feasibility->address ?? '' }}<br>
            {{ $feasibility->city ?? '' }},
            {{ $feasibility->district ?? '' }},
            {{ $feasibility->state ?? '' }} -
            {{ $feasibility->pincode ?? '' }}<br>
            GSTIN: {{ $feasibility->vandor->gstin ?? '' }}
        </td>
    </tr>
</table>
            <br>

            {{-- Subject --}}
            <p>
                <strong>Subject:</strong>
                Invoice for Order ID: {{ $purchase->order_id ?? '' }}
                | Vendor Code: {{ $deliverables->deliverablePlan->vendor_code ?? '' }}
            </p>

            {{-- Items Table --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Item & Description</th>
                            <th rowspan="2">HSN/SAC</th>
                            <th rowspan="2">Qty</th>
                            <th rowspan="2">Rate</th>
                            <th rowspan="2">Amount</th>
                            <th rowspan="2">Taxable Amount</th>
                            <th colspan="2">CGST</th>
                            <th colspan="2">SGST</th>
                        </tr>
                        <tr>
                            <th>%</th>
                            <th>Amt</th>
                            <th>%</th>
                            <th>Amt</th>
                        </tr>
                    </thead>

                    @php
    $taxable = $deliverables->purchaseOrder->arc_per_link ?? 0;
    $cgstPercent = 9;
    $sgstPercent = 9;

    $cgstAmount = ($taxable * $cgstPercent) / 100;
    $sgstAmount = ($taxable * $sgstPercent) / 100;
@endphp
<!-- 995423 -->
                    <tbody>
                      @foreach(($purchase->items ?? []) as $key => $item)
                        <tr>
                            <td>1</td>
                            <td>{{ $item ? $item->item_name : 'N/A' }}</td>
                            <td>{{ $item ? $item->hsn_sac_code : 'N/A' }}</td>
                            <td></td>
                            <td>{{ $deliverables?->purchaseOrder?->arc_per_link ?? '0' }}</td>
<td>{{ $deliverables?->purchaseOrder?->arc_per_link ?? '0' }}</td>
<td>{{ $deliverables?->purchaseOrder?->arc_per_link ?? '0' }}</td>
                            <td>{{ $cgstPercent }}%</td>
                            <td>{{ number_format($cgstAmount,2) }}</td>
                            <td>{{ $sgstPercent }}%</td>
                            <td>{{ number_format($sgstAmount,2) }}</td>
                        </tr>
                      @endforeach 
                    </tbody>
                </table>
            </div>


            {{-- Bottom Section --}}
            <div class="invoice-bottom">

                <!-- LEFT -->
                <div class="left-box">
                    <p><strong>Total in Words</strong><br>
                    {{ $purchase->total_in_words ?? '---' }}</p>

                    <p><strong>Notes</strong><br>
                    Thanks for your business.</p>

                    <p><strong>Terms & Conditions</strong><br>
                        Payment Details: {{ $feasibility->company->company_name ?? ''}}<br>
                        Account Number : {{ $feasibility->company->account_number ?? '' }}<br>
                        IFSC Code : {{ $feasibility->company->ifsc_code ?? '' }}<br>
                        Branch & Bank : {{ $feasibility->company->branch_name ?? '' }},
                        {{ $feasibility->company->bank_name ?? '' }}
                    </p>
                </div>

                <!-- RIGHT -->
                <!-- RIGHT SIDE -->
       <!-- RIGHT SIDE -->
        <div style="width:35%; padding:0;">
            <table style="width:100%;">
                <tr>
                    <td>Total</td>
                    <td class="text-end"><strong>{{ number_format($purchase->total_amount,2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Balance Due</strong></td>
                    <td class="text-end"><strong>{{ number_format($purchase->total_amount,2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="2" style="height:80px; text-align:center; vertical-align:bottom;">
                        Authorized Signature
                    </td>
                </tr>
            </table>
        </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="{{ route('finance.purchases.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    
</div>

<style>
.card {
    font-size: 14px;
}
.table th, .table td {
    vertical-align: middle;
    font-size: 13px;
}

span{
    text-align: right;
}
.invoice-bottom {
    display: flex;
    margin-top: 20px;
}
.left-box {
    width: 60%;
}
.right-box {
    width: 40%;
    border: 1px solid #000;
    padding: 10px;
}
.right-box table td {
    border: none;
    padding: 5px;
}
.signature {
    margin-top: 40px;
    text-align: center;
}
.card{
    border-color: #000;
    border-radius: 1px;
    border-style: solid;
    width: 70%;
    margin-left: 15%;
}

</style>

@endsection