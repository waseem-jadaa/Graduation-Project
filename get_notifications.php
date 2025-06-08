<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    SELECT n.id, n.message, n.link, n.is_read, n.created_at, n.sender_id, n.employer_photo,
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

// معالجة صورة الإشعار (صورة صاحب الوظيفة أو المرسل)
foreach ($notifications as $k => &$notif) {
    // إذا كان المرسل هو الأدمن (User_ID 25)، استخدم الصورة الثابتة له
    if ($notif['sender_id'] == 25) {
        $notif['photo'] = 'PG/admin_photo.png';
    } else if (
        strpos($notif['message'], 'تم إغلاق الوظيفة') !== false ||
        strpos($notif['message'], 'تم تحديث تفاصيل الوظيفة') !== false
    ) {
        $notif['photo'] = !empty($notif['employer_photo']) ? $notif['employer_photo'] : 'image/p.png';
    } else {
        // إذا كان الإشعار ليس متعلقاً بوظيفة، استخدم صورة المرسل العادية
        $notif['photo'] = !empty($notif['sender_photo']) ? $notif['sender_photo'] : 'image/p.png';
    }

    // تجاهل الإشعارات التالفة فقط إذا كان sender_id أو sender_name فارغ، ولا تعتمد على الصورة
    if (empty($notif['sender_id']) || empty($notif['sender_name'])) {
        unset($notifications[$k]);
    }
}

// إعادة فهرسة المصفوفة بعد الحذف
$notifications = array_values($notifications);

header('Content-Type: application/json');
echo json_encode($notifications);
