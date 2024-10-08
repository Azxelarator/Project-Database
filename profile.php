<?php

session_start();
require "config.php"; // เพิ่มการเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

// ถ้าไม่มี user_id ให้ redirect ไปหน้า login
if (!$user_id) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$stmt = $pdo->prepare("SELECT id, username, email, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่าดึงข้อมูลสำเร็จหรือไม่
if (!$user) {
    die("ไม่พบข้อมูลผู้ใช้");
}

// กำหนดค่า profile_image
$profile_image = $user['profile_image'] ?? 'default_avatar.png';
$image_path = "uploads/" . $profile_image;
if (!file_exists($image_path) || !is_readable($image_path)) {
    $image_path = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
}

// ตรวจสอบสิทธิ์การเข้าถึงโฟลเดอร์ uploads
$upload_dir = "uploads";
if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
    error_log("Upload directory is not writable or does not exist: " . $upload_dir);
}

// ตรวจสอบสิทธิ์การเข้าถึงไฟล์รูปภาพ
if (file_exists($image_path) && !is_readable($image_path)) {
    error_log("Profile image is not readable: " . $image_path);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    <?php include "css/profile.css"?>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-secondary bg-gradient">
    <!-- navbar -->
    <?php include('nav.php') ?>
    <!-- navbar -->

    <!-- heroes -->
    <section class="vh-auto" style="background-color: secondary;"> <!-- เปลี่ยนจาก vh-100 เป็น vh-auto -->
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-lg-10 mb-4 mb-lprog-0 centered"> <!-- Add centered class here -->
                    <div class="card mb-3" style="border-radius: .5rem;">
                        <div class="row g-0">
                            <div class="col-md-4 gradient-custom text-center text-white"
                                style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                <?php
                                $profile_image = $user['profile_image'] ?? 'default_avatar.png';
                                $image_path = "uploads/" . $profile_image;
                                if (!file_exists($image_path) || !is_readable($image_path)) {
                                    $image_path = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>?t=<?php echo time(); ?>"
                                    alt="Avatar" class="img-fluid my-5" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;" />
                                <form id="profile-image-form" enctype="multipart/form-data">
                                    <label for="profile_image" class="btn btn-light btn-sm">เปลี่ยนรูปโปรไฟล์</label>
                                    <input type="file" id="profile_image" name="profile_image" style="display: none;" accept="image/*">
                                </form>
                                <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                                <p style="font-size: 18px; font-weight: bold;">Rank: 
                                    <span class="rank-badge <?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') ? 'rank-admin' : 'rank-member'; ?>">
                                        <?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin') ? 'Admin' : 'Member'; ?>
                                    </span>
                                </p>
                                <i class="far fa-edit mb-5"></i>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <h6>Information</h6>
                                    <hr class="mt-0 mb-4">
                                    <div class="row pt-1">
                                        <div class="col-6 mb-3">
                                            <h6>Email</h6>
                                            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                            <!-- แสดงอีเมล -->
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h6>Name</h6>
                                            <p class="text-muted"><?php echo htmlspecialchars($user['username']); ?></p>
                                            <!-- แสดงชื่อผู้ใช้ -->
                                        </div>
                                    </div>
                                    <h6>Projects</h6>
                                    <hr class="mt-0 mb-4">
                                    <div class="row pt-1">
                                        <div class="col-6 mb-3">
                                            <h6>Recent</h6>
                                            <p class="text-muted">Lorem ipsum</p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h6>Most Viewed</h6>
                                            <p class="text-muted">Dolor sit amet</p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <a href="#!"><i class="fab fa-facebook-f fa-lg me-3"></i></a>
                                        <a href="#!"><i class="fab fa-twitter fa-lg me-3"></i></a>
                                        <a href="#!"><i class="fab fa-instagram fa-lg"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="container" style="margin-top: 1px; border-top: 2px solid #ccc;">
                                <!-- แสดงชื่อผู้ใช้และปุ่ม Create New Post -->
                                <div class="data-block" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                                    <h3 style="margin-top: 10px; margin-bottom: 0;">Users: <?php echo htmlspecialchars($user['username']); ?></h3>
                                    <a href='Postdata.php' class='btn btn-warning'>Create New Post</a>
                                </div>

                                <!-- แสดงโพสต์ -->
                                <h6>Posts</h6> <!-- Add a heading for the posts section -->
                                <div class='posts'> <!-- เพิ่ม div สำหรับโพสต์ทั้งหมด -->
                                    <?php
                                    include 'config.php';
        
                                    try {
                                    // เตรียมคำสั่ง SQL สำหรับดึงข้อมูลโพสต์
                                        $sql_posts = "SELECT postid, description, title FROM posts WHERE id = ?";
                                        $stmt_posts = $pdo->prepare($sql_posts);
                                        $stmt_posts->execute([$_SESSION['user_id']]);
                                        

                                        while ($row = $stmt_posts->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<div class='post' style='margin-bottom: 20px;'>"; // เพิ่ม div สำหรับโพสต์แต่ละอัน
                                            echo "<p>ID: " . htmlspecialchars($row['postid']) . "</p>";
                                            echo "<p>Title: " . htmlspecialchars($row['title']) . "</p>";
                                            echo "<p>Description: " . htmlspecialchars($row['description']) . "</p>";

                                            // Add form for editing and deleting posts
                                            echo "<div class='d-flex justify-content-start align-items-center' style='margin-top: 10px;'>"; // Added margin-top for spacing
                                            echo "<form method='post' action='editpost.php' class='me-2' style='margin-bottom: 0;'>"; // เพิ่ม margin-bottom: 0 เพื่อกำจัดช่องว่างด้านล่างของฟอร์ม
                                            echo "<input type='hidden' name='postid' value='" . htmlspecialchars($row['postid']) . "'>";
                                            echo "<button type='submit' class='btn btn-secondary action-btn' name='edit'>Edit</button>";
                                            echo "</form>";
                                            echo "<button class='btn btn-danger delete-post action-btn' data-postid='" . htmlspecialchars($row['postid']) . "'>Delete</button>";
                                            echo "</div>"; 
                                            echo "</div>"; // ปิด div สำหรับโพสต์แต่ละอัน
                                        }

                                    } catch (PDOException $e) {
                                        die("Could not retrieve posts: " . $e->getMessage());
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- heroes -->

    <!-- footer -->
    <?php include('footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="js/profile.js"></script>
</body>

</html>