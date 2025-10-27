<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Feasibility Updated</title>
</head>
<body>
    <h2>Feasibility Updated</h2>

    <p><strong>Service Type:</strong> {{ $feasibility->type_of_service }}</p>
    <p><strong>Client:</strong> {{ $feasibility->client->client_name ?? 'N/A' }}</p>
    <p><strong>Pincode:</strong> {{ $feasibility->pincode }}</p>
    <p><strong>Status:</strong> {{ $feasibility->status }}</p>

    <p>The feasibility record has been updated successfully.</p>

    <p>Thank you,<br>
    <strong>Network Team</strong></p>
</body>
</html>
