@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Debit Notes</h4>
        <a href="{{ route('finance.debit-notes.create') }}" class="btn btn-primary">
            + Add Debit Note
        </a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark-primary">
            <tr>
                <th>S.No</th>
                <th>Debit Note No</th>
                <th>Vendor</th>
                <th>Invoice</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debitNotes as $note)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $note->debit_note_no }}</td>
                <td>{{ optional($note->vendorInvoice->vendor)->vendor_name ?? '-' }}</td>
                <td>{{ optional($note->vendorInvoice)->invoice_no ?? '-' }}</td>
                <td>{{ $note->date ? \Carbon\Carbon::parse($note->date)->format('d-m-Y') : '-' }}</td>
                <td>₹ {{ number_format($note->amount, 2) }}</td>
                <td>{{ $note->reason ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
