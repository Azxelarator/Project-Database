<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    <?php include "css/sign-in.css"?>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-secondary bg-gradient">
    <!-- navbar -->
    <?php include('nav.php')?>
    <!-- navbar -->

    <!-- sign in -->
    <main class="form-signin w-100 m-auto">
        <form action="login_db.php" method="POST">

            <h1 class="text-light pt-5 mb-3 fs-1 fw-bold">Please Sign-in</h1>

            <?php if(isset($_SESSION['error'])) {?>
                <div class="alert alert-danger" role="alert">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php }?>

            <div class="form-floating">
                <input type="email" class="form-control my-2" name="email" id="floatingInput" placeholder="name@example.com">
                <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control my-2" name="password" id="floatingPassword" placeholder="Password">
                <label for="floatingPassword">Password</label>
            </div>

            <button class="btn btn-warning w-100 py-2" name="login" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-light">&copy; Don't have an account yet? Click here to <a
                    href="register.php">register</a> now</p>
        </form>
    </main>
    <!-- sign in -->

    <!-- footer -->
    <?php include('footer.php')?>
    <!-- footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['login_success'])): ?>
        Swal.fire({
            title: 'เข้าสู่ระบบสำเร็จ!',
            icon: 'success',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'dashboard.php';
            }
        });
        <?php 
        unset($_SESSION['login_success']);
        endif; 
        ?>

        <?php if (isset($_SESSION['login_error'])): ?>
        Swal.fire({
            title: 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
            icon: 'error',
            confirmButtonText: 'ลองอีกครั้ง'
        });
        <?php 
        unset($_SESSION['login_error']);
        endif; 
        ?>
    });
    </script>
</body>

</html>