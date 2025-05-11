<?php
require 'db.php';
$student_id = $_GET['student_id'];

// المواد المطلوبة مسبقاً + الدرجة الدنيا المطلوبة لكل مادة
$subjects = [
    'اللغة العربية' => 70,
    'التاريخ' => 65,
    'الجغرافيا' => 60,
    'الاجتماع' => 60,
    'الإعلام' => 70,
    'اللغات الشرقية' => 75,
    'الفرنسية' => 75
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($subjects as $subject => $min_grade) {
        $grade = $_POST['grade'][$subject] ?? 0;
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_name, grade) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $subject, $grade]);
    }
    header("Location: add_guardian.php?student_id=$student_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدخال الدرجات - كلية الآداب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #0077b6, #ffffff);
            min-height: 100vh;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
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
        <h3>إدخال الدرجات</h3>
        <form method="POST">
            <?php foreach ($subjects as $subject => $min): ?>
                <div class="form-group">
                    <label class="form-label"><?= $subject ?> (الحد الأدنى: <?= $min ?> درجة)</label>
                    <input type="number" name="grade[<?= $subject ?>]" class="form-control" min="0" max="100" required>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary w-100">التالي</button>
        </form>
        <a href="index.php" class="text-link">الرجوع إلى الصفحة الرئيسية</a>
    </div>
</div>

</body>
</html>
