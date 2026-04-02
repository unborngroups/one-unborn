<table>
    <thead>
        <tr>
            <th>Vendor Name</th>
            <th>Vendor Bank Account</th>
            <th>IFSC</th>
            <th>Amount</th>
            <th>Invoice No</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $row)
            <tr>
                <td>{{ $row->vendor_name }}</td>
                <td>{{ $row->vendor_bank_account }}</td>
                <td>{{ $row->vendor_ifsc }}</td>
                <td>{{ $row->amount }}</td>
                <td>{{ $row->invoice_no }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
