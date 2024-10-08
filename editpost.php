<?php

session_start();
require 'config.php';

if (isset($_POST['postid'])) {
    $postid = $_POST['postid'];

    // Fetch the existing post data
    $stmt = $pdo->prepare("SELECT title, description FROM posts WHERE postid = ?");
    $stmt->execute([$postid]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        $title = $post['title'];
        $description = $post['description'];
    } else {
        $_SESSION['error'] = "Post not found.";
        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['editpost'])) {
        $postid = $_POST['postid'];
        $title = $_POST['title'];
        $description = $_POST['description'];

        $stmt1 = $pdo->prepare("UPDATE posts SET title = ?, description = ? WHERE postid = ?");
        $stmt1->execute([$title, $description, $postid]);

        if ($stmt1 != $stmt) {
            $_SESSION['success'] = "Post updated successfully.";
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['error'] = "You can't update the same post";
            header("Location: editpost.php?postid=" . $postid);
            exit();
        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        <?php include "css/sign-in.css" ?>
        <?php include "css/edit.css" ?>
    </style>
</head>

<body>
    <!-- navbar -->
    <?php include('nav.php') ?>
    <!-- navbar -->

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1 class="fw-bold mb-5 mx-1 mt-4">Edit Post</h1>

                <form action="" method="POST">
                    <input type="hidden" name="postid" value="<?php echo htmlspecialchars($postid); ?>">

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter your title"
                            value="<?php echo htmlspecialchars($title); ?>">
                        <label for="title">Title</label>
                    </div>

                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="description" name="description"
                            placeholder="Enter your description"
                            style="height: 100px"><?php echo htmlspecialchars($description); ?></textarea>
                        <label for="description">Description</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" name="editpost" type="submit">Update Post</button>
                        <a href="profile.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include('footer.php') ?>
    <!-- footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>