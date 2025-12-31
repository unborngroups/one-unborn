<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feasibility Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .content { background-color: #f8f9fa; padding: 20px; border-radius: 5px; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .info-table td { padding: 8px; border-bottom: 1px solid #ddd; }
        .info-table .label { font-weight: bold; width: 40%; }
    </style>
</head>
<body>

<div class="container">
    <div class="content">

        {{-- Sales Created → Email to Team --}}
        @if($emailType == 'created')

            <h2 style="color:#007bff;">New Feasibility Created</h2>

            <p>
                Hello Operations Team,<br><br>
                The Sales & Marketing team has created a new feasibility request.<br>
                Please log in and update the feasibility.
            </p>
            <p>

           <a href="{{ url('/login') }}" target="_blank"
   style="display:inline-block; background-color:#6a1b9a; color:#fff;
          padding:12px 25px; text-decoration:none; border-radius:6px;
          font-weight:bold;">
    Login Now
</a>

        </p>

        {{-- Operations Closed → Email to Creator --}}
        @elseif($status == 'Closed')

            <h2 style="color:#28a745;">Feasibility Completed</h2>

            <p>
                Hello {{ $feasibility->createdBy->name ?? 'User' }},<br><br>
                Your feasibility request has been <strong>successfully closed by the Operations Team.</strong>
            </p>

            <table class="info-table">
                <tr>
                    <td class="label">Status</td>
                    <td>Completed</td>
                </tr>
                <tr>
                    <td class="label">Closed By</td>
                    <td>{{ $actionBy->name ?? 'System' }}</td>
                </tr>
                <tr>
                    <td class="label">Closed On</td>
                    <td>{{ now()->format('Y-m-d H:i A') }}</td>
                </tr>
            </table>

        {{-- Operations Updated → Email to Creator --}}
        @else

            <h2 style="color:#007bff;">Feasibility Status Updated</h2>

            <p>
                Hello {{ $feasibility->createdBy->name ?? 'User' }},<br><br>
                Your feasibility request status has been updated.
            </p>

             


            <table class="info-table">
                <tr>
                    <td class="label">Previous Status</td>
                    <td>{{ $previousStatus ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">New Status</td>
                    <td>{{ $status }}</td>
                </tr>
                <tr>
                    <td class="label">Updated By</td>
                    <td>{{ $actionBy->name ?? 'System' }}</td>
                </tr>
            </table>

        @endif

    </div>
</div>

</body>
</html>
