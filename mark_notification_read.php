<?php
// mark_notification_read.php
// تحديث حالة الإشعار إلى مقروء
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Notification ID required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$id = intval($_POST['id']);

$stmt = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id');
$stmt->execute([':id' => $id, ':user_id' => $user_id]);

echo json_encode(['success' => true]);
