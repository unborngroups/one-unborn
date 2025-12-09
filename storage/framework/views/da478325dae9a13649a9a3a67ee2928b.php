<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Portal Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f5f7fb;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .page-shell {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        .branding-panel {
            background: linear-gradient(180deg, #10163a 0%, #252c6b 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            gap: 24px;
        }

        .branding-panel h1 {
            margin: 0;
            font-size: 2.4rem;
            letter-spacing: 0.5px;
        }

        .branding-panel p {
            margin: 0;
            max-width: 320px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.75);
        }

        .glass-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
            padding: 40px;
            margin: auto;
            width: min(420px, 100%);
        }

        .glass-card h2 {
            margin-bottom: 8px;
            font-size: 1.6rem;
            color: #0f172a;
        }

        .glass-card small {
            color: #475467;
            display: block;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-weight: 600;
            color: #0f172a;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 1rem;
            color: #0f172a;
        }

        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .btn-primary {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
        }

        .meta-note {
            font-size: 0.9rem;
            color: #475467;
            margin-top: 12px;
        }

        .alert {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 0.95rem;
        }

        @media (max-width: 960px) {
            .page-shell {
                grid-template-columns: 1fr;
            }

            .branding-panel {
                text-align: center;
                align-items: center;
                padding: 40px 24px;
            }

            .glass-card {
                margin: 32px auto;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <div class="branding-panel">
            <div>
                <h1>Client Portal</h1>
                <p>Access order status, invoices, and SLA reports from a secure dashboard designed for every client of One-Unborn.</p>
            </div>
            <div>
                <p class="meta-note">Need help? Contact support at <strong>support@oneunborn.com</strong></p>
            </div>
        </div>

        <div class="glass-card">
            <h2>Welcome back</h2>
            <small>Enter your credentials to continue.</small>

            <?php if(session('error')): ?>
                <div class="alert"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <form method="post" action="<?php echo e(route('client.login.submit')); ?>">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="user_name">Username</label>
                    <input id="user_name" type="text" class="form-control" name="user_name" required>
                </div>

                <div class="form-group">
                    <label for="portal_password">Password</label>
                    <input id="portal_password" type="password" class="form-control" name="portal_password" required>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <p class="meta-note">Unlock faster support by keeping your account active and profile up to date.</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH F:\xampp\htdocs\multipleuserpage\resources\views/client_portal/login.blade.php ENDPATH**/ ?>