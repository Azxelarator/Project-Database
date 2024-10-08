<?php

session_start();
require "config.php";

// ไม่จำเป็นต้องใช้ jQuery และ CSS ของ SweetAlert1 อีกต่อไป
// แต่ยังคงต้องเพิ่ม script ของ SweetAlert2
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email)) {
        $_SESSION['error'] = "กรุณากรอกอีเมล";
        header("Location: login.php");
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลที่ถูกต้อง";
        header("Location: login.php");
        exit();
    } else if (empty($password)) {
        $_SESSION['error'] = "กรุณากรอกรหัสผ่าน";
        header("Location: login.php");
        exit();
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();

            if ($userData && password_verify($password, $userData['password'])) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['login_success'] = true;
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['login_error'] = true;
                header("Location: login.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง";
            header("Location: login.php");
            exit();
        }
    }
}

