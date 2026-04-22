@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Cash Flow Statement</h4>

    <table class="table table-bordered">
        <tr>
            <th>Cash Inflow</th>
            <td>₹ {{ number_format($cashIn,2) }}</td>
        </tr>
        <tr>
            <th>Cash Outflow</th>
            <td>₹ {{ number_format($cashOut,2) }}</td>
        </tr>
        <tr class="table-success">
            <th>Net Cash Flow</th>
            <td>₹ {{ number_format($netCash,2) }}</td>
        </tr>
    </table>
</div>
@endsection
