<!-- หน้าต่างแสดงโพสและผู้ใช้งาน -->
<?php

session_start();
require "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $post = new Post();
    $post->create_post($userid, $data);

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let's Talk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .post-item a:hover h5 {
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-secondary bg-gradient">
    <!-- navbar -->
    <?php include('nav.php') ?>
    <!-- navbar -->

    <!-- heroes -->
    <div >
        <?php
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        try {

            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $userData = $stmt->fetch();

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>
    <!-- heroes -->

    <!-- Posting Dashboard -->
    <main class="container">
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <div class="d-flex align-items-center p-3 my-3 text-white bg-secondary rounded shadow-sm">
                <!-- Logo -->
                <div>
                    <?php
                    $userProfileImage = "uploads/" . ($userData['profile_image'] ?? 'default_avatar.png');
                    if (!file_exists($userProfileImage) || !is_readable($userProfileImage)) {
                        $userProfileImage = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
                    }
                    ?>
                    <img class="me-3 rounded-circle" src="<?php echo htmlspecialchars($userProfileImage); ?>" alt="โปรไฟล์ผู้ใช้ <?php echo htmlspecialchars($userData['username']); ?>"
                        width="48" height="48" style="object-fit: cover;">
                </div>
                <div class="lh-1">
                    <h1 class="h6 mb-0 lh-1">
                        <a href="profileuser.php?username=<?php echo urlencode($userData['username']); ?>" class="text-light text-decoration-none">
                            <?php echo htmlspecialchars($userData['username']); ?>
                        </a>
                    </h1>
                </div>
            </div>
            <!-- <h6 class="border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-gray-800 ">Total post : </h6>  -->
            <div class="my-3 p-3 bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :user_id ORDER BY date DESC LIMIT 1");
                    $stmt->execute(['user_id' => $user_id]);
                    $latestPost = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($latestPost) {
                        echo "<h6 class='border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-light'>Your Latest Post</h6>";
                        echo "<div class='post-item'>";
                        echo "<a href='dashboardComment.php?post_id=" . urlencode($latestPost['id']) . "' class='text-decoration-none'>";
                        echo "<h5 class='text-light pt-2'>Title : " . htmlspecialchars($latestPost['title']) . "</h5>";
                        echo "</a>";
                        echo "<p class='text-light'>Description : " . htmlspecialchars($latestPost['description']) . "</p>";
                        echo "<small class='text-grey'>Posted on: " . htmlspecialchars($latestPost['date']) . "</small>";
                        echo "</div>";
                    } else {
                        echo "<p class='text-light'>No posts available.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }

                ?>
            </div>

            <small class="d-block text-end mt-3">
                <!-- <a href="#">All updates</a> -->
            </small>
        </div>
    </main>
    <!-- Posting Dashboard -->


    
    <!-- Posting Dashboard -->
    <main class="container">
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <div class="d-flex align-items-center p-3 my-3 text-white bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT users.id, users.username, users.profile_image, posts.date, posts.id AS post_id, posts.title, posts.description
                                           FROM users 
                                           JOIN posts ON users.id = posts.id
                                           ORDER BY posts.date DESC 
                                           LIMIT 1");
                    $stmt->execute();
                    $latestPost = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($latestPost) {
                        ?>
                        <!-- Logo -->
                        <div>
                            <?php
                            $latestUserProfileImage = "uploads/" . ($latestPost['profile_image'] ?? 'default_avatar.png');
                            if (!file_exists($latestUserProfileImage) || !is_readable($latestUserProfileImage)) {
                                $latestUserProfileImage = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
                            }
                            ?>
                            <img class="me-3 rounded-circle" src="<?php echo htmlspecialchars($latestUserProfileImage); ?>" alt="โปรไฟล์ผู้ใช้ <?php echo htmlspecialchars($latestPost['username']); ?>"
                                width="48" height="48" style="object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <h1 class="h6 mb-0 lh-1">
                                <a href="profileuser.php?username=<?php echo urlencode($latestPost['username']); ?>" class="text-light text-decoration-none">
                                    <?php echo htmlspecialchars($latestPost['username']); ?>
                                </a>
                            </h1>
                            <small class="text-dark">Latest post: <?php echo htmlspecialchars($latestPost['date']); ?></small>
                        </div>
                        
                        <?php
                    } else {
                        echo "<p>No recent posts available.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
            <div class="my-3 p-3 bg-secondary rounded shadow-sm">
                <?php
                if ($latestPost) {
                    echo "<h6 class='border-bottom border-dark pb-2 mb-0 h5 mb-0 font-weight-bold text-light'>Latest Post from " . htmlspecialchars($latestPost['username']) . "</h6>";
                    echo "<div class='post-item'>";
                    echo "<a href='dashboardComment.php?post_id=" . urlencode($latestPost['post_id']) . "' class='text-decoration-none'>";
                    echo "<h5 class='text-light fw-bold pt-2'>Title : " . htmlspecialchars($latestPost['title']) . "</h5>";
                    echo "</a>";
                    echo "<p class='text-light'>Description : " . htmlspecialchars($latestPost['description']) . "</p>";
                    echo "<small class='text-grey'>Posted on: " . htmlspecialchars($latestPost['date']) . "</small>";
                    echo "</div>";
                } else {
                    echo "<p class='text-light'>No posts available.</p>";
                }
                ?>
            </div>

            <small class="d-block text-end mt-3">
                <!-- <a href="#">All updates</a> -->
            </small>
        </div>
    </main>
    <!-- Posting Dashboard -->

    <!-- เพิ่มกล่องที่ 3 -->
    <main class="container">
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <div class="d-flex align-items-center p-3 my-3 text-white bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT users.id, users.username, users.profile_image, posts.date 
                                           FROM users 
                                           JOIN posts ON users.id = posts.id
                                           WHERE users.id != :current_user_id
                                           ORDER BY posts.date DESC 
                                           LIMIT 1 OFFSET 2"); // เปลี่ยน OFFSET เป็น 2 เพื่อดึงผู้ใช้คนที่ 3
                    $stmt->execute(['current_user_id' => $user_id]);
                    $thirdLatestUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($thirdLatestUser) {
                        ?>
                        <!-- Logo -->
                        <div>
                            <?php
                            $thirdLatestUserProfileImage = "uploads/" . ($thirdLatestUser['profile_image'] ?? 'default_avatar.png');
                            if (!file_exists($thirdLatestUserProfileImage) || !is_readable($thirdLatestUserProfileImage)) {
                                $thirdLatestUserProfileImage = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
                            }
                            ?>
                            <img class="me-3 rounded-circle" src="<?php echo htmlspecialchars($thirdLatestUserProfileImage); ?>" alt="โปรไฟล์ผู้ใช้ <?php echo htmlspecialchars($thirdLatestUser['username']); ?>"
                                width="48" height="48" style="object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <h1 class="h6 mb-0 lh-1">
                                <a href="profileuser.php?username=<?php echo urlencode($thirdLatestUser['username']); ?>" class="text-light text-decoration-none">
                                    <?php echo htmlspecialchars($thirdLatestUser['username']); ?>
                                </a>
                            </h1>
                            <small class="text-dark">Latest post: <?php echo htmlspecialchars($thirdLatestUser['date']); ?></small>
                        </div>
                        <?php
                    } else {
                        echo "<p>No more recent posts available from other users.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
            <div class="my-3 p-3 bg-secondary rounded shadow-sm">
                <?php
                try {
                    // Fetch the latest post from the third user
                    $stmt = $pdo->prepare("SELECT users.username, users.id AS user_id, posts.id AS post_id, posts.title, posts.description, posts.date 
                                            FROM posts 
                                            JOIN users ON posts.id = users.id 
                                            WHERE users.id != :user_id 
                                            ORDER BY posts.date DESC LIMIT 1 OFFSET 2"); // เปลี่ยน OFFSET เป็น 2
                    $stmt->execute(['user_id' => $user_id]);
                    $thirdLatestPost = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($thirdLatestPost) {
                        echo "<h6 class='border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-light'>Latest Post from " . htmlspecialchars($thirdLatestPost['username']) . "</h6>";
                        echo "<div class='post-item'>";
                        echo "<a href='dashboardComment.php?post_id=" . urlencode($thirdLatestPost['post_id']) . "' class='text-decoration-none'>";
                        echo "<h5 class='text-light fw-bold pt-2'>Title : " . htmlspecialchars($thirdLatestPost['title']) . "</h5>";
                        echo "</a>";
                        echo "<p class='text-light'>Description : " . htmlspecialchars($thirdLatestPost['description']) . "</p>";
                        echo "<small class='text-grey'>Posted on: " . htmlspecialchars($thirdLatestPost['date']) . "</small>";
                        echo "</div>";
                    } else {
                        echo "<p>No more posts available.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
        </div>
    </main>
    <!-- จบกล่องที่ 3 -->

    <!-- Box 4 -->
    <main class="container">
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <div class="d-flex align-items-center p-3 my-3 text-white bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT users.id, users.username, users.profile_image, posts.date 
                                           FROM users 
                                           JOIN posts ON users.id = posts.id
                                           WHERE users.id != :current_user_id
                                           ORDER BY posts.date DESC 
                                           LIMIT 1 OFFSET 3");
                    $stmt->execute(['current_user_id' => $user_id]);
                    $fourthLatestUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($fourthLatestUser) {
                        $fourthLatestUserProfileImage = "uploads/" . ($fourthLatestUser['profile_image'] ?? 'default_avatar.png');
                        if (!file_exists($fourthLatestUserProfileImage) || !is_readable($fourthLatestUserProfileImage)) {
                            $fourthLatestUserProfileImage = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
                        }
                        ?>
                        <div>
                            <img class="me-3 rounded-circle" src="<?php echo htmlspecialchars($fourthLatestUserProfileImage); ?>" alt="โปรไฟล์ผู้ใช้ <?php echo htmlspecialchars($fourthLatestUser['username']); ?>"
                                width="48" height="48" style="object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <h1 class="h6 mb-0 lh-1">
                                <a href="profileuser.php?username=<?php echo urlencode($fourthLatestUser['username']); ?>" class="text-light text-decoration-none">
                                    <?php echo htmlspecialchars($fourthLatestUser['username']); ?>
                                </a>
                            </h1>
                            <small class="text-dark">Latest post: <?php echo htmlspecialchars($fourthLatestUser['date']); ?></small>
                        </div>
                        <?php
                    } else {
                        echo "<p>No more recent posts available from other users.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
            <div class="my-3 p-3 bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT users.username, users.id AS user_id, posts.id AS post_id, posts.title, posts.description, posts.date 
                                            FROM posts 
                                            JOIN users ON posts.id = users.id 
                                            WHERE users.id != :user_id 
                                            ORDER BY posts.date DESC LIMIT 1 OFFSET 3");
                    $stmt->execute(['user_id' => $user_id]);
                    $fourthLatestPost = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($fourthLatestPost) {
                        echo "<h6 class='border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-light'>Latest Post from " . htmlspecialchars($fourthLatestPost['username']) . "</h6>";
                        echo "<div class='post-item'>";
                        echo "<a href='dashboardComment.php?post_id=" . urlencode($fourthLatestPost['post_id']) . "' class='text-decoration-none'>";
                        echo "<h5 class='text-light pt-2'>Title : " . htmlspecialchars($fourthLatestPost['title']) . "</h5>";
                        echo "</a>";
                        echo "<p class='text-light'>Description : " . htmlspecialchars($fourthLatestPost['description']) . "</p>";
                        echo "<small class='text-grey'>Posted on: " . htmlspecialchars($fourthLatestPost['date']) . "</small>";
                        echo "</div>";
                    } else {
                        echo "<p class='text-light'>No more posts available.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
        </div>
    </main>
    <!-- End of Box 4 -->

    <!-- Box 5 -->
    <main class="container">
        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <div class="d-flex align-items-center p-3 my-3 text-white bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT users.id, users.username, users.profile_image, posts.date 
                                           FROM users 
                                           JOIN posts ON users.id = posts.id
                                           WHERE users.id != :current_user_id
                                           ORDER BY posts.date DESC 
                                           LIMIT 1 OFFSET 4");
                    $stmt->execute(['current_user_id' => $user_id]);
                    $fifthLatestUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($fifthLatestUser) {
                        $fifthLatestUserProfileImage = "uploads/" . ($fifthLatestUser['profile_image'] ?? 'default_avatar.png');
                        if (!file_exists($fifthLatestUserProfileImage) || !is_readable($fifthLatestUserProfileImage)) {
                            $fifthLatestUserProfileImage = "https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava1-bg.webp";
                        }
                        ?>
                        <div>
                            <img class="me-3 rounded-circle" src="<?php echo htmlspecialchars($fifthLatestUserProfileImage); ?>" alt="โปรไฟล์ผู้ใช้ <?php echo htmlspecialchars($fifthLatestUser['username']); ?>"
                                width="48" height="48" style="object-fit: cover;">
                        </div>
                        <div class="lh-1">
                            <h1 class="h6 mb-0 lh-1">
                                <a href="profileuser.php?username=<?php echo urlencode($fifthLatestUser['username']); ?>" class="text-light text-decoration-none">
                                    <?php echo htmlspecialchars($fifthLatestUser['username']); ?>
                                </a>
                            </h1>
                            <small class="text-dark">Latest post: <?php echo htmlspecialchars($fifthLatestUser['date']); ?></small>
                        </div>
                        <?php
                    } else {
                        echo "<p class='text-light'>No more recent posts available from other users.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
            <div class="my-3 p-3 bg-secondary rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT users.username, users.id AS user_id, posts.id AS post_id, posts.title, posts.description, posts.date 
                                            FROM posts 
                                            JOIN users ON posts.id = users.id 
                                            WHERE users.id != :user_id 
                                            ORDER BY posts.date DESC LIMIT 1 OFFSET 4");
                    $stmt->execute(['user_id' => $user_id]);
                    $fifthLatestPost = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($fifthLatestPost) {
                        echo "<h6 class='border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-light'>Latest Post from " . htmlspecialchars($fifthLatestPost['username']) . "</h6>";
                        echo "<div class='post-item'>";
                        echo "<a href='dashboardComment.php?post_id=" . urlencode($fifthLatestPost['post_id']) . "' class='text-decoration-none'>";
                        echo "<h5 class='text-light pt-2'>Title : " . htmlspecialchars($fifthLatestPost['title']) . "</h5>";
                        echo "</a>";
                        echo "<p class='text-light'>Description : " . htmlspecialchars($fifthLatestPost['description']) . "</p>";
                        echo "<small>Posted on: " . htmlspecialchars($fifthLatestPost['date']) . "</small>";
                        echo "</div>";
                    } else {
                        echo "<p class='text-light'>No more posts available.</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
        </div>
    </main>
    <!-- End of Box 5 -->

    <!-- footer -->
    <?php include('footer.php') ?>
    <!-- footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eHz" crossorigin="anonymous">
        </script>
</body>

</html>