<!-- ใช้สำหรับเชื่อมต่อฐานข้อมูล -->
 <?php


    $host = "localhost";
    $dbname = "customer_db";
    $username = "root"; // สำหรับ XAMPP
    $password = ""; 

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        // ตั้งค่า PDO ให้แสดงข้อผิดพลาด
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?> 

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customer_db";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
