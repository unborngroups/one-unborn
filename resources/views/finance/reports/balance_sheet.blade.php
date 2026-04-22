@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Balance Sheet</h4>

    <table class="table table-bordered">
        <tr class="table-primary">
            <th colspan="2">Assets</th>
        </tr>
        <tr>
            <td>Total Assets</td>
            <td>₹ {{ number_format($assets,2) }}</td>
        </tr>

        <tr class="table-warning">
            <th colspan="2">Liabilities</th>
        </tr>
        <tr>
            <td>Total Liabilities</td>
            <td>₹ {{ number_format($liabilities,2) }}</td>
        </tr>

        <tr class="table-success">
            <th>Equity</th>
            <td>₹ {{ number_format($equity,2) }}</td>
        </tr>
    </table>
</div>
@endsection
