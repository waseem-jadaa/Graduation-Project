<?php
// API للرسائل: جلب وإرسال
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    // جلب الرسائل مع مستخدم معين
    $other_id = intval($_GET['user_id']);
    // تحديث الرسائل المستلمة للطرف الحالي إلى مقروءة
    $conn->prepare('UPDATE message SET seen=1 WHERE receiver_ID = :me AND sender_ID = :other AND seen=0')->execute([':me' => $user_id, ':other' => $other_id]);
    // جلب الرسائل مع اسم المرسل والمرسل إليه
    $stmt = $conn->prepare('
        SELECT m.*, 
               usend.name AS sender_name, usend.User_ID AS sender_id, COALESCE(psend.profile_photo, "image/p.png") AS sender_photo,
               urecv.name AS receiver_name, urecv.User_ID AS receiver_id, COALESCE(precv.profile_photo, "image/p.png") AS receiver_photo
        FROM message m
        JOIN user usend ON m.sender_ID = usend.User_ID
        LEFT JOIN profile psend ON usend.User_ID = psend.User_ID
        JOIN user urecv ON m.receiver_ID = urecv.User_ID
        LEFT JOIN profile precv ON urecv.User_ID = precv.User_ID
        WHERE (m.sender_ID = :me AND m.receiver_ID = :other) OR (m.sender_ID = :other AND m.receiver_ID = :me)
        ORDER BY m.created_at ASC
    ');
    $stmt->execute([':me' => $user_id, ':other' => $other_id]);
    $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($msgs as $msg) {
        $sent_by_me = $msg['sender_ID'] == $user_id;
        $profile_photo = $sent_by_me ? ($msg['sender_photo'] ?: 'image/p.png') : ($msg['sender_photo'] ?: 'image/p.png');
        // في كل الحالات نعرض صورة المرسل
        $result[] = [
            'message' => htmlspecialchars($msg['content']),
            'sent_by_me' => $sent_by_me,
            'time' => date('H:i', strtotime($msg['created_at'])),
            'seen' => (int)$msg['seen'],
            'sender_name' => $msg['sender_name'],
            'receiver_name' => $msg['receiver_name'],
            'sender_id' => $msg['sender_id'],
            'receiver_id' => $msg['receiver_id'],
            'profile_photo' => $msg['sender_photo'] ?: 'image/p.png'
        ];
    }
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['to_user_id'], $_POST['message'])) {
    $to_id = intval($_POST['to_user_id']);
    $msg = trim($_POST['message']);
    if ($to_id && $msg !== '') {
        $stmt = $conn->prepare('INSERT INTO message (sender_ID, receiver_ID, content, created_at, seen) VALUES (:sid, :rid, :msg, NOW(), 0)');
        $stmt->execute([':sid' => $user_id, ':rid' => $to_id, ':msg' => $msg]);
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['recent'])) {
    // جلب آخر المحادثات مع آخر رسالة وبياناتها
    $stmt = $conn->prepare('
        SELECT u.User_ID as user_id, u.name, m.content, m.created_at, m.sender_ID, m.receiver_ID, m.seen,
               COALESCE(p.profile_photo, "image/p.png") as profile_photo
        FROM user u
        LEFT JOIN profile p ON u.User_ID = p.User_ID
        JOIN (
            SELECT *,
                ROW_NUMBER() OVER (PARTITION BY LEAST(sender_ID, receiver_ID), GREATEST(sender_ID, receiver_ID) ORDER BY created_at DESC) as rn
            FROM message
            WHERE sender_ID = :me OR receiver_ID = :me
        ) m ON (u.User_ID = m.sender_ID OR u.User_ID = m.receiver_ID) AND u.User_ID != :me
        WHERE m.rn = 1
        ORDER BY m.created_at DESC
        LIMIT 20
    ');
    $stmt->execute([':me' => $user_id]);
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($chats as $chat) {
        $sent_by_me = $chat['sender_ID'] == $user_id;
        $unread = !$sent_by_me && $chat['seen'] == 0;
        $result[] = [
            'user_id' => $chat['user_id'],
            'name' => $chat['name'],
            'content' => htmlspecialchars($chat['content']),
            'created_at' => $chat['created_at'],
            'sent_by_me' => $sent_by_me,
            'unread' => $unread,
            'seen' => (int)$chat['seen'],
            'profile_photo' => $chat['profile_photo'] ?: 'image/p.png'
        ];
    }
    echo json_encode($result);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['delete_chat'], $_GET['user_id'])) {
    $other_id = intval($_GET['user_id']);
    $stmt = $conn->prepare('DELETE FROM message WHERE (sender_ID = :me AND receiver_ID = :other) OR (sender_ID = :other AND receiver_ID = :me)');
    $stmt->execute([':me' => $user_id, ':other' => $other_id]);
    echo json_encode(['success' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['unread_count'])) {
    // جلب عدد المحادثات التي فيها رسائل غير مقروءة
    $stmt = $conn->prepare('SELECT COUNT(DISTINCT sender_ID) FROM message WHERE receiver_ID = :me AND seen = 0');
    $stmt->execute([':me' => $user_id]);
    $count = $stmt->fetchColumn();
    echo json_encode(['count' => intval($count)]);
    exit;
}

echo json_encode(['error' => 'Invalid request']);
