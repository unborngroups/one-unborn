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
        .button {
            display: inline-block;
            background-color: #6a1b9a;
            color: white !important;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
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
    <div class="email-container">
        <div class="header">
            Welcome to {{ $company ?? config('app.name') }}
        </div>

         <div class="content">
            @php
                // Support both types of mail data
                $name = $name ?? ($emailData['name'] ?? $emailData['user']['name'] ?? 'User');
                $email = $email ?? ($emailData['email'] ?? $emailData['user']['email'] ?? '');
                $password = $password ?? ($emailData['password'] ?? '');
                $company = $company ?? ($emailData['company'] ?? config('app.name'));
            @endphp

            {{-- Always show default welcome content first --}}
            <p>Hi {{ ucfirst($name) }},</p>
            <p>Welcome to {{ $company ?? config('app.name') }}!</p>
            
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

            <p>Best regards,<br>HR Team</p>

            @if(isset($emailData['template_body']) && !empty($emailData['template_body']))
                {{-- CASE 1: Add Template Master content below default content (no header, direct content) --}}
                <div class="template-master-content" style="margin-top: 25px; padding: 20px; background-color: #f8f9fa; border-left: 4px solid #6a1b9a; border-radius: 4px;">
                    {!! nl2br(e($emailData['template_body'])) !!}
                </div>
            @elseif(isset($template_body) && !empty($template_body))
                {{-- CASE 1: Alternative template body format (no header, direct content) --}}
                <div class="template-master-content" style="margin-top: 25px; padding: 20px; background-color: #f8f9fa; border-left: 4px solid #6a1b9a; border-radius: 4px;">
                    {!! nl2br(e($template_body)) !!}
                </div>
            @endif

        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ $company ?? config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
