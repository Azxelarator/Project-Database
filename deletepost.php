<?php
    session_start();
    require "config.php";

    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not authorized']);
        exit;
    }

    if (isset($_POST['postid'])) {
        $postid = $_POST['postid'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM posts WHERE postid = ? AND id = ?");
            $result = $stmt->execute([$postid, $_SESSION['user_id']]);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No post ID provided']);
    }
?>