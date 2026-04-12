<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recurring Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #111; margin: 0; padding: 20px; }
        .wrap { max-width: 900px; margin: 0 auto; border: 1px solid #ddd; }
        .head { padding: 16px; border-bottom: 1px solid #ddd; }
        .title { float: right; font-size: 20px; font-weight: 700; }
        .company { font-size: 14px; font-weight: 700; }
        .muted { color: #555; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background: #f6f6f6; text-align: left; }
        .section { padding: 16px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="head">
            <div class="title">RECURRING INVOICE</div>
            <div class="company">{{ $company->company_name ?? 'Unborn Group' }}</div>
            <div class="muted">{{ $company->address ?? '' }}</div>
            <div class="muted">GSTIN: {{ $company->gstin ?? '' }}</div>
            <div class="muted">Email: {{ $company->company_email ?? '' }}</div>
            <div class="clear"></div>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th style="width:50%">Client Details</th>
                    <th style="width:50%">Service Details</th>
                </tr>
                <tr>
                    <td>
                        <strong>{{ $client->client_name ?? '-' }}</strong><br>
                        {{ $client->address1 ?? '' }}<br>
                        {{ $client->city ?? '' }} {{ $client->state ?? '' }} {{ $client->pincode ?? '' }}<br>
                        GSTIN: {{ $client->gstin ?? '-' }}
                    </td>
                    <td>
                        <strong>Circuit ID:</strong> {{ $formula['circuit_id'] ?? ($renewal->circuit_id ?? '-') }}<br>
                        <strong>Renewal Date:</strong> {{ $renewal->date_of_renewal ?? '-' }}<br>
                        <strong>New Expiry Date:</strong> {{ $renewal->new_expiry_date ?? '-' }}<br>
                        <strong>Renewal Months:</strong> {{ $renewal->renewal_months ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table>
                <tr>
                    <th>Particular</th>
                    <th class="right">Value</th>
                </tr>
                <tr>
                    <td>ARC (Annual)</td>
                    <td class="right">{{ number_format((float) ($formula['annual_arc'] ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Static (Annual)</td>
                    <td class="right">{{ number_format((float) ($formula['annual_static'] ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Billable Days</td>
                    <td class="right">{{ $formula['billable_days'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>Day Rate</td>
                    <td class="right">{{ number_format((float) ($formula['day_rate'] ?? 0), 6) }}</td>
                </tr>
                <tr>
                    <th>Formula Amount</th>
                    <th class="right">{{ number_format((float) ($formula['formula_amount'] ?? 0), 2) }}</th>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
