@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Profit & Loss Report</h4>

    <table class="table table-bordered">
        <tr>
            <th>Total Income</th>
            <td>₹ {{ number_format($totalIncome,2) }}</td>
        </tr>
        <tr>
            <th>Total Expenses</th>
            <td>₹ {{ number_format($totalExpense,2) }}</td>
        </tr>
        <tr class="table-success">
            <th>Net Profit / Loss</th>
            <td>₹ {{ number_format($profit,2) }}</td>
        </tr>
    </table>
</div>
@endsection
