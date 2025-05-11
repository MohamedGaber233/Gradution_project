<?php
require 'db.php';
$student_id = $_GET['student_id'];

// تحديد الرغبات مع الحد الأدنى للدرجة
$available_prefs = [
    'اللغة العربية' => ['subject' => 'اللغة العربية', 'min_grade' => 70],
    'التاريخ' => ['subject' => 'التاريخ', 'min_grade' => 65],
    'الاجتماع' => ['subject' => 'الاجتماع', 'min_grade' => 60],
    'الإعلام' => ['subject' => 'الإعلام', 'min_grade' => 70],
    'الجغرافيا' => ['subject' => 'الجغرافيا', 'min_grade' => 60],
    'اللغات الشرقية' => ['subject' => 'اللغات الشرقية', 'min_grade' => 75],
    'الفرنسية' => ['subject' => 'الفرنسية', 'min_grade' => 75]
];

// جلب درجات الطالب من قاعدة البيانات
$grades = [];
$stmt = $pdo->prepare("SELECT subject_name, grade FROM grades WHERE student_id = ?");
$stmt->execute([$student_id]);
while ($row = $stmt->fetch()) {
    $grades[$row['subject_name']] = $row['grade'];
}

// معالجة حالة كل رغبة
$final_prefs = [];

foreach ($available_prefs as $pref_name => $data) {
    $subject = $data['subject'];
    $min_grade = $data['min_grade'];
    $student_grade = $grades[$subject] ?? 0;

    // تحقق من عدد الطلاب الذين اختاروا هذه الرغبة كرغبة أولى
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM preferences WHERE preference_order = 1 AND preference_name = ?");
    $stmt->execute([$pref_name]);
    $count = $stmt->fetchColumn();

    $is_full = $count >= 5;
    $meets_grade = $student_grade >= $min_grade;

    $final_prefs[] = [
        'name' => $pref_name,
        'subject' => $subject,
        'grade' => $student_grade,
        'min' => $min_grade,
        'is_full' => $is_full,
        'is_eligible' => $meets_grade
    ];
}

// ترتيب الرغبات
usort($final_prefs, function ($a, $b) {
    if ($a['is_eligible'] && !$a['is_full']) return -1;
    if ($b['is_eligible'] && !$b['is_full']) return 1;
    if ($a['is_eligible'] && $a['is_full']) return -1;
    if ($b['is_eligible'] && $b['is_full']) return 1;
    return 0;
});

// حفظ الرغبات وتحديث created_at
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM preferences WHERE student_id = ?");
    $stmt->execute([$student_id]);

    foreach ($_POST['preference'] as $i => $pref) {
        $stmt = $pdo->prepare("INSERT INTO preferences (student_id, preference_order, preference_name)
                               VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $i + 1, $pref]);
    }

    // تحديث تاريخ التسجيل
    $stmt = $pdo->prepare("UPDATE students SET created_at = NOW() WHERE student_id = ?");
    $stmt->execute([$student_id]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>اختيار الرغبات - كلية الآداب</title>
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

        #sortable li {
            margin: 5px;
            padding: 10px;
            background: #eee;
            border-radius: 8px;
            cursor: move;
        }

        .btn-primary {
            background: linear-gradient(to right, #0077b6, #00b4d8);
            border: none;
            padding: 12px;
            width: 100%;
        }

        .text-link {
            display: block;
            margin-top: 15px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="image/download.jpeg" alt="شعار الكلية" class="logo">
    <h4 class="m-0">برنامج التنسيق الإلكتروني - كلية الآداب - جامعة دمنهور</h4>
</div>

<div class="form-container">
    <div class="form-box">
        <h3>اختيار الرغبات (اسحب لترتيبها)</h3>
        <form method="POST">
            <ul id="sortable" style="list-style:none; padding:0;">
                <?php foreach ($final_prefs as $pref): ?>
                    <?php
                        $style = '';
                        $note = '';

                        if (!$pref['is_eligible']) {
                            $style = 'background: #f8d7da; color: #721c24;';
                            $note = 'غير مؤهل بسبب الدرجة (المطلوبة: ' . $pref['min'] . ')';
                        } elseif ($pref['is_full']) {
                            $style = 'background: #d6d6d6; color: #555;';
                            $note = 'تم إغلاق القسم بسبب اكتمال العدد';
                        }
                    ?>
                    <li class="ui-state-default" style="<?= $style ?>">
                        <input type="hidden" name="preference[]" value="<?= $pref['name'] ?>"
                            <?= (!$pref['is_eligible'] || $pref['is_full']) ? 'disabled' : '' ?>>
                        <?= $pref['name'] ?>
                        <?php if ($note): ?>
                            <small style="display:block; font-size:12px;"><?= $note ?></small>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button type="submit" class="btn btn-primary">حفظ الرغبات</button>
        </form>
        <a href="index.php" class="text-link">الرجوع إلى الصفحة الرئيسية</a>
    </div>
</div>

<!-- jQuery و jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(function () {
        $("#sortable").sortable();
        $("#sortable").disableSelection();
    });
</script>

</body>
</html>
