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
    <title>test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- navbar -->
    <?php include('nav.php') ?>
    <!-- navbar -->

    <!-- heroes -->
    <div>
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
            <div class="d-flex align-items-center p-3 my-3 text-white bg-purple rounded shadow-sm">
                <!-- Logo -->
                <div>
                    <a href="profile.php?id=<?php echo urlencode($userData['id']); ?>">
                        <img class="me-3 rounded-circle" src="https://i.pinimg.com/236x/ca/7d/6a/ca7d6a4f6887b0a306273f7b2126ad2e.jpg" alt="โปรไฟล์ผู้ใช้ <?php echo htmlspecialchars($userData['username']); ?>"
                            width="48" height="48">
                    </a>
                </div>
                <div class="lh-1">
                    <h1 class="h6 mb-0 lh-1">
                        <a href="profile.php?id=<?php echo urlencode($userData['id']); ?>" class="text-black text-decoration-none">
                            <?php echo htmlspecialchars($userData['username']); ?>
                        </a>
                    </h1>
                </div>
            </div>
            <!-- <h6 class="border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-gray-800 ">Total post : </h6>  -->
            <div class="my-3 p-3 bg-body rounded shadow-sm">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :user_id ORDER BY date DESC LIMIT 1");
                    $stmt->execute(['user_id' => $user_id]);
                    $latestPost = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($latestPost) {
                        echo "<h6 class='border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-gray-800'>Latest Post:</h6>";
                        echo "<div class='post-item'>";
                        echo "<h5 class='text-primary'>Title : " . htmlspecialchars($latestPost['title']) . "</h5>";
                        echo "<p>Description : " . htmlspecialchars($latestPost['description']) . "</p>";
                        echo "<small>Posted on: " . htmlspecialchars($latestPost['date']) . "</small>";
                        echo "</div>";
                    } else {
                        echo "<p>No posts available.</p>";
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


    <!-- footer -->
    <?php include('footer.php') ?>
    <!-- footer -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eHz" crossorigin="anonymous">
        </script>
</body>

</html>