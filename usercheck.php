<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

// สร้างรายการของหน้าที่ไม่ต้องล็อกอิน
$public_pages = ['login.php', 'register.php', 'index.php'];

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้ล็อกอิน และหน้าปัจจุบันไม่อยู่ในรายการ public_pages ให้ redirect ไปที่หน้า login
    $current_page = basename($_SERVER['PHP_SELF']);
    if (!in_array($current_page, $public_pages)) {
        header("Location: login.php");
        exit();
    }
} else {
    // ถ้าล็อกอินแล้ว ให้ดึงข้อมูล userlevel
    $stmt = $pdo->prepare("SELECT userlevel FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($users) {
        $_SESSION['userlevel'] = $users['userlevel'];
        $_SESSION['role'] = ($users['userlevel'] == 1) ? 'Admin' : 'Member';
    } else {
        // ถ้าไม่พบข้อมูลผู้ใช้ ให้แสดงข้อความแทนการ redirect
        echo "ไม่พบข้อมูลผู้ใช้ในฐานข้อมูล";
    }
}

?>