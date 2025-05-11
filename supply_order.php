<?php
session_start();
require 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// جلب اسم الطالب
$stmt = $pdo->prepare("SELECT student_name FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// جلب الرغبة الأولى (القسم المختار)
$stmt = $pdo->prepare("SELECT preference_name FROM preferences WHERE student_id = ? ORDER BY preference_order ASC LIMIT 1");
$stmt->execute([$student_id]);
$preference = $stmt->fetch();
$selected_department = $preference ? $preference['preference_name'] : 'لم يتم اختيار قسم';

// تحديد التاريخ غدًا
$supply_date = date('d/m/Y', strtotime('+1 day'));
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إذن التوريد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #fefefe;
            padding: 40px;
        }
        .container {
            border: 2px dashed #0077b6;
            padding: 30px;
            border-radius: 10px;
            background-color: white;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img {
            height: 80px;
            width: auto;
        }
        .btn-print {
            display: block;
            margin: 30px auto 0;
            padding: 10px 30px;
            font-size: 18px;
        }
        table {
            margin: 20px auto;
            width: 80%;
            text-align: center;
        }
        table, th, td {
            border: 1px dashed #0077b6;
            border-collapse: collapse;
            padding: 10px;
        }
        .footer-note {
            text-align: center;
            margin-top: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="logo">
        <img src="image/download.jpeg" alt="شعار الجامعة">
        <div>
            <h4>جامعة دمنهور</h4>
            <h5>إذن توريد</h5>
            <p>يرجى التوجه إلى الخزينة في تاريخ:<br><strong><?= $supply_date ?></strong></p>
        </div>
    </div>

    <table>
        <tr><th>الاسم</th><td><?= $student['student_name'] ?></td></tr>
        <tr><th>القسم المختار</th><td><?= $selected_department ?></td></tr>
        <tr><th>الفرقه</th><td>......................</td></tr>
        <tr><th>التوقيع</th><td>......................</td></tr>
    </table>

    <div class="footer-note">
        برجاء تسليم الإيصال إلى شؤون الطلاب بعد تسديد المبلغ للمحتوى الخاص بالدفعة
    </div>

    <button class="btn btn-primary btn-print" onclick="window.print()">طباعة</button>
</div>

</body>
</html>
