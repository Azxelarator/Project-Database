<?php

session_start();
require "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userFound = true; // เพิ่มตัวแปรเพื่อตรวจสอบว่าพบผู้ใช้หรือไม่

// ตรวจสอบว่ามีการส่งชื่อผู้ใช้มาหรือไม่
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูลตามชื่อผู้ใช้
    $stmt = $pdo->prepare("SELECT id, username, email, profile_image, userlevel FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าพบผู้ใช้หรือไม่
    if (!$user) {
        $userFound = false; // ไม่พบผู้ใช้
    } else {
        // ดึงโพสต์ของผู้ใช้ที่ค้นหา
        $stmt_posts = $pdo->prepare("SELECT postid, description, title FROM posts WHERE id = ?");
        $stmt_posts->execute([$user['id']]);
        $posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // ถ้าไม่มีการค้นหา ให้ใช้ข้อมูลของผู้ใช้ที่ล็อกอินอยู่
    $stmt = $pdo->prepare("SELECT id, username, email, profile_image, userlevel FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ดึงโพสต์ของผู้ใช้ที่ล็อกอินอยู่
    $stmt_posts = $pdo->prepare("SELECT postid, description, title FROM posts WHERE id = ?");
    $stmt_posts->execute([$_SESSION['user_id']]);
    $posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
}

// กำหนดค่า profile_image
$profile_image = $user['profile_image'] ?? 'default_avatar.png';
$image_path = "uploads/" . $profile_image;
if (!file_exists($image_path) || !is_readable($image_path)) {
    $image_path = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    <?php include "css/profile.css"?>
    </style>
</head>
<body>
    <?php include('nav.php') ?>

    <?php if ($userFound): ?>
    <!-- เนื้อหาหน้าโปรไฟล์ (คงเดิม) -->
    <section class="vh-auto" style="background-color: #f4f5f7;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-lg-10 mb-4 mb-lg-0 centered">
                    <div class="card mb-3" style="border-radius: .5rem;">
                        <div class="row g-0">
                            <div class="col-md-4 gradient-custom text-center text-white"
                                style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                <img src="<?php echo htmlspecialchars($image_path); ?>?t=<?php echo time(); ?>"
                                    alt="Avatar" class="img-fluid my-5" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;" />
                                <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                                <p style="font-size: 18px; font-weight: bold;">Rank: 
                                    <span class="rank-badge <?php echo ($user['userlevel'] == 1) ? 'rank-admin' : 'rank-member'; ?>">
                                        <?php echo ($user['userlevel'] == 1) ? 'Admin' : 'Member'; ?>
                                    </span>
                                </p>
                                <i class="far fa-edit mb-5"></i>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <h6>ข้อมูล</h6>
                                    <hr class="mt-0 mb-4">
                                    <div class="row pt-1">
                                        <div class="col-6 mb-3">
                                            <h6>อีเมล</h6>
                                            <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <h6>ชื่อผู้ใช้</h6>
                                            <p class="text-muted"><?php echo htmlspecialchars($user['username']); ?></p>
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
                                <!-- แสดงชื่อผู้ใช้และปุ่ม Create New Post (เฉพาะเจ้าของโปรไฟล์) -->
                                <div class="data-block" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                                    <h3 style="margin-top: 10px; margin-bottom: 0;">Users: <?php echo htmlspecialchars($user['username']); ?></h3>
                                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                        <a href='Postdata.php' class='btn' style='background-color: #FFB6C1; color: #000;'>Create New Post</a>
                                    <?php endif; ?>
                                </div>

                                <!-- แสดงโพสต์ -->
                                <h6>โพสต์</h6>
                                <div class='posts'>
                                    <?php
                                    try {
                                        $sql_posts = "SELECT postid, description, title FROM posts WHERE id = ?";
                                        $stmt_posts = $pdo->prepare($sql_posts);
                                        $stmt_posts->execute([$user['id']]);
                                        
                                        while ($row = $stmt_posts->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<div class='post' style='margin-bottom: 20px;'>";
                                            echo "<p>ID: " . htmlspecialchars($row['postid']) . "</p>";
                                            echo "<p>Title: " . htmlspecialchars($row['title']) . "</p>";
                                            echo "<p>Description: " . htmlspecialchars($row['description']) . "</p>";

                                            // แสดงปุ่ม Edit และ Delete เฉพาะเจ้าของโพสต์
                                            if ($user['id'] == $_SESSION['user_id']) {
                                                echo "<div class='d-flex' style='margin-top: 10px;'>";
                                                echo "<form method='post' action='editpost.php' class='me-2'>";
                                                echo "<input type='hidden' name='postid' value='" . htmlspecialchars($row['postid']) . "'>";
                                                echo "<button type='submit' class='btn btn-secondary' name='edit'>Edit</button>";
                                                echo "</form>";
                                                echo "<form method='post' action='deletepost.php'>";
                                                echo "<input type='hidden' name='postid' value='" . htmlspecialchars($row['postid']) . "'>";
                                                echo "<button class='btn btn-danger' name='delete' type='submit'>Delete</button>";
                                                echo "</form>";
                                                echo "</div>";
                                            }
                                            echo "</div>";
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
    <?php endif; ?>

    <?php include('footer.php') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    <?php if (!$userFound): ?>
    Swal.fire({
        title: 'ไม่พบข้อมูลผู้ใช้',
        icon: 'error',
        confirmButtonText: 'ตกลง'
    }).then((result) => {
        if (result.isConfirmed) {
            window.history.back(); // กลับไปหน้าที่มาก่อนหน้านี้
        }
    });
    <?php endif; ?>
    </script>
</body>
</html>