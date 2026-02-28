@extends('layouts.app')

@section('content')

<!DOCTYPE html>
<html>
<head>
    <title>State-wise Invoice Report</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>State-wise Invoice Report (Monthly)</h2>
    <table>
        <thead>
            <tr>
                <th>State</th>
                <th>April</th>
                <th>May</th>
                <th>June</th>
                <th>July</th>
                <th>August</th>
                <th>September</th>
                <th>October</th>
                <th>November</th>
                <th>December</th>
                <th>January</th>
                <th>February</th>
                <th>March</th>
            </tr>
        </thead>
        <tbody>
            @foreach($states as $state)
            <tr>
                <td>{{ $state->name }}</td>
                @foreach($months as $month)
                    <td>{{ $state->monthly_invoices[$month] ?? 0 }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>State-wise Invoice Report (Quarterly)</h2>
    <table>
        <thead>
            <tr>
                <th>State</th>
                <th>Q1 (Apr-Jun)</th>
                <th>Q2 (Jul-Sep)</th>
                <th>Q3 (Oct-Dec)</th>
                <th>Q4 (Jan-Mar)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($states as $state)
            <tr>
                <td>{{ $state->name }}</td>
                <td>{{ $state->quarterly_invoices[1] ?? 0 }}</td>
                <td>{{ $state->quarterly_invoices[2] ?? 0 }}</td>
                <td>{{ $state->quarterly_invoices[3] ?? 0 }}</td>
                <td>{{ $state->quarterly_invoices[4] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
@endsection
