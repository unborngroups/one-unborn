<table>
    <thead>
        <tr>
            @foreach($columns as $key => $label)
                <th>{{ $label }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($records as $record)
            <tr>
                <td>{{ $record->feasibility->client->client_name ?? 'N/A' }}</td>
                <td>{{ $record->location_id ?? 'N/A' }}</td>
                <td>{{ $record->feasibility->address ?? 'N/A' }}</td>
                <td>{{ $record->circuit_id ?? 'N/A' }}</td>
                <td>{{ $record->date_of_activation ? \Carbon\Carbon::parse($record->date_of_activation)->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $record->mode_of_delivery ?? 'N/A' }}</td>
                <td>{{ $record->static_ip ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
