<?php
session_start();
require 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

$student = null;
$grades = [];
$guardian = null;
$preferences = [];

$stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
$stmt->execute([$student_id]);
$grades = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM guardians WHERE student_id = ?");
$stmt->execute([$student_id]);
$guardian = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM preferences WHERE student_id = ? ORDER BY preference_order ASC");
$stmt->execute([$student_id]);
$preferences = $stmt->fetchAll();

$total_score = 0;
foreach ($grades as $grade) {
    $total_score += $grade['grade'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بيانات الطالب</title>
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

        .btn-container {
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .student-info, .guardian-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .student-info div, .guardian-info div {
            flex: 1 1 30%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #0077b6;
            margin: 10px 0;
            text-align: center;
        }

        .grades-table table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .grades-table th, .grades-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        .grades-table th {
            background-color: #0077b6;
            color: white;
        }

        .total-score {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .card-header {
            font-weight: bold;
        }

        .nav-bar {
            background-color: #0077b6;
        }

        .nav-bar .nav-link {
            color: white !important;
        }

        .btn {
            min-width: 180px;
            margin: 5px;
        }
    </style>
</head>
<body class="bg-light p-4">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark nav-bar">
    <div class="container">
        <a class="navbar-brand" href="#">تنسيق كلية الآداب - جامعة دمنهور</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="تبديل التنقل">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a href="index.php" class="nav-link">تسجيل خروج</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- الصفحة الرئيسية -->
<div class="container">
    <div class="header">
        <h2>بيانات الطالب</h2>
    </div>

    <div class="btn-container d-flex justify-content-center gap-2 flex-wrap">
        <a href="edit.php?student_id=<?= $student['student_id'] ?>" class="btn btn-outline-primary">تعديل البيانات</a>
        <a href="supply_order.php?student_id=<?= $student['student_id'] ?>" class="btn btn-outline-info">أيصال الدفع</a>
        <a href="upload_documents.php" class="btn btn-outline-warning">رفع المستندات</a> 
        <a href="required_documents.php" class="btn btn-outline-success">الأوراق المطلوبة</a>
        <a href="committee decision.php?student_id=<?= $student['student_id'] ?>" class="btn btn-outline-dark">قرار اللجنة</a>
    </div>

    <?php if ($student): ?>
        <!-- بيانات الطالب -->
        <div class="card">
            <div class="card-header bg-primary text-white">بيانات الطالب</div>
            <div class="card-body student-info">
                <div>
                    <p><strong>الاسم:</strong> <?= $student['student_name'] ?></p>
                    <p><strong>رقم الجلوس:</strong> <?= $student['seat_number'] ?></p>
                </div>
                <div>
                    <p><strong>الرقم القومي:</strong> <?= $student['national_id'] ?></p>
                    <p><strong>الهاتف:</strong> <?= $student['phone'] ?></p>
                </div>
                <div>
                    <p><strong>العنوان:</strong> <?= $student['address'] ?></p>
                </div>
            </div>
        </div>

        <!-- بيانات ولي الأمر -->
        <?php if ($guardian): ?>
            <div class="card">
                <div class="card-header bg-success text-white">بيانات ولي الأمر</div>
                <div class="card-body guardian-info">
                    <div><p><strong>الاسم:</strong> <?= $guardian['guardian_name'] ?></p></div>
                    <div><p><strong>الوظيفة:</strong> <?= $guardian['guardian_job'] ?></p></div>
                    <div><p><strong>الرقم القومي:</strong> <?= $guardian['guardian_national_id'] ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- الدرجات -->
        <div class="card">
            <div class="card-header bg-info text-white">درجات الطالب</div>
            <div class="card-body grades-table">
                <table>
                    <thead>
                        <tr>
                            <th>المادة</th>
                            <th>الدرجة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $g): ?>
                            <tr>
                                <td><?= $g['subject_name'] ?></td>
                                <td><?= $g['grade'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- مجموع الدرجات -->
        <div class="total-score">
            <p>مجموع الدرجات: <?= $total_score ?></p>
        </div>

        <!-- الرغبة الأولى فقط -->
        <div class="card">
            <div class="card-header bg-warning text-dark">الرغبة الأولى</div>
            <div class="card-body">
                <ol>
                    <?php if (!empty($preferences)): ?>
                        <li><?= $preferences[0]['preference_name'] ?></li>
                    <?php else: ?>
                        <li>لا توجد رغبات مسجلة</li>
                    <?php endif; ?>
                </ol>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- إضافة زر الطباعه -->
<div class="text-center">
    <button class="btn btn-success" onclick="window.print()">طباعة البيانات</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
