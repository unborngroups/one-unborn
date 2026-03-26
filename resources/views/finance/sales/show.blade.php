@extends('layouts.app')

@section('content')

<div class="container mt-4">
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
            
            <strong>Invoice #</strong> : <span>{{ $sales->invoice_no }}</span> <br>
            <strong>Invoice Date</strong> : <span>{{ $sales->invoice_date }} </span> <br>
            <strong>Terms</strong> : <span>Net 30</span><br>
            <strong>Due Date</strong> : <span>{{ $sales->due_date }}</span> <br>
            <strong>P.O #</strong> : <span>{{ $deliverables->purchaseOrder->po_number ?? '' }}</span>
        </td>

        <!-- RIGHT SIDE -->
        <td style="width:50%; text-align: left;">
            <strong>Place Of Supply</strong> : {{ $feasibility->client->state ?? '' }} <br>
            <strong>Service ID</strong> : {{ $sales->service_id ?? '' }} <br>
            <strong>UNBORN Service ID/Circuit_id</strong> : {{ $sales->service_id ?? '' }} | {{ $deliverablePlan->circuit_id ?? '' }} <br>
            <strong>Feasibility ID</strong> : {{ $deliverables->feasibility->feasibility_request_id ?? '' }} <br>
            <strong>client ID</strong> : {{ $deliverablePlan->client_code ?? '' }}
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
        
        <!-- <h4> <strong>{{ $feasibility->client->client_name ?? '' }}</strong></h4>
        <h4>{{ $feasibility->client->client_name ?? '' }}</h4>
        <h4>{{ $feasibility->client->address1 ?? '' }}</h4>
        <h4>{{ $feasibility->client->city ?? '' }},
            {{ $feasibility->client->state ?? '' }} -
            {{ $feasibility->client->pincode ?? '' }}</h4>
        <h4>GSTIN: {{ $feasibility->client->gstin ?? '' }}</h4> -->
            <strong>{{ $feasibility->client->client_name ?? '' }}</strong><br>
            {{ $feasibility->client->client_name ?? '' }}<br>
            {{ $feasibility->client->address1 ?? '' }}<br>
            {{ $feasibility->client->city ?? '' }},
            {{ $feasibility->client->state ?? '' }} -
            {{ $feasibility->client->pincode ?? '' }}<br>
            GSTIN: {{ $feasibility->client->gstin ?? '' }}
        </td>

        <td class="Ship">
            {{ $feasibility->client->client_name ?? '' }}<br>
            {{ $feasibility->address ?? '' }}<br>
            {{ $feasibility->city ?? '' }},
            {{ $feasibility->district ?? '' }},
            {{ $feasibility->state ?? '' }} -
            {{ $feasibility->pincode ?? '' }}<br>
            GSTIN: {{ $feasibility->client->gstin ?? '' }}
        </td>
    </tr>
</table>
            <br>

            {{-- Subject --}}
            <p>
                <strong>Subject:</strong>
                Invoice for Order ID: {{ $sales->order_id ?? '' }}
                | client Code: {{ $deliverables->deliverablePlan->client_code ?? '' }}
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
                                            @foreach(($sales->items ?? []) as $key => $item)
                                                <tr>
                                                        <td>{{ $key + 1 }}</td>
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
                    {{ $sales->total_in_words ?? '---' }}</p>

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
                    <td class="text-end"><strong>{{ number_format($sales->total_amount,2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Balance Due</strong></td>
                    <td class="text-end"><strong>{{ number_format($sales->total_amount,2) }}</strong></td>
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
/* .left-box {
    width: 60%;
}
.right-box {
    width: 40%;
    border: 1px solid #000;
    padding: 10px;
} */
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
    width: 100%;
    margin: 0 auto;
}

.container.mt-4 {
    padding-left: 16px;
    padding-right: 16px;
    max-width: 1000px;
    margin: 0 auto;
}

</style>

@endsection