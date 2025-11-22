<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>One-Unborn</title>

    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.jpg')); ?>">



    <!--  Bootstrap CSS for layout and styling -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">



    <style>

        /* Page background and global font */

        body {

            background: #1e73be; /* Blue background color */

            font-family: Arial, sans-serif;

        }



        /*  Full-screen centering container */

        .login-container {

            height: 100vh; /* Full viewport height */

            display: flex;

            justify-content: center; /* Horizontally center */

            align-items: center; /* Vertically center */

        }



        /* Login form box styling */

        .login-box {

            background: #fff; /* White background */

            padding: 30px; /* Inner space */

            border-radius: 10px; /* Rounded corners */

            width: 350px; /* Fixed width for the box */

            text-align: center;

            box-shadow: 0px 4px 10px rgba(0,0,0,0.2); /* Soft shadow */

        }



        /*  Heading styling */

        .login-box h2 {

            margin-bottom: 20px;

            font-weight: bold;

        }



        /*  Input fields spacing */

        .form-control {

            margin-bottom: 15px;

        }



        /*  Footer note styling */

        .footer {

            font-size: 13px;

            margin-top: 20px;

            color: gray;

        }



        /*  Pay Now button container spacing (currently commented out) */

        .pay-now {

            margin-top: 10px;

        }

    </style>

</head>

<body>

    <!--  Main container to center the login box -->

    <div class="login-container">

        <div class="login-box">

            <!--  Application title -->

              <img src="<?php echo e(asset('images/unborn_logo.jpg')); ?>" alt="logo" style="width:50px;">Unborn Networks



            <!-- <h2>Unborn</h2> -->

            <!-- <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.jpg')); ?>"> -->

            <!-- <h5>2025</h5> -->



            

            <?php if($errors->any()): ?>

                <div class="alert alert-danger py-2">

                    <?php echo e($errors->first()); ?>  <!--  Display first validation error -->

                </div>

            <?php endif; ?>



            <!--  Login form -->

            <form action="<?php echo e(route('login')); ?>" method="POST">

                <?php echo csrf_field(); ?>  <!--  Security token to prevent CSRF attacks -->



                <!--  Email input -->

                <input type="email" name="email" class="form-control" placeholder="Email" required>



                <!--  Password input -->

                <input type="password" name="password" class="form-control" placeholder="Password" required>



                <!--  Submit button -->

                <button type="submit" class="btn btn-primary w-100">Sign In</button>

            </form>



            <!-- Footer section with copyright -->

            <div class="footer">

                © Copyright 2011 - 2025 <b>Unborn</b><br>

                All rights reserved.

                <!--  Forgot Password link -->

               <a href="<?php echo e(url('/forgot-password')); ?>">Forgot Password?</a>


            </div>

        </div>

    </div>

</body>

</html>

<?php /**PATH F:\xampp\htdocs\wlcome\multipleuserpage\resources\views/auth/login.blade.php ENDPATH**/ ?>