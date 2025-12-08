<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>One-Unborn</title>

    <!-- Favicon -->

    <link rel="icon" type="image/png" href="{{ asset('images/logo.jpg') }}">

    <!-- Bootstrap 5 CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        /* ======= Page Layout ======= */

        body {
            background-image:url('{{ asset("images/constructions-1.jpeg") }}'); /* Blue background image */
            background-repeat: no-repeat;
            background-size: cover;
            background-position: fixed;
            background-attachment: fixed;


            /* background: linear-gradient(to right, #1e73be, #155a96); */

            font-family: Arial, sans-serif;

            display: flex;

            justify-content: center;

            align-items: center;

            height: 100vh;

            margin: 0;

        }

         /* ======= Login Box Styling ======= */

        .login-box {

            background: #fff;

            padding: 30px 25px;

            border-radius: 12px;

            width: 100%;

            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;

            box-shadow: 0px 6px 15px rgba(0,0,0,0.25);

            text-align: center;

        }
        .login-box:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0px 10px 20px rgba(0,0,0,0.35);
}

        .login-box h3 {

            margin-bottom: 20px;

            font-weight: bold;

            color: #1e73be;

        }

        /* ======= Form Controls ======= */

        .form-control {

            border-radius: 8px;

            padding: 10px;

            margin-bottom: 15px;

        }

        /* ======= Submit Button ======= */

        .btn-success {

            width: 100%;

            border-radius: 8px;

            padding: 10px;

            font-weight: bold;

            background-color: #28a745;

            border: none;

        }

        .btn-success:hover {

            background-color: #218838;

        }

        /* ======= Labels ======= */

        label {

            font-weight: bold;

            float: left;

            margin-bottom: 5px;

            color: #333;

        }

        /* ======= Alert Styling ======= */

        .alert {

            text-align: left;

            font-size: 14px;

            margin-bottom: 15px;

        }

    </style>

</head>

<body>

    

<div class="login-box">

    <h3>Reset Password</h3>



    <!-- Flash success message (session) -->

    @if (session('status'))

        <div class="alert alert-success">{{ session('status') }}</div>

    @endif



    <!-- Password Reset Form -->

    <form method="POST" action="/reset-password">


        @csrf

        <!-- Laravel password reset token -->

        <input type="hidden" name="token" value="{{ $token }}">



        <!-- Email Address -->

        <div class="mb-3">

            <!-- <label>Email Address</label> -->
            <input type="hidden" name="email" value="{{ $email }}">



        </div>



        <!-- New Password -->

        <div class="mb-3">

            <label>New Password</label>

            <input type="password" name="password" class="form-control" required>

        </div>



        <!-- Confirm Password -->

        <div class="mb-3">

            <label>Confirm Password</label>

            <input type="password" name="password_confirmation" class="form-control" required>

        </div>



        <!-- Submit Button -->

        <button type="submit" class="btn btn-success">Reset Password</button>

    </form>

</div>



</body>

</html>

