<?php

    session_start();
    require 'config.php';
    $minLength = 6;

    if(isset($_POST['register'])){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

       
    }
    // ตรวจสอบว่าช่องข้อความว่างรึป่าว
    if(empty($username)){
        $_SESSION['error'] = "Please enter your username";
        header("location: register.php");
        exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
    }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['error'] = "Please enter a valid email address";
        header("location: register.php");
        exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
    }else if(strlen($password) < $minLength){
        $_SESSION['error'] = "Please enter a valid password";
        header("location: register.php");
        exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
    }else if ($password !== $confirmPassword){
        $_SESSION['error'] = "Your password do not match";
        header("location: register.php");
        exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
    // ตรวจสอบว่าช่องข้อความว่างรึป่าว
    }else {
    // ตรวจสอบว่ามีชื่อหรืออีเมลซ้ำรึป่าว
        $checkUsername = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $checkUsername->execute([$username]);
        $userNameExists = $checkUsername->fetchColumn();

        $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkEmail->execute([$email]);
        $userEmailExists = $checkEmail->fetchColumn();

        if($userNameExists){
            $_SESSION['error'] = "Username alredy exists";
            header("location: register.php");
            exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
        } else if($userEmailExists){
            $_SESSION['error'] = "Email alredy exists";
            header("location: register.php");
            exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
        // ตรวจสอบว่ามีชื่อหรืออีเมลซ้ำรึป่าว
        } else {
        // Encode password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Encode password

        // เก็บข้อมูลใส่ฐานข้อมูล
            try{

                $stmt = $pdo->prepare("INSERT INTO users(username, email, password,userlevel) VALUES(?, ?, ?,'0')");
                $stmt->execute([$username, $email, $hashedPassword]);

                $_SESSION['success'] = "Registration Successfully";
                header("location: register.php");
                exit(); // เพิ่ม exit เพื่อหยุดการทำงาน

            }catch(PDOException $e){
                $_SESSION['error'] = "Something went wrong, please try again";
                echo "Registration failed: " . $e->getMessage();
                header("location: register.php");
                exit(); // เพิ่ม exit เพื่อหยุดการทำงาน
            }
        // เก็บข้อมูลใส่ฐานข้อมูล

        } 

    }

?>