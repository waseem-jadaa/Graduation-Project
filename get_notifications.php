<?php
// get_notifications.php
// جلب إشعارات المستخدم الحالي مع بيانات المرسل
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('
    SELECT n.id, n.message, n.link, n.is_read, n.created_at, n.sender_id,
           u.name AS sender_name, p.profile_photo AS sender_photo
    FROM notifications n
    LEFT JOIN user u ON n.sender_id = u.User_ID
    LEFT JOIN profile p ON u.User_ID = p.User_ID
    WHERE n.user_id = :user_id
    ORDER BY n.created_at DESC
    LIMIT 10
');
$stmt->execute([':user_id' => $user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($notifications);
