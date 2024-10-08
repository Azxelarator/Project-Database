<?php
require "config.php";

$postid = $_GET['postid'] ?? null;

if ($postid) {
    $stmt_comments = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.userid = u.id WHERE c.postid = :postid ORDER BY c.date DESC");
    $stmt_comments->execute(['postid' => $postid]);
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comments as $comment) {
        echo "<div class='comment'>";
        echo "<strong>" . htmlspecialchars($comment['username']) . "</strong> <small>" . htmlspecialchars($comment['Date']) . "</small>";
        echo "<p>" . htmlspecialchars($comment['comments']) . "</p>";
        echo "<form method='post' action='deletecomment.php' style='display:inline;'>";
        echo "<input type='hidden' name='commentid' value='" . htmlspecialchars($comment['commentid']) . "'>";
        echo "<button type='submit' class='btn btn-danger btn-sm' name='delete'>Delete</button>";
        echo "</form>";
        echo "</div>";
    }
}
?>