<?php
// get_recent_messages.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {    // Get unread count
    $stmt = $conn->prepare('
        SELECT COUNT(*) 
        FROM message 
        WHERE receiver_ID = :user_id 
        AND seen = 0
    ');
    $stmt->execute([':user_id' => $user_id]);
    $unreadCount = $stmt->fetchColumn();

    // Get latest message from each sender (no duplicate users)
    $stmt = $conn->prepare('
        SELECT m1.message_ID as id,
               m1.sender_ID as from_id,
               m1.content,
               m1.seen as is_read,
               m1.created_at as sent_at,
               u.name as sender_name,
               COALESCE(p.profile_photo, "image/p.png") as sender_photo
        FROM message m1
        JOIN user u ON m1.sender_ID = u.User_ID
        LEFT JOIN profile p ON u.User_ID = p.User_ID
        INNER JOIN (
            SELECT sender_ID, MAX(created_at) as max_created
            FROM message
            WHERE receiver_ID = :user_id
            GROUP BY sender_ID
        ) m2 ON m1.sender_ID = m2.sender_ID AND m1.created_at = m2.max_created
        WHERE m1.receiver_ID = :user_id
        ORDER BY m1.created_at DESC
        LIMIT 10
    ');
    $stmt->execute([':user_id' => $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'unreadCount' => $unreadCount,
        'messages' => array_map(function($msg) {
            return [
                'id' => $msg['id'],
                'from_id' => $msg['from_id'],
                'content' => htmlspecialchars($msg['content']),
                'is_read' => $msg['is_read'],
                'sent_at' => $msg['sent_at'],
                'sender_name' => htmlspecialchars($msg['sender_name']),
                'sender_photo' => $msg['sender_photo']
            ];
        }, $messages)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
