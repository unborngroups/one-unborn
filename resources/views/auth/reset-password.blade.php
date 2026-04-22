<!DOCTYPE html>

<html lang="en">

<head>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password | One-Unborn</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #f7f1e5 0%, #f8ece0 50%, #a7c0de 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: 'Poppins', system-ui, sans-serif;
            }
            .auth-shell {
                width: min(1100px, 90vw);
                border-radius: 32px;
                background: #fff;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                box-shadow: 0 30px 60px rgba(10,34,66,0.25);
                overflow: hidden;
            }
            .auth-form {
                padding: 48px 48px 36px;
            }
            .auth-form h2 {
                font-weight: 700;
                margin-bottom: 8px;
                color: #1f1f1f;
            }
            .auth-form p {
                margin-bottom: 28px;
                color: #5c5b5b;
            }
            .auth-form .form-control {
                border-radius: 12px;
                margin-bottom: 16px;
                padding: 14px 16px;
                border: 1px solid #d0d0d0;
                transition: border-color 0.2s;
            }
            .auth-form .form-control:focus {
                border-color: #1f4b84;
                box-shadow: none;
            }
            .auth-form .btn-primary {
                display: inline-flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                border-radius: 999px;
                padding: 14px 40px;
                font-weight: 600;
                background-image: linear-gradient(135deg, #f8c68e, #1f4b84);
                border: none;
                color: #fff;
                text-transform: uppercase;
                box-shadow: 0 20px 40px rgba(31,75,132,0.45);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                position: relative;
                overflow: hidden;
            }
            .auth-form .btn-primary::after {
                content: '';
                position: absolute;
                width: 64px;
                height: 64px;
                background: rgba(255,255,255,0.18);
                border-radius: 50%;
                right: -12px;
                top: -18px;
                z-index: 1;
                filter: blur(0.5px);
            }
            .auth-form .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 24px 38px rgba(31,75,132,0.5);
            }
            .auth-form .btn-primary span,
            .auth-form .btn-primary i {
                position: relative;
                z-index: 2;
            }
            .auth-form .form-note {
                font-size: 13px;
                color: #6b6b6b;
                margin-top: 16px;
            }
            .auth-side {
                background: linear-gradient(130deg, rgba(253,223,192,0.95), rgba(148,209,252,0.95)),
                            url('https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=60');
                background-size: cover;
                background-position: center;
                color: #fff;
                padding: 46px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .auth-side .logo-badge {
                background: rgba(255,255,255,0.15);
                padding: 18px 22px;
                border-radius: 18px;
                width: max-content;
                margin-bottom: 12px;
            }
            .auth-side span {
                font-size: 14px;
                letter-spacing: 2px;
                text-transform: uppercase;
            }
            .auth-side h3 {
                font-size: 32px;
                font-weight: 600;
                margin-bottom: 8px;
            }
            .auth-side p {
                color: rgba(255,255,255,0.9);
                line-height: 1.6;
            }
            footer.auth-footer {
                font-size: 12px;
                letter-spacing: 1px;
                text-transform: uppercase;
                color: rgba(255,255,255,0.8);
                margin-top: 32px;
            }
            @media (max-width: 768px) {
                body {
                    padding: 32px 0;
                }
                .auth-side {
                    padding: 32px;
                }
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <div class="auth-form">
                <div class="d-flex flex-column">
                    <span class="text-uppercase" style="letter-spacing:4px; font-size:13px; color:#1f4b84;">One-Unborn</span>
                    <h2>Reset Password</h2>
                    <p class="mb-0">Enter your new password below. The reset link is valid for 15 minutes.</p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success mt-3">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                @endif
                <form method="POST" action="/reset-password" class="mt-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="password" name="password" class="form-control" placeholder="New Password" required autofocus>
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-repeat fs-5"></i>
                        Reset Password
                    </button>
                </form>
                <div class="form-note">
                    Remembered your password? <a href="{{ route('login') }}" style="color:#1f4b84; font-weight:600;">Sign In</a>
                </div>
            </div>
            <div class="auth-side">
                <div class="logo-badge">
                    <img src="{{ asset('images/logo1.png') }}" alt="Unborn" style="height:44px;">
                </div>
                <div>
                    <span>Need Help?</span>
                    <h3>Support Team</h3>
                    <p>Reach out to <strong>support@oneunborn.com</strong> if the link doesn't work or if you need a new profile invite.</p>
                </div>
                <footer class="auth-footer">One-Unborn Infrastructure Control</footer>
            </div>
        </div>
    </body>
    </html>
        <!-- New Password -->