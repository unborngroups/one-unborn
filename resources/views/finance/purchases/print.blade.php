<!DOCTYPE html>
<html>
<head>
    <title>Purchase Invoice - {{ $purchase->invoice_number }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .invoice-box {
            max-width: 900px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f5f5f5;
        }

        .text-end {
            text-align: right;
        }

        .total-section {
            margin-top: 20px;
        }

        .no-print {
            text-align: right;
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="invoice-box">

    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <div>
            <h2>Your Company Name</h2>
            <p>
                Company Address Line 1 <br>
                GST: XXXXXXXX <br>
                Phone: 9999999999
            </p>
        </div>

        <div class="text-end">
            <h2>Purchase Invoice</h2>
            <p>
                <strong>Invoice No:</strong> {{ $purchase->invoice_number }} <br>
                <strong>Date:</strong>
                {{ \Carbon\Carbon::parse($purchase->invoice_date)->format('d-m-Y') }}
            </p>
        </div>
    </div>

    <hr>

    <h4>Vendor Details</h4>
    <p>
        <strong>Name:</strong> {{ $purchase->vendor->name ?? '-' }} <br>
        <strong>Email:</strong> {{ $purchase->vendor->email ?? '-' }} <br>
        <strong>Phone:</strong> {{ $purchase->vendor->phone ?? '-' }} <br>
        <strong>GST:</strong> {{ $purchase->vendor->gst_number ?? '-' }}
    </p>

    <br>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchase->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₹ {{ number_format($item->price, 2) }}</td>
                    <td>₹ {{ number_format($item->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No Items Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-section text-end">
        <h3>Grand Total: ₹ {{ number_format($purchase->total_amount, 2) }}</h3>
    </div>

    <br><br>

    <div style="margin-top:60px;">
        <div style="float:left;">
            __________________________ <br>
            Authorized Signature
        </div>

        <div style="float:right;">
            __________________________ <br>
            Received By
        </div>
    </div>

</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>