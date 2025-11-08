<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to {{ $company ?? config('app.name') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 30px;
        }
        .email-container {
            background: #fff;
            max-width: 600px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #6a1b9a;
            color: #fff;
            text-align: center;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
        }
        .content {
            padding: 30px;
            line-height: 1.6;
            color: #333;
        }
        .footer {
            background-color: #eee;
            text-align: center;
            padding: 12px;
            font-size: 13px;
            color: #555;
        }
    </style>
</head>
<body>

@php
    // ✅ Normalize variables
    $company     = $company ?? ($emailData['company'] ?? config('app.name'));
    $name        = $name ?? ($emailData['name'] ?? ($emailData['user']['name'] ?? 'User'));
    $email       = $email ?? ($emailData['email'] ?? ($emailData['user']['email'] ?? ''));
    $password    = $password ?? ($emailData['password'] ?? '');
    $templateBody = $emailData['template_body'] ?? '';
    
    // ✅ Footer & signature from company settings
    $footerText     = $emailData['mail_footer'] ?? '';
    $signatureText  = $emailData['mail_signature'] ?? 'HR Team';

    // ✅ Whether template exists
    $templateExists = !empty($templateBody);
@endphp

<div class="email-container">

    <!-- ✅ HEADER -->
    <div class="header">
        Welcome to {{ $company }}
    </div>

    <!-- ✅ MAIN CONTENT -->
    <div class="content">

        <!-- ✅ Default email content -->
        <p>Hi {{ strtoupper($name) }},</p>

        <p>Welcome to {{ $company }}!</p>

        <p><strong>Your Login Credentials:</strong></p>
        <p>Email: <b>{{ $email }}</b></p>
        <p>Temporary Password: <b>{{ $password }}</b></p>

        <p>Please login and update your profile.</p>

        <p>
            <a href="{{ url('/login') }}" 
               style="display:inline-block; background-color:#6a1b9a; color:#fff; 
                      padding:12px 25px; text-decoration:none; border-radius:6px; font-weight:bold;">
               Login Now
            </a>
        </p>

        <!-- ✅ Show Best Regards only when NO template master -->
        <!-- @if(!$templateExists)
            <p>Best regards,<br>{{ $signatureText }}</p>
        @endif
         -->
        @if(!empty($mail_footer))
    <div style="margin-top:15px; font-size:12px; color:#666;">
        {!! nl2br($mail_footer) !!}
    </div>
@endif


        @if(!empty($signatureText))
    <p>{!! nl2br($signatureText) !!}</p>
@else
    <p>Best regards,<br>{{ $company }}</p>
@endif



        <!-- ✅ TEMPLATE MASTER CONTENT (only if exists) -->
        @if($templateExists)
            <div style="margin-top: 25px; padding: 20px; 
                        background-color: #f8f9fa; 
                        border-left: 4px solid #6a1b9a; border-radius: 4px;">
                {!! nl2br(e($templateBody)) !!}
            </div>
        @endif

    </div>

    <!-- ✅ FOOTER -->
    <div class="footer">
        © {{ date('Y') }} {{ $company }}. 
        {{ $footerText ?: 'All rights reserved.' }}
    </div>

</div>

</body>
</html>
