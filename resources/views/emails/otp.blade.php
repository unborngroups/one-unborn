<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your One-Unborn Login OTP</title>
    <style>
        body {
            background: #f7f3ee;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .otp-card {
            max-width: 420px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(31,75,132,0.10);
            padding: 32px 32px 24px 32px;
            text-align: center;
        }
        .otp-title {
            color: #1f4b84;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .otp-code {
            display: inline-block;
            background: #f8c68e;
            color: #1f1f1f;
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 6px;
            border-radius: 10px;
            padding: 12px 32px;
            margin: 18px 0 12px 0;
            box-shadow: 0 2px 8px rgba(31,75,132,0.08);
        }
        .otp-info {
            color: #555;
            font-size: 1rem;
            margin-bottom: 18px;
        }
        .otp-footer {
            color: #888;
            font-size: 0.95rem;
            margin-top: 24px;
        }
        .brand {
            margin-top: 18px;
            font-weight: 600;
            color: #b21e1c;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="otp-card">
        <div class="otp-title">One-Unborn Login OTP</div>
        <div class="otp-info">Use the following OTP to login. This code is valid for 5 minutes.</div>
        <div class="otp-code">{{ $otp }}</div>
        <div class="otp-info">Do not share this OTP with anyone for security reasons.</div>
        <div class="otp-footer">
            Thank you,<br>
            <span class="brand">Unborn Networks</span>
        </div>
    </div>
</body>
</html>
