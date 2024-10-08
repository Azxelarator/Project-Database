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

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    
    // Fetch the specific post
    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.id = users.id WHERE posts.id = :post_id");
    $stmt->execute(['post_id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        // Display the post details
        // ... (use $post array to display title, description, etc.)

        // Fetch and display comments for this post
        // ... (use $post_id to fetch comments)
    } else {
        echo "<p>Post not found.</p>";
    }
} else {
    echo "<p>No post specified.</p>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let's Talk - Comment Post</title>
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
            // เปลี่ยนจาก $user_id เป็น $post['user_id']
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$post['id']]);
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
                </div>
                <div class="lh-1">
                    <h1 class="h6 mb-0 lh-1">
                        <a href="profileuser.php?username=<?php echo urlencode($userData['username']); ?>" class="text-light text-decoration-none">
                            <?php echo htmlspecialchars($userData['username']); ?>
                        </a>
                    </h1>
                </div>
            </div>
            <div class="my-3 p-3 bg-secondary rounded shadow-sm">
                <?php
                if (isset($_GET['post_id'])) {
                    $post_id = $_GET['post_id'];
                    try {
                        $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.id = users.id WHERE posts.id = :post_id");
                        $stmt->execute(['post_id' => $post_id]);
                        $post = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($post) {
                            echo "<h6 class='border-bottom pb-2 mb-0 h5 mb-0 font-weight-bold text-light'>Post Details:</h6>";
                            echo "<div class='post-item'>";
                            echo "<h5 class='text-light fw-bold pt-2'>Title : " . htmlspecialchars($post['title']) . "</h5>";
                            echo "<p class='text-light'>Description : " . htmlspecialchars($post['description']) . "</p>";
                            echo "<small class='text-grey'>Posted by: " . htmlspecialchars($post['username']) . " on " . htmlspecialchars($post['date']) . "</small>";
                            echo "</div>";
                        } else {
                            echo "<p class='text-light'>Post not found.</p>";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                } else {
                    echo "<p class='text-light'>No post specified.</p>";
                }
                ?>
            </div>
            <!-- Comment Form -->
            <form id="commentForm" method="POST" action="comments.php">
                <input type="hidden" name="postid" value="<?php echo isset($post_id) ? htmlspecialchars($post_id) : ''; ?>">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="comment" placeholder="Add a comment..." required>
                    <button class="btn btn-primary" type="submit">Post</button>
                </div>
            </form>
            <div id="commentsSection" class="bg-secondary rounded shadow-sm p-3">
                <!-- Comments will be loaded here -->
            </div>
            <script>
                // Function to load comments
                function loadComments() {
                    fetch('load_comments.php?postid=<?php echo isset($post_id) ? urlencode($post_id) : ''; ?>')
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('commentsSection').innerHTML = data;
                        })
                        .catch(error => console.error('Error:', error));
                }

                // Load comments when the page loads
                document.addEventListener('DOMContentLoaded', loadComments);

                document.getElementById('commentForm').addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission
                    const formData = new FormData(this);
                    fetch('comments.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(() => {
                        loadComments(); // Reload comments after posting
                        this.reset(); // Reset the form
                    })
                    .catch(error => console.error('Error:', error));
                });
            </script>
            <!-- Comment Form -->

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