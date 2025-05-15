<?php
// get_notifications.php
// جلب إشعارات المستخدم الحالي
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 10');
$stmt->execute([':user_id' => $user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($notifications);
