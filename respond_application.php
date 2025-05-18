<?php
// respond_application.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}
$application_id = isset($_POST['application_id']) ? intval($_POST['application_id']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';
if (!$application_id || !in_array($status, ['accepted','rejected'])) {
    echo json_encode(['error' => 'invalid']);
    exit;
}
// جلب بيانات الطلب والمتقدم والوظيفة
$stmt = $conn->prepare('SELECT a.*, j.title, j.employer_ID, u.User_ID as applicant_id FROM application a JOIN job j ON a.job_ID = j.job_ID JOIN user u ON a.user_ID = u.User_ID WHERE a.application_ID = :app_id');
$stmt->execute([':app_id' => $application_id]);
$app = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$app) {
    echo json_encode(['error' => 'not_found']);
    exit;
}
// التأكد أن المستخدم هو صاحب الوظيفة
if ($app['employer_ID'] != $_SESSION['user_id']) {
    echo json_encode(['error' => 'forbidden']);
    exit;
}
// تحديث حالة الطلب
$stmt = $conn->prepare('UPDATE application SET status = :status WHERE application_ID = :app_id');
$stmt->execute([':status' => $status, ':app_id' => $application_id]);
// إرسال إشعار للمتقدم
$msg = $status === 'accepted' ? "تم قبول طلبك لوظيفة: {$app['title']}" : "تم رفض طلبك لوظيفة: {$app['title']}";
$stmt = $conn->prepare('INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (:user_id, :msg, :link, 0, NOW())');
$stmt->execute([
    ':user_id' => $app['applicant_id'],
    ':msg' => $msg,
    ':link' => 'jobs.php'
]);
echo json_encode(['success' => true]);
