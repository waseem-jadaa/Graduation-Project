<?php
// mark_all_notifications_read.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0');
$stmt->execute([':user_id' => $user_id]);

echo json_encode(['success' => true]); 