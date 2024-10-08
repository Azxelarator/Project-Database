<!-- adminpage ทำหน้าที่ดึงข้อมูลออกจากฐานข้อมูลออกมาประมวลผล -->
<?php 

// เริ่มต้น session และเรียกใช้ไฟล์ config.php
session_start();
require 'config.php';

// ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่ ถ้าไม่ใช่จะแสดงข้อความแจ้งเตือน
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("You don't have permission to enter this site!");
}

try {
    $sql_users = "SELECT id, username, email FROM users;";
    $sql_posts = "SELECT postid, title, description, date, id FROM posts;";


    $stmt_users = $pdo->query($sql_users);
    $stmt_posts = $pdo->query($sql_posts);

    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

    // กำหนดจำนวน users และ posts ต่อหน้า
    $items_per_page = 5;

    // สำหรับ Users
    $total_pages_users = ceil($total_users / $items_per_page);
    $current_page_users = isset($_GET['users_page']) ? (int)$_GET['users_page'] : 1;
    $current_page_users = max(1, min($current_page_users, $total_pages_users));
    $offset_users = ($current_page_users - 1) * $items_per_page;

    // สำหรับ Posts
    $total_pages_posts = ceil($total_posts / $items_per_page);
    $current_page_posts = isset($_GET['posts_page']) ? (int)$_GET['posts_page'] : 1;
    $current_page_posts = max(1, min($current_page_posts, $total_pages_posts));
    $offset_posts = ($current_page_posts - 1) * $items_per_page;

    // แก้ไข SQL query สำหรับ Users
    $stmt_users = $pdo->prepare("SELECT id, username, email FROM users LIMIT :limit OFFSET :offset");
    $stmt_users->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt_users->bindValue(':offset', $offset_users, PDO::PARAM_INT);
    $stmt_users->execute();

    // แก้ไข SQL query สำหรับ Posts
    $stmt_posts = $pdo->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.id = u.id ORDER BY p.date DESC LIMIT :limit OFFSET :offset");
    $stmt_posts->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt_posts->bindValue(':offset', $offset_posts, PDO::PARAM_INT);
    $stmt_posts->execute();

} catch (PDOException $e) {
    // แสดงข้อความ error ถ้าเกิดข้อผิดพลาดในการ query
    echo "Error: " . $e->getMessage();
}

