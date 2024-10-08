<?php
if (isset($_POST['delete']) && isset($_POST['commentid'])) {
    $commentid = $_POST['commentid'];

    require 'config.php'; // Ensure the database connection is available

    // Prepare SQL statement to delete the comment
    $stmt = $pdo->prepare("DELETE FROM comments WHERE commentid = ?");
    
    // Execute the statement with the provided commentid
    if ($stmt->execute([$commentid])) {
        $_SESSION['success'] = "Comment deleted successfully."; // Set success message
    } else {
        $_SESSION['error'] = "Failed to delete comment."; // Set error message
    }
    
    // Redirect back to the page where comments are displayed
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
