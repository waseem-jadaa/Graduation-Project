<?php
// صفحة الرسائل الرئيسية
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// جلب جميع المستخدمين مع الصورة الشخصية باستثناء المستخدم الحالي
$stmt = $conn->prepare('
    SELECT u.User_ID, u.name, COALESCE(p.profile_photo, "image/p.png") as profile_photo
    FROM user u
    LEFT JOIN profile p ON u.User_ID = p.User_ID
    WHERE u.User_ID != :uid AND u.role != "admin" AND u.name != "Admin"
');
$stmt->execute([':uid' => $user_id]);
$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>    <meta charset="UTF-8">
    <title>الرسائل | FursaPal</title>
    <link rel="stylesheet" href="css/messages.css">
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/messages-dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'headerDash.php'; ?>
<div class="messages-container">
    <div class="chats-list" id="chatsList">        <div class="chats-list-header">
            <span>المحادثات</span>
            <button id="toggleUsersBtn" title="إظهار/إخفاء المستخدمين"><i class="fas fa-users"></i></button>
        </div>
        <ul></ul>
    </div>
    <div class="users-list" id="usersList" style="display:none;">
        <div class="users-list-header">المستخدمون</div>
        <input type="text" id="userSearch" placeholder="ابحث عن مستخدم...">
        <ul>
            <?php foreach($all_users as $user): ?>
                <li data-user-id="<?= $user['User_ID'] ?>">
                    <img src="<?= htmlspecialchars($user['profile_photo'] ?? 'image/p.png') ?>" alt="avatar" class="msg-avatar" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #eee;box-shadow:0 1px 4px #0001;margin-left:6px;">
                    <span><?= htmlspecialchars($user['name']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="chat-section">
        <div class="chat-header" id="chatHeader">اختر مستخدم لبدء المحادثة</div>
        <div class="chat-messages" id="chatMessages"></div>
        <form class="chat-input" id="chatForm" style="display:none;">
            <input type="text" id="messageInput" placeholder="اكتب رسالتك..." autocomplete="off">
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>    </div>
</div>
<script src="js/messages.js"></script>
<script src="js/messages-dropdown.js"></script>
</body>
</html>
