@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }
        .section {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th {
            background-color: #f2f2f2;
        }
        th, td {
            padding: 6px;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .no-border {
            border: none !important;
        }
        img {
            max-width: 250px;
        }
        .card{
            width: 70%;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #000;
        }
    </style>
</head>

<body>
<div class="card">
<table width="100%" cellpadding="5">
    <tr>
        <td width="15%" style="border:none;">
                            <img src="{{ asset('images/logo1.png') }}" alt="Logo" class="h-16">

        </td>

        <td width="55%" style="border:none;">
            <strong>{{ $feasibility->company->company_name ?? '' }}</strong><br>
            Company ID: {{ $feasibility->company->company_id ?? '' }}<br>
            {{ $feasibility->company->address ?? '' }}<br>
            GSTIN: {{ $feasibility->company->gstin ?? '' }}<br>
            Phone: {{ $feasibility->company->alternative_contact_number ?? '' }}<br>
            Email: {{ $feasibility->company->company_email ?? '' }}
        </td>

        <td width="30%" align="right" style="border:none;">
            <h2>TAX INVOICE</h2>
        </td>
    </tr>
</table>

<hr>

{{-- Invoice Details --}}
<table width="100%" cellpadding="5">
    <tr>
        <td width="50%">
            <strong>Invoice #:</strong> {{ $invoice->invoice_no }}<br>
            <strong>Invoice Date:</strong> {{ $invoice->invoice_date }}<br>
            <strong>Terms:</strong> Net 30<br>
            <strong>Due Date:</strong> {{ $invoice->due_date }}<br>
            <strong>PO #:</strong> {{ $deliverables->purchaseOrder->po_number ?? '' }}
        </td>

        <td width="50%">
            <strong>Place Of Supply:</strong> {{ $invoice->place_of_supply ?? '' }}<br>
            <strong>Service ID:</strong> {{ $invoice->service_id ?? '' }}<br>
            <strong>UNBORN Service ID:</strong> {{ $invoice->unborn_service_id ?? '' }}<br>
            <strong>Feasibility ID:</strong> {{ $deliverables->feasibility->feasibility_request_id ?? '' }}<br>
            <strong>Vendor ID:</strong> {{ $deliverablePlan->vendor_code ?? '' }}
        </td>
    </tr>
</table>

<br>

{{-- Bill To / Ship To --}}
<table width="100%" cellpadding="5">
    <tr>
        <th width="50%">Bill To</th>
        <th width="50%">Ship To</th>
    </tr>
    <tr>
        <td>
            <strong>{{ $feasibility->client->client_name ?? '' }}</strong><br>
            {{ $feasibility->client->address1 ?? '' }}<br>
            {{ $feasibility->client->city ?? '' }},
            {{ $feasibility->client->state ?? '' }}
            - {{ $feasibility->client->pincode ?? '' }}<br>
            GSTIN: {{ $feasibility->client->gstin ?? '' }}
        </td>

        <td>
            <strong>{{ $client->ship_to_name ?? $client->client_name ?? '' }}</strong><br>
            {{ $client->ship_to_address ?? $client->address ?? '' }}<br>
            {{ $client->ship_to_city ?? $client->city ?? '' }},
            {{ $client->ship_to_state ?? $client->state ?? '' }}
            - {{ $client->ship_to_pincode ?? $client->pincode ?? '' }}<br>
            GSTIN: {{ $client->ship_to_gst_number ?? $client->gst_number ?? '' }}
        </td>
    </tr>
</table>

<br>
<strong>Subject:</strong>
            Invoice for Order ID: {{ $invoice->order_id ?? '' }}
            | Vendor Code: {{ $deliverablePlan->vendor_code ?? '' }}


<br>

{{-- Items Table --}}
<table width="100%" cellpadding="5">
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>HSN/SAC</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Taxable</th>
            <th>CGST</th>
            <th>SGST</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $key => $item)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->hsn_sac ?? '' }}</td>
            <td>{{ $item->quantity }}</td>
            <td align="right">{{ number_format($item->rate,2) }}</td>
            <td align="right">{{ number_format($item->amount,2) }}</td>
            <td align="right">{{ number_format($item->taxable_amount ?? 0,2) }}</td>
            <td align="right">{{ number_format($item->cgst_amount ?? 0,2) }}</td>
            <td align="right">{{ number_format($item->sgst_amount ?? 0,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

{{-- Totals Right Box --}}
<table width="100%">
    <tr>
        <td width="60%" style="border:none;"></td>

        <td width="40%">
            <table width="100%" cellpadding="5">
                <tr>
                    <td>Sub Total</td>
                    <td align="right">{{ number_format($invoice->subtotal,2) }}</td>
                </tr>
                <tr>
                    <td>CGST</td>
                    <td align="right">{{ number_format($invoice->cgst_total ?? 0,2) }}</td>
                </tr>
                <tr>
                    <td>SGST</td>
                    <td align="right">{{ number_format($invoice->sgst_total ?? 0,2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td align="right"><strong>{{ number_format($invoice->total_amount,2) }}</strong></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<br><br>

<div style="text-align:right;">
    Authorized Signature
</div>


</div>
{{-- Buttons --}}
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('finance.invoices.index') }}" class="btn btn-secondary"><--Back</a>
            </div>
</body> 
</html>


@endsection
