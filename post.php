<?php
    
    include 'config.php'; 
  
    try {
        // Prepare SQL statements to fetch posts and user information
        $sql_posts = "SELECT postid, description, title, date FROM posts WHERE id = :user_id ORDER BY date DESC;";
        $sql_users = "SELECT id, username, email FROM users WHERE id = :user_id;";

        $stmt_posts = $pdo->prepare($sql_posts);
        $stmt_posts->execute(['user_id' => $_SESSION['user_id']]);
        
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->execute(['user_id' => $_SESSION['user_id']]);
        
        // Create a block to display information
        echo "<div class='data-block'>";
       
        // Display user information
        echo "<h3>Users</h3>";
        echo "<ul>";
        while ($row = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>Username: " . htmlspecialchars($row['username']) . "</li>";
        }
        echo "</ul>";

        // Display posts
        echo "<h3>Posts</h3>";
        echo "<ul>";
        while ($row = $stmt_posts->fetch(PDO::FETCH_ASSOC)) {
            echo "<li>Post ID : " . htmlspecialchars($row['postid']) . "<br>" .
                 " Title : " . htmlspecialchars($row['title']) . "<br>" .
                 " Description : " . htmlspecialchars($row['description']) . "<br>" .
                 " Post At : " . htmlspecialchars($row['date']) . "</li>";
        }
        echo "</ul>";

        echo "</div>";
    } catch (PDOException $e) {
        die("Could not retrieve posts: " . $e->getMessage());
    }
?>
