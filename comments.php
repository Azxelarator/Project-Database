<!-- ฟังก์ชันสำหรับการตอบกลับคอมเม้นต์ -->
<?php
session_start();
require "config.php";

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
    $postid = $_POST['postid'];
    $comment = $_POST['comment'];
    $date = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO comments (postid, userid, comments, Date) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$postid, $userid, $comment, $date]);

    if ($result) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
<!-- ฟังก์ชันสำหรับการตอบกลับคอมเม้นต์ -->