// ฟังก์ชันค้นหาผู้ใช้
function searchUser($pdo, $username) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE :username");
    $stmt->execute(['username' => "%$username%"]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ฟังก์ชันดึงโพสต์ของผู้ใช้
function getUserPosts($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :userId ORDER BY date DESC");
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ตัวแปรสำหรับเก็บผลลัพธ์การค้นหา
$searchResult = null;
$userPosts = [];

// ตรวจสอบว่ามีการส่งคำค้นหาผู้ใช้มาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $searchUsername = $_POST['search_username'];
    $searchResult = searchUser($pdo, $searchUsername);
    if ($searchResult) {
        $userPosts = getUserPosts($pdo, $searchResult['id']);
    }
}

// ดึงข้อมูล Users ทั้งหมด
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูล Posts ทั้งหมดจากฐานข้อมูลโดยเรียงตามเวลา
$stmt = $pdo->query("SELECT * FROM posts ORDER BY date DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    <?php include "css/admin.css"?>
    body {
        background-color: #f0f2f5; /* เปลี่ยนสีพื้นหลังให้เข้ากับธีมเว็บ */
    }
    .container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-top: 20px;
    }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="dashboard.php" class="back-to-dashboard">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
        <h2 class="text-center mb-0"><i class="fas fa-user-shield fa-icon"></i>Admin Dashboard</h2>
        <div style="width: 200px;"></div> <!-- ช่องว่างเพื่อให้ Admin Dashboard อยู่ตรงกลาง -->
    </div>

    <!-- กล่องค้นหาผู้ใช้ -->
    <div class="search-container">
        <h3><i class="fas fa-search fa-icon"></i>Search User</h3>
        <form method="POST" class="search-form mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search_username" placeholder="Search for a user..." required>
                <button class="btn btn-primary" type="submit" name="search">Search</button>
            </div>
        </form>
        <div class="user-count">Total Users: <?php echo htmlspecialchars($total_users); ?></div>

        <?php if ($searchResult): ?>
            <div class="search-result mt-3">
                <h4>User Found:</h4>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($searchResult['id']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($searchResult['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($searchResult['email']); ?></p>
                
                <?php if (!empty($userPosts)): ?>
                    <h5>User's Posts:</h5>
                    <ul>
                    <?php foreach ($userPosts as $post): ?>
                        <li>
                            <strong>Post ID:</strong> <?php echo htmlspecialchars($post['postid']); ?><br>
                            <strong>Title:</strong> <?php echo htmlspecialchars($post['title']); ?><br>
                            <strong>Date:</strong> <?php echo htmlspecialchars($post['date']); ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>This user has no posts.</p>
                <?php endif; ?>
            </div>
        <?php elseif (isset($_POST['search'])): ?>
            <p class="text-muted mt-3">No user found with the username '<?php echo htmlspecialchars($_POST['search_username']); ?>'</p>
        <?php endif; ?>
    </div>

    <!-- กล่องค้นหาโพสต์ -->
    <div class="search-container">
        <h3><i class="fas fa-file-alt fa-icon"></i>Search Post</h3>
        <form method="POST" class="search-form mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search_postid" placeholder="Enter Post ID..." required>
                <button class="btn btn-primary" type="submit" name="search_post">Search</button>
            </div>
        </form>
        <div class="post-count">Total Posts: <?php echo htmlspecialchars($total_posts); ?></div>

        <?php
        // ตรวจสอบว่ามีการค้นหาโพสต์หรือไม่
        if (isset($_POST['search_post'])) {
            $search_postid = $_POST['search_postid'];

            // ค้นหาโพสต์ตาม ID
            $stmt_search_post = $pdo->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.id = u.id WHERE p.postid = :postid");
            $stmt_search_post->execute(['postid' => $search_postid]);
            $post = $stmt_search_post->fetch(PDO::FETCH_ASSOC);

            // แสดงผลลัพธ์การค้นหา
            if ($post) {
                echo "<div class='search-result'>";
                echo "<h4>Post Found:</h4>";
                echo "<p><strong>Post ID:</strong> " . htmlspecialchars($post['postid']) . "</p>";
                echo "<p><strong>Title:</strong> " . htmlspecialchars($post['title']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($post['description']) . "</p>";
                echo "<p><strong>Date:</strong> " . htmlspecialchars($post['date']) . "</p>";
                echo "<p><strong>Posted by:</strong> " . htmlspecialchars($post['username']) . "</p>";
                echo "</div>";
            } else {
                echo "<p class='text-muted mt-3'>No post found with ID '" . htmlspecialchars($search_postid) . "'</p>";
            }
        }
        ?>
    </div>

    <div class="row">
        <!-- แสดงรายการผู้ใช้ทั้งหมด -->
        <div class="col-md-6">
            <div class="user-block">
                <h3><i class="fas fa-users fa-icon"></i>Users</h3>
                <ul class="list-group">
                    <?php while ($user = $stmt_users->fetch(PDO::FETCH_ASSOC)) { ?>
                        <li class="list-group-item">
                            <strong>User ID:</strong> <?php echo htmlspecialchars($user['id']); ?><br>
                            <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                        </li>
                    <?php } ?>
                </ul>
                
                <!-- Pagination for Users -->
                <nav aria-label="User Page Navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page_users > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?users_page=<?php echo $current_page_users - 1; ?>&posts_page=<?php echo $current_page_posts; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo; Previous</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages_users; $i++): ?>
                            <li class="page-item <?php echo ($i == $current_page_users) ? 'active' : ''; ?>">
                                <a class="page-link" href="?users_page=<?php echo $i; ?>&posts_page=<?php echo $current_page_posts; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page_users < $total_pages_users): ?>
                            <li class="page-item">
                                <a class="page-link" href="?users_page=<?php echo $current_page_users + 1; ?>&posts_page=<?php echo $current_page_posts; ?>" aria-label="Next">
                                    <span aria-hidden="true">Next &raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- แสดงรายการโพสต์ทั้งหมด -->
        <div class="col-md-6">
            <div class="post-block">
                <h3><i class="fas fa-file-alt fa-icon"></i>Posts</h3>
                <ul class="list-group">
                    <?php while ($post = $stmt_posts->fetch(PDO::FETCH_ASSOC)) { ?>
                        <li class="list-group-item">
                            <strong>Post ID:</strong> <?php echo htmlspecialchars($post['postid']); ?><br>
                            <strong>Title:</strong> <?php echo htmlspecialchars($post['title']); ?><br>
                            <strong>Description:</strong> <?php echo htmlspecialchars(substr($post['description'], 0, 50)) . '...'; ?><br>
                            <strong>Posted by:</strong> <?php echo htmlspecialchars($post['username']); ?><br>
                            <strong>Date:</strong> <?php echo htmlspecialchars($post['date']); ?><br>
                            <div class="mt-2">
                                <a href="edit_post.php?postid=<?php echo htmlspecialchars($post['postid']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_post.php?postid=<?php echo htmlspecialchars($post['postid']); ?>" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                
                <!-- Pagination for Posts -->
                <nav aria-label="Post Page Navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($current_page_posts > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?users_page=<?php echo $current_page_users; ?>&posts_page=<?php echo $current_page_posts - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo; Previous</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages_posts; $i++): ?>
                            <li class="page-item <?php echo ($i == $current_page_posts) ? 'active' : ''; ?>">
                                <a class="page-link" href="?users_page=<?php echo $current_page_users; ?>&posts_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($current_page_posts < $total_pages_posts): ?>
                            <li class="page-item">
                                <a class="page-link" href="?users_page=<?php echo $current_page_users; ?>&posts_page=<?php echo $current_page_posts + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">Next &raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    
</div>

</body>
</html>