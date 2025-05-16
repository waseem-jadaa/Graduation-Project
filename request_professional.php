<?php
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$role = null;
$stmt = $conn->prepare('SELECT role FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) $role = $row['role'];
if ($role !== 'employer') {
    echo json_encode(['error' => 'forbidden']);
    exit;
}
$professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;
if (!$professional_id || $professional_id == $user_id) {
    echo json_encode(['error' => 'invalid_professional']);
    exit;
}
// تحقق من عدم وجود طلب سابق
$stmt = $conn->prepare('SELECT * FROM application WHERE job_ID IS NULL AND user_ID = :professional_id AND status = "pending" AND employer_id = :employer_id');
$stmt->execute([':professional_id' => $professional_id, ':employer_id' => $user_id]);
if ($stmt->fetch()) {
    echo json_encode(['error' => 'already_requested']);
    exit;
}
// إضافة الطلب (job_ID = NULL, user_ID = المهني, employer_id = employer)
$stmt = $conn->prepare('INSERT INTO application (job_ID, user_ID, status, employer_id) VALUES (NULL, :professional_id, "pending", :employer_id)');
$stmt->execute([':professional_id' => $professional_id, ':employer_id' => $user_id]);
// إرسال إشعار للمهني
$msg = "لديك طلب جديد من صاحب عمل. يمكنك قبول أو رفض الطلب.";
$link = "manage_professional_requests.php";
$stmt = $conn->prepare('INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (:user_id, :message, :link, 0, NOW())');
$stmt->execute([':user_id' => $professional_id, ':message' => $msg, ':link' => $link]);
echo json_encode(['success' => true]);
