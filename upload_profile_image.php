<?php
// ตรวจสอบให้แน่ใจว่าไม่มีการส่งข้อมูลใดๆ ก่อนหน้านี้
ob_start();
session_start();
require "config.php";

// ตั้งค่า header เป็น JSON
header('Content-Type: application/json');

$upload_dir = "uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
} elseif (!is_writable($upload_dir)) {
    $response = json_encode(["success" => false, "message" => "ไม่สามารถเขียนไฟล์ลงในโฟลเดอร์ uploads ได้"]);
    ob_end_clean(); // ล้าง output buffer
    echo $response;
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_image"])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES["profile_image"];
    
    // ตรวจสอบประเภทไฟล์
    $allowed_types = ["image/jpeg", "image/png", "image/gif"];
    if (!in_array($file["type"], $allowed_types)) {
        $response = json_encode(["success" => false, "message" => "กรุณาอัปโหลดไฟล์รูปภาพเท่านั้น"]);
        ob_end_clean(); // ล้าง output buffer
        echo $response;
        exit;
    }
    
    // สร้างชื่อไฟล์ใหม่
    $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
    $new_filename = "profile_" . $user_id . "_" . time() . "." . $extension;
    $upload_path = $upload_dir . "/" . $new_filename;
    
    // ลบไฟล์เก่า (ถ้ามี)
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $old_image = $stmt->fetchColumn();
    if ($old_image && $old_image != 'default_avatar.png') {
        $old_image_path = $upload_dir . "/" . $old_image;
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
    }
    
    error_log("Upload attempt: " . print_r($_FILES, true));
    if (move_uploaded_file($file["tmp_name"], $upload_path)) {
        error_log("File uploaded successfully: " . $upload_path);
        // อัปเดตฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        if ($stmt->execute([$new_filename, $user_id])) {
            $response = json_encode(["success" => true, "filename" => $new_filename]);
            ob_end_clean(); // ล้าง output buffer
            echo $response;
        } else {
            $response = json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการอัปเดตฐานข้อมูล"]);
            ob_end_clean(); // ล้าง output buffer
            echo $response;
        }
    } else {
        error_log("File upload failed: " . error_get_last()['message']);
        $error = error_get_last();
        $response = json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการอัปโหลดไฟล์: " . $error['message']]);
        ob_end_clean(); // ล้าง output buffer
        echo $response;
    }
} else {
    $response = json_encode(["success" => false, "message" => "คำขอไม่ถูกต้อง"]);
    ob_end_clean(); // ล้าง output buffer
    echo $response;
}
?>
