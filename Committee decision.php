<?php
require 'db.php';
$student_id = $_GET['student_id'];

// استعلام لجلب بيانات الطالب
$stmt = $pdo->prepare("SELECT student_name, national_id, seat_number FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// استعلام لجلب الرغبات
$stmt_prefs = $pdo->prepare("SELECT preference_name FROM preferences WHERE student_id = ? ORDER BY preference_order LIMIT 1");
$stmt_prefs->execute([$student_id]);
$preference = $stmt_prefs->fetch();

// إذا لم يكن هناك رغبات للطالب، سنقوم بتعيين قيمة افتراضية
$department = $preference ? $preference['preference_name'] : 'غير محدد';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قرار اللجنة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Cairo', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .header {
            background-color: #0077b6;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .card-header {
            font-weight: bold;
        }
        .btn-container {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>قرار اللجنة</h2>
    </div>

    <!-- بيانات الطالب -->
    <div class="card">
        <div class="card-header bg-primary text-white">البيانات الشخصية</div>
        <div class="card-body">
            <p><strong>الاسم:</strong> <?= htmlspecialchars($student['student_name']) ?></p>
            <p><strong>رقم الجلوس:</strong> <?= htmlspecialchars($student['seat_number']) ?></p>
            <p><strong>الرقم القومي:</strong> <?= htmlspecialchars($student['national_id']) ?></p>
        </div>
    </div>

    <!-- قرار اللجنة -->
    <div class="card">
        <div class="card-header bg-success text-white">قرار اللجنة (خاص بالكلية)</div>
        <div class="card-body">
            <p>تم القبول بكلية التربية جامعة دمنهور</p>
            <p>تم قبول الطالب في قسم <?= htmlspecialchars($department) ?></p>
            <p>للعام الجامعي 2025</p>
        </div>
    </div>

    <!-- أعضاء اللجنة -->
    <div class="card">
        <div class="card-header bg-info text-white">أعضاء اللجنة</div>
        <div class="card-body">
            <p>1 - ........................................</p>
            <p>2 - ........................................</p>
            <p>3 - ........................................</p>
        </div>
    </div>

    <!-- أزرار -->
    <div class="btn-container">
        <button class="btn btn-outline-primary" onclick="window.print()">طباعة</button>
        <a href="index.php" class="btn btn-outline-secondary">الصفحة الرئيسية</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
