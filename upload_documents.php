<?php
session_start();
require 'db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// استعلام للحصول على تاريخ تسجيل الطالب
$stmt = $pdo->prepare("SELECT created_at FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$created_at = $stmt->fetchColumn();

$show_delete_alert = false;
if ($created_at) {
    $created_time = new DateTime($created_at);
    $now = new DateTime();
    $interval = $created_time->diff($now)->days;

    $checkUploads = $pdo->prepare("SELECT COUNT(*) FROM uploaded_documents WHERE student_id = ?");
    $checkUploads->execute([$student_id]);
    $hasUploads = $checkUploads->fetchColumn();

    if ($interval > 2 && $hasUploads == 0) {
        $tables = ['uploaded_documents', 'preferences', 'grades', 'students'];
        foreach ($tables as $table) {
            $del = $pdo->prepare("DELETE FROM $table WHERE student_id = ?");
            $del->execute([$student_id]);
        }
        session_destroy();
        $show_delete_alert = true;
    }
}

$success = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
    $uploads_dir = 'uploads/';

    $documents = [
        'payment_receipt' => 'إيصال الدفع',
        'committee_decision' => 'قرار اللجنة'
    ];

    foreach ($documents as $key => $label) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] == UPLOAD_ERR_OK) {
            $file_name = basename($_FILES[$key]['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $errors[] = "صيغة $label غير مدعومة.";
            } else {
                $unique_name = uniqid() . "_" . $file_name;
                move_uploaded_file($_FILES[$key]['tmp_name'], $uploads_dir . $unique_name);

                $stmt = $pdo->prepare("INSERT INTO uploaded_documents (student_id, document_type, file_name)
                                       VALUES (?, ?, ?)");
                $stmt->execute([$student_id, $key, $unique_name]);
            }
        } else {
            $errors[] = "يرجى رفع ملف $label.";
        }
    }

    if (empty($errors)) {
        $success = "تم رفع الملفات بنجاح.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رفع المستندات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body { background-color: #f9f9f9; font-family: 'Cairo', sans-serif; }
        .container { margin-top: 50px; }
        .header { background-color: #0077b6; color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .upload-form { background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .upload-form h3 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-control { direction: ltr; }
        .alert { margin-top: 20px; }
        .btn-submit { width: 100%; font-size: 18px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0077b6;">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="image/logo-univ.png" alt="شعار الجامعة" class="img-fluid">
            تنسيق كلية الآداب - جامعة دمنهور
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">الرئيسية</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php">تسجيل خروج</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?php if ($show_delete_alert): ?>
        <div class="alert alert-danger text-center">
            لقد تجاوزت المدة المسموح بها (يومان) بدون رفع المستندات، وتم حذف بياناتك تلقائيًا.<br>
            يرجى إعادة التسجيل من جديد.
        </div>
        <script>
            setTimeout(() => { window.location.href = 'login.php'; }, 5000);
        </script>
    <?php else: ?>
        <div class="header"><h2>رفع المستندات المطلوبة</h2></div>

        <div class="upload-form">
            <h3>الرجاء رفع المستندات التالية</h3>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endforeach; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="payment_receipt">إيصال الدفع</label>
                    <input type="file" name="payment_receipt" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="committee_decision">قرار اللجنة</label>
                    <input type="file" name="committee_decision" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-submit">رفع المستندات</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
