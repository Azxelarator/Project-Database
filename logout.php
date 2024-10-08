<?php 

    session_start();
    session_unset(); // ลบข้อมูลเซสชัน
    session_destroy(); // ทำลายเซสชัน
    header("Location: index.php"); // เปลี่ยนเส้นทางไปยังหน้าแรก
    exit();

?>