<?php

if (isset($_GET['postid'])) {
    $postid = $_GET['postid'];

    require 'config.php'; // Ensure the database connection is available

    // Prepare SQL statement to delete the post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE postid = ?");
    
    // Execute the statement with the provided postid
    if ($stmt->execute([$postid])) {
        $_SESSION['success'] = "Post deleted successfully."; // Set success message
    } else {
        $_SESSION['error'] = "Failed to delete post."; // Set error message
    }
    // Redirect back to the admin page
    header("Location: adminpage.php");
    exit();
}
?>