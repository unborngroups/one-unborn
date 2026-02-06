<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Unborn</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.jpg')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f7f3ee;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', system-ui, sans-serif;
            padding: 24px;
        }
        .auth-shell {
            width: min(1200px, 95vw);
            border-radius: 32px;
            background: #fff;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            box-shadow: 0 40px 60px rgba(10, 30, 60, 0.25);
            overflow: hidden;
        }
        .auth-form {
            padding: 52px 56px 42px;
        }
        .auth-form h2 {
            font-weight: 700;
            margin-bottom: 6px;
            color: #1f1f1f;
            letter-spacing: -0.3px;
        }
        .auth-form p {
            margin-bottom: 32px;
            color: #5c5b5b;
        }
        .auth-form .form-control {
            border-radius: 12px;
            margin-bottom: 18px;
            padding: 16px 18px;
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
            text-transform: uppercase;
            background-image: linear-gradient(135deg, #f8c68e, #1f4b84);
            border: none;
            color: #fff;
            box-shadow: 0 20px 40px rgba(31, 75, 132, 0.45);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .auth-form .btn-primary::after {
            content: '';
            position: absolute;
            width: 64px;
            height: 64px;
            right: -12px;
            top: -18px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            z-index: 1;
            filter: blur(0.5px);
        }
        .auth-form .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 38px rgba(31, 75, 132, 0.5);
        }
        .auth-form .btn-primary span,
        .auth-form .btn-primary i {
            position: relative;
            z-index: 2;
        }
        .auth-form .forgot-link {
            display: block;
            margin-top: 16px;
            text-decoration: none;
            color: #1f4b84;
            font-weight: 500;
        }
        .auth-side {
            background: linear-gradient(135deg, #fbe7d2 0%, #b7c9ff 100%);
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 24px;
            position: relative;
        }
        .auth-side::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.3), transparent 45%);
            pointer-events: none;
        }
        .auth-side .panel-content {
            position: relative;
            z-index: 1;
        }
        .auth-side h3 {
            font-size: 2.00rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1f1f1f;
            letter-spacing: -0.3px;
        }
        .auth-side span {
            font-size: 14px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.9);
        }
        .auth-side p {
            color: rgba(255,255,255,0.85);
        }
        .social-row {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .social-link {
            width: 42px;
            height: 42px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 10px 25px rgba(6,31,63,0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .social-link i {
            font-size: 1.3rem;
        }
        .social-link:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 16px 35px rgba(6,31,63,0.35);
        }
        .social-link.facebook {
            background: #1877f2;
        }
        .social-link.linkedin {
            background: #0a66c2;
        }
        .social-link.instagram {
            background: radial-gradient(circle at 30% 30%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285aeb 90%);
        }
        .social-link.whatsapp {
            background: #25d366;
        }
        .shield {
            background: rgba(255, 255, 255, 0.2);
            padding: 18px 22px;
            border-radius: 18px;
            width: max-content;
            position: relative;
            z-index: 1;
        }
        footer.auth-footer {
            font-size: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 12px;
            position: relative;
            z-index: 1;
        }
        @media (max-width: 768px) {
            body {
                padding: 16px;
            }
            .auth-form {
                padding: 40px 32px 36px;
            }
            .auth-side {
                padding: 36px 28px;
            }
        }
    </style>
</head>
<body>
    <?php
        $company = $company ?? \App\Models\CompanySetting::first();
    ?>
    <div class="auth-shell">
        <div class="auth-form">
            <div class="d-flex flex-column">
                <span class="text-uppercase" style="letter-spacing: 4px; font-size: 13px; color: #b21e1c;">One-Unborn</span>
                <h2>Login to Dashboard</h2>
                <p class="mb-0">Enter your credentials to access the ISP operations console.</p>
            </div>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger py-2 mt-3">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
            <form id="loginForm" action="<?php echo e(route('login')); ?>" method="POST" class="mt-4" autocomplete="off" onsubmit="return false;">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <div class="input-group">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Official Email" value="<?php echo e(old('email')); ?>" required autofocus>
                        <button type="button" id="verifyEmailBtn" class="btn btn-outline-primary px-3 py-1" style="height: 58px; font-size: 15px;">Verify</button>
                    </div>
                </div>
                <div class="mb-3" id="otpSection" style="display:none;">
                    <div class="input-group">
                        <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter OTP">
                        <button type="button" id="verifyOtpBtn" class="btn btn-outline-success px-3 py-1" style="height: 58px; font-size: 15px;">Verify OTP</button>
                    </div>
                </div>
                <div class="mb-3" id="passwordSection" style="display:none;">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                </div>
                <button type="submit" id="loginBtn" class="btn btn-primary w-100" style="display:none;">
                    <i class="bi bi-arrow-right-short fs-5"></i>
                    <span>Sign In</span>
                </button>
            </form>
            <a href="<?php echo e(url('/forgot-password')); ?>" class="forgot-link">Forgot password?</a>
            <script>
                function showAlert(message, type = 'danger') {
                    let alertDiv = document.createElement('div');
                    alertDiv.className = `alert alert-${type} py-2 mt-3`;
                    alertDiv.innerText = message;
                    let form = document.querySelector('.auth-form form');
                    form.parentNode.insertBefore(alertDiv, form);
                    setTimeout(() => alertDiv.remove(), 4000);
                }

                document.getElementById('verifyEmailBtn').onclick = function() {
                    let email = document.getElementById('email').value;
                    let btn = document.getElementById('verifyEmailBtn');
                    if (!email) {
                        showAlert('Please enter your email.');
                        return;
                    }
                    btn.disabled = true;
                    let originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
                    fetch("<?php echo e(route('otp.send')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                        },
                        body: JSON.stringify({ email })
                    })
                    .then(res => res.json())
                        .then(data => {
                            if (data.status) {
                                if (data.require_otp_always) {
                                    document.getElementById('otpSection').style.display = 'block';
                                } else {
                                    document.getElementById('otpSection').style.display = 'none';
                                    document.getElementById('passwordSection').style.display = 'block';
                                    document.getElementById('loginBtn').style.display = 'block';
                                }
                                showAlert(data.message, 'success');
                            } else {
                                showAlert(data.message || 'Failed to send OTP.');
                            }
                        })
                    .catch(() => showAlert('Server error. Please try again.'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
                };

                document.getElementById('verifyOtpBtn').onclick = function() {
                    let email = document.getElementById('email').value;
                    let otp = document.getElementById('otp').value;
                    if (!otp) {
                        showAlert('Please enter the OTP.');
                        return;
                    }
                    fetch("<?php echo e(route('otp.verify')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                        },
                        body: JSON.stringify({ email, otp })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            document.getElementById('passwordSection').style.display = 'block';
                            document.getElementById('loginBtn').style.display = 'block';
                            showAlert(data.message, 'success');
                            // Disable email and OTP fields after successful OTP verification
                            document.getElementById('email').readOnly = true;
                            document.getElementById('verifyEmailBtn').disabled = true;
                            document.getElementById('otp').readOnly = true;
                            document.getElementById('verifyOtpBtn').disabled = true;
                        } else {
                            showAlert(data.message || 'OTP verification failed.');
                        }
                    })
                    .catch(() => showAlert('Server error. Please try again.'));
                };

                // AJAX password submit
                document.getElementById('loginForm').onsubmit = function(e) {
                    e.preventDefault();
                    let email = document.getElementById('email').value;
                    let otp = document.getElementById('otp').value;
                    let password = document.getElementById('password').value;
                    let btn = document.getElementById('loginBtn');
                    btn.disabled = true;
                    let originalText = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';
                    fetch("<?php echo e(route('login')); ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
                        },
                        body: JSON.stringify({ email, otp, password })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            window.location.href = data.redirect || '/dashboard';
                        } else {
                            showAlert(data.message || 'Invalid credentials.');
                            document.getElementById('password').value = '';
                        }
                    })
                    .catch(() => showAlert('Server error. Please try again.'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    });
                };
            </script>
            <div class="mt-3 d-flex gap-2 align-items-center">
                <i class="bi bi-shield-lock-fill text-danger fs-5"></i>
                <span class="text-muted" style="font-size: 14px;">Protected by One-Unborn security.</span>
            </div>
            <div class="mt-4" style="font-size: 12px; color: #7a7a7a;">
                © <?php echo e(date('Y')); ?> <strong>Unborn Networks</strong>. All rights reserved.
            </div>
        </div>
        <div class="auth-side">
            <div class="panel-content">
                <span style="color: #b21e1c;">Welcome Back</span>
                <h3>Unborn Networks</h3>
                <p class="mb-0" style="color: #5c5b5b;">Connecting service providers across India. Monitor feasibilities, vendors, and deliverables from a single panel.</p>
            </div>
            <div class="panel-content">
                <div class="social-row">
                    <?php if(!empty($company?->linkedin_url)): ?>
                        <a class="social-link linkedin" href="<?php echo e($company->linkedin_url); ?>" target="_blank" aria-label="LinkedIn">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($company?->facebook_url)): ?>
                        <a class="social-link facebook" href="<?php echo e($company->facebook_url); ?>" target="_blank" aria-label="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($company?->instagram_url)): ?>
                        <a class="social-link instagram" href="<?php echo e($company->instagram_url); ?>" target="_blank" aria-label="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($company?->whatsapp_number)): ?>
                        <a class="social-link whatsapp" href="https://wa.me/<?php echo e($company->whatsapp_number); ?>" target="_blank" aria-label="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <div class="shield mb-1">
                    <img src="<?php echo e(asset('images/logo1.png')); ?>" alt="Unborn" style="height: 32px;">
                </div>
                <footer class="auth-footer" style="color: #5c5b5b;">One-Unborn</footer>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/auth/login.blade.php ENDPATH**/ ?>