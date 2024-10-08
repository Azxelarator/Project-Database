<?php 

    session_start();
    require "config.php";   
  
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mainpage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-secondary bg-gradient">
    <!-- navbar -->
    <?php include('nav.php')?>
    <!-- navbar -->

    <!-- heroes -->
    <div class="px-4 py-5 my-5 text-center">
        <!-- <img class="d-block mx-auto mb-4" src="https://getbootstrap.com/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
        <h1 class="display-5 fw-bold text-body-emphasis">Welcome to Let's Talk</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4"> This is a website for people to share their thoughts and ideas with each other. </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center " >
                <button type="button" class="btn btn-warning btn-lg btn-lg px-4 gap-3"> <a href="postdata.php" class="text-body">Create post</a> </button>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <button type="button" class="btn btn-danger btn-lg px-4"><a href="dashboard.php" class="text-light">Go to dashboard</a></button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- heroes -->

    <!-- footer -->
    <?php include('footer.php')?>
    <!-- footer -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>