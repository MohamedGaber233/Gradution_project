<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['student_name'];
    $national_id = $_POST['national_id'];
    $seat_number = $_POST['seat_number'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $pdo->prepare("INSERT INTO students (student_name, national_id, seat_number, phone, address)
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $national_id, $seat_number, $phone, $address]);
    $student_id = $pdo->lastInsertId();

    header("Location: add_grades.php?student_id=$student_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة طالب - كلية الآداب</title>
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
            height: 60px; /* تعديل حجم الشعار */
        }

        .header h4 {
            font-size: 20px; /* تعديل حجم النص */
            color: #0077b6;
        }

        .form-container {
            display: flex;
            justify-content: center;
            padding: 50px 10px;
        }

        .form-box {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 600px;
        }

        .form-box h3 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-control {
            margin-bottom: 15px;
            padding: 15px;
            font-size: 16px;
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
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <img src="image/download.jpeg" alt="شعار الكلية" class="logo">
    <h4 class="m-0">برنامج التنسيق الإلكتروني - كلية الآداب - جامعة دمنهور</h4>
</div>

<!-- Form Area -->
<div class="form-container">
    <div class="form-box">
        <h3>إدخال بيانات الطالب</h3>
        <form method="POST">
            <input name="student_name" class="form-control" placeholder="اسم الطالب" required><br>
            <input name="national_id" class="form-control" placeholder="الرقم القومي" required><br>
            <input name="seat_number" class="form-control" placeholder="رقم الجلوس" required><br>
            <input name="phone" class="form-control" placeholder="رقم الهاتف"><br>
            <input name="address" class="form-control" placeholder="العنوان"><br>
            <button type="submit" class="btn btn-primary w-100">التالي</button>
        </form>
        <a href="index.php" class="text-link">الرجوع إلى الصفحة الرئيسية</a>
    </div>
</div>

</body>
</html>
