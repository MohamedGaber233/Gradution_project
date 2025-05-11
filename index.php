<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $national_id = $_POST['national_id'];
    $seat_number = $_POST['seat_number'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE national_id = ? AND seat_number = ?");
    $stmt->execute([$national_id, $seat_number]);
    $student = $stmt->fetch();

    if ($student) {
        $_SESSION['student_id'] = $student['student_id'];
        header("Location: student_data.php");
        exit;
    } else {
        $error = "الرقم القومي أو رقم الجلوس غير صحيح.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - كلية الآداب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #0077b6, #ffffff);
            min-height: 100vh;
            margin: 0;
            font-family: 'Cairo', sans-serif;
        }

        .header {
            background-color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            height: 60px;
        }

        .header h4 {
            font-size: 20px;
            color: #0077b6;
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 10px;
        }

        .login-box {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
            display: flex;
            max-width: 1000px;
            width: 100%;
        }

        .login-form {
            flex: 1;
            padding: 2rem;
        }

        .login-form h3 {
            margin-bottom: 20px;
        }

        .form-control {
            margin-bottom: 15px;
            padding: 15px;
            font-size: 16px;
        }

        .login-illustration {
            flex: 1;
            background-image: url('login-image.png');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: center;
        }

        .btn-primary {
            background: linear-gradient(to right, #0077b6, #00b4d8);
            border: none;
            padding: 12px;
        }

        .text-link {
            display: block;
            margin-top: 15px;
            text-align: center;
            font-size: 16px;
        }

        .text-link a {
            display: inline-block;
            padding: 12px;
            background: linear-gradient(to right, #0077b6, #00b4d8);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            width: 100%;
            text-align: center;
            font-size: 16px;
        }

        .text-link a:hover {
            background: linear-gradient(to right, #00b4d8, #0077b6);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-illustration img {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <img src="image/download.jpeg" alt="شعار الكلية" class="logo">
    <h4 class="m-0">برنامج التنسيق الإلكتروني - كلية الآداب - جامعة دمنهور</h4>
</div>

<!-- Login Area -->
<div class="login-container">
    <div class="login-box">
        
        <!-- Left image -->
        <div class="login-illustration">
            <img src="image/Capture.JPG" alt="صورة توضيحية">
        </div>

        <!-- Right form -->
        <div class="login-form">
            <h3 class="text-center">تسجيل الدخول</h3>
            <form method="POST" >
                <input type="text" name="national_id" class="form-control" placeholder="الرقم القومي" required>
                <input type="text" name="seat_number" class="form-control" placeholder="رقم الجلوس" required>
                <button  class="btn btn-primary w-100">نسجل الدخول</button>
            </form>

            <div class="text-link">
                <a href="add_student.php">إضافة طالب جديد</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
