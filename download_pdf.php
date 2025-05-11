<?php
require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';
require 'db.php';

// استقبال البيانات من الرابط
$national_id = $_GET['national_id'] ?? null;
$seat_number = $_GET['seat_number'] ?? null;

if (!$national_id || !$seat_number) {
    die("الرقم القومي أو رقم الجلوس غير موجودين.");
}

// جلب بيانات الطالب
$stmt = $pdo->prepare("SELECT * FROM students WHERE national_id = ? AND seat_number = ?");
$stmt->execute([$national_id, $seat_number]);
$student = $stmt->fetch();

if (!$student) {
    die("الطالب غير موجود.");
}

// جلب درجات الطالب
$stmt = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
$stmt->execute([$student['student_id']]);
$grades = $stmt->fetchAll();

// جلب بيانات ولي الأمر
$stmt = $pdo->prepare("SELECT * FROM guardians WHERE student_id = ?");
$stmt->execute([$student['student_id']]);
$guardian = $stmt->fetch();

// جلب الرغبات
$stmt = $pdo->prepare("SELECT * FROM preferences WHERE student_id = ? ORDER BY preference_order ASC");
$stmt->execute([$student['student_id']]);
$preferences = $stmt->fetchAll();

// إنشاء ملف PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('نظام التنسيق');
$pdf->SetTitle('بيانات الطالب');
$pdf->SetHeaderData('', 0, '', '');
$pdf->setHeaderFont(Array('helvetica', '', 10));
$pdf->setFooterFont(Array('helvetica', '', 8));
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// إضافة الخط العربي
$pdf->AddFont('amiri', '', 'amiri.php');
$pdf->SetFont('amiri', '', 14, '', true);
$pdf->AddPage();

// محتوى PDF
$html = '<h2 align="center">بيانات الطالب</h2>';
$html .= "<p><strong>الاسم:</strong> {$student['student_name']}</p>";
$html .= "<p><strong>الرقم القومي:</strong> {$student['national_id']}</p>";
$html .= "<p><strong>رقم الجلوس:</strong> {$student['seat_number']}</p>";
$html .= "<p><strong>رقم الهاتف:</strong> {$student['phone']}</p>";
$html .= "<p><strong>العنوان:</strong> {$student['address']}</p>";

if ($guardian) {
    $html .= '<h3>بيانات ولي الأمر</h3>';
    $html .= "<p><strong>الاسم:</strong> {$guardian['guardian_name']}</p>";
    $html .= "<p><strong>الوظيفة:</strong> {$guardian['guardian_job']}</p>";
    $html .= "<p><strong>الرقم القومي:</strong> {$guardian['guardian_national_id']}</p>";
    $html .= "<p><strong>رقم الهاتف:</strong> {$guardian['guardian_phone']}</p>";
    $html .= "<p><strong>العنوان:</strong> {$guardian['guardian_address']}</p>";
}

$html .= '<h3>الدرجات</h3><ul>';
foreach ($grades as $g) {
    $html .= "<li>{$g['subject_name']}: {$g['grade']}</li>";
}
$html .= '</ul>';

$html .= '<h3>الرغبات</h3><ol>';
foreach ($preferences as $p) {
    $html .= "<li>{$p['preference_name']}</li>";
}
$html .= '</ol>';

// إخراج الملف
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('student_data.pdf', 'I');
?>
