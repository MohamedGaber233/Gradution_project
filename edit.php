<?php
require 'db.php';

if (!isset($_GET['student_id'])) {
    die('رقم الطالب غير موجود.');
}

$student_id = $_GET['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تحديث بيانات الطالب
    $stmt = $pdo->prepare("UPDATE students SET student_name=?, national_id=?, seat_number=?, phone=?, address=? WHERE student_id=?");
    $stmt->execute([
        $_POST['student_name'],
        $_POST['national_id'],
        $_POST['seat_number'],
        $_POST['phone'],
        $_POST['address'],
        $student_id
    ]);

    // تحديث بيانات ولي الأمر
    $stmt = $pdo->prepare("UPDATE guardians SET guardian_name=?, guardian_job=?, guardian_national_id=?, guardian_phone=?, guardian_address=? WHERE student_id=?");
    $stmt->execute([
        $_POST['guardian_name'],
        $_POST['guardian_job'],
        $_POST['guardian_national_id'],
        $_POST['guardian_phone'],
        $_POST['guardian_address'],
        $student_id
    ]);

    // تحديث الدرجات
    if (isset($_POST['subject_name']) && isset($_POST['grade'])) {
        foreach ($_POST['subject_name'] as $index => $subject) {
            $grade = $_POST['grade'][$index];
            $grade_id = $_POST['grade_id'][$index];

            $stmt = $pdo->prepare("UPDATE grades SET subject_name = ?, grade = ? WHERE grade_id = ? AND student_id = ?");
            $stmt->execute([$subject, $grade, $grade_id, $student_id]);
        }
    }

    // تحديث الرغبات
    if (isset($_POST['preference_name']) && isset($_POST['preference_order'])) {
        foreach ($_POST['preference_name'] as $index => $pref) {
            $order = $_POST['preference_order'][$index];
            $pref_id = $_POST['preference_id'][$index];

            $stmt = $pdo->prepare("UPDATE preferences SET preference_name = ?, preference_order = ? WHERE preference_id = ? AND student_id = ?");
            $stmt->execute([$pref, $order, $pref_id, $student_id]);
        }
    }

    // إعادة التوجيه إلى الصفحة الرئيسية
    header("Location: index.php");
    exit;
}

// جلب البيانات الحالية
$student = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
$student->execute([$student_id]);
$student = $student->fetch();

$guardian = $pdo->prepare("SELECT * FROM guardians WHERE student_id = ?");
$guardian->execute([$student_id]);
$guardian = $guardian->fetch();

$grades = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
$grades->execute([$student_id]);
$grades = $grades->fetchAll();

$preferences = $pdo->prepare("SELECT * FROM preferences WHERE student_id = ? ORDER BY preference_order ASC");
$preferences->execute([$student_id]);
$preferences = $preferences->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل بيانات الطالب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 1000px;
        }
        .card-header {
            font-weight: bold;
            background-color: #007bff;
            color: #fff;
        }
        .form-control {
            border-radius: 8px;
        }
        .btn {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .card-body {
            background-color: #fff;
            padding: 1.5rem;
        }
        .section-header {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            font-size: 1.2rem;
        }
        .form-label {
            font-weight: bold;
        }
        .mb-3 input {
            border-radius: 8px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .btn-container .btn {
            width: 48%;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">تعديل بيانات الطالب</h2>

    <form method="POST">
        <div class="card mb-4 shadow-sm">
            <div class="card-header">بيانات الطالب</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="student_name" class="form-label">الاسم</label>
                    <input type="text" id="student_name" name="student_name" class="form-control" value="<?= htmlspecialchars($student['student_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="national_id" class="form-label">الرقم القومي</label>
                    <input type="text" id="national_id" name="national_id" class="form-control" value="<?= htmlspecialchars($student['national_id']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="seat_number" class="form-label">رقم الجلوس</label>
                    <input type="text" id="seat_number" name="seat_number" class="form-control" value="<?= htmlspecialchars($student['seat_number']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">العنوان</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($student['address']) ?>">
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="section-header">بيانات ولي الأمر</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="guardian_name" class="form-label">اسم ولي الأمر</label>
                    <input type="text" id="guardian_name" name="guardian_name" class="form-control" value="<?= htmlspecialchars($guardian['guardian_name']) ?>">
                </div>
                <div class="mb-3">
                    <label for="guardian_job" class="form-label">الوظيفة</label>
                    <input type="text" id="guardian_job" name="guardian_job" class="form-control" value="<?= htmlspecialchars($guardian['guardian_job']) ?>">
                </div>
                <div class="mb-3">
                    <label for="guardian_national_id" class="form-label">الرقم القومي</label>
                    <input type="text" id="guardian_national_id" name="guardian_national_id" class="form-control" value="<?= htmlspecialchars($guardian['guardian_national_id']) ?>">
                </div>
                <div class="mb-3">
                    <label for="guardian_phone" class="form-label">رقم الهاتف</label>
                    <input type="text" id="guardian_phone" name="guardian_phone" class="form-control" value="<?= htmlspecialchars($guardian['guardian_phone']) ?>">
                </div>
                <div class="mb-3">
                    <label for="guardian_address" class="form-label">العنوان</label>
                    <input type="text" id="guardian_address" name="guardian_address" class="form-control" value="<?= htmlspecialchars($guardian['guardian_address']) ?>">
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="section-header">درجات الطالب</div>
            <div class="card-body">
                <?php foreach ($grades as $g): ?>
                    <div class="mb-3">
                        <label for="subject_name[]" class="form-label">اسم المادة</label>
                        <input type="text" name="subject_name[]" class="form-control" value="<?= htmlspecialchars($g['subject_name']) ?>" placeholder="اسم المادة">
                    </div>
                    <div class="mb-3">
                        <label for="grade[]" class="form-label">الدرجة</label>
                        <input type="text" name="grade[]" class="form-control" value="<?= htmlspecialchars($g['grade']) ?>" placeholder="الدرجة">
                    </div>
                    <input type="hidden" name="grade_id[]" value="<?= $g['grade_id'] ?>">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="section-header">الرغبات المختارة</div>
            <div class="card-body">
                <?php foreach ($preferences as $p): ?>
                    <div class="mb-3">
                        <label for="preference_name[]" class="form-label">اسم الرغبة</label>
                        <input type="text" name="preference_name[]" class="form-control" value="<?= htmlspecialchars($p['preference_name']) ?>" placeholder="اسم الرغبة">
                    </div>
                    <div class="mb-3">
                        <label for="preference_order[]" class="form-label">الترتيب</label>
                        <input type="number" name="preference_order[]" class="form-control" value="<?= htmlspecialchars($p['preference_order']) ?>" placeholder="الترتيب">
                    </div>
                    <input type="hidden" name="preference_id[]" value="<?= $p['preference_id'] ?>">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="btn-container">
            <button class="btn btn-primary">حفظ التعديلات</button>
            <a href="student_data.php" class="btn btn-secondary">رجوع</a>
        </div>
    </form>
</div>
</body>
</html>
