<?php
// لوحة تحكم الأدمن لمراجعة طلبات التوثيق
include 'db.php';
session_start();
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: main.php');
    exit();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// تحقق أن المستخدم أدمن
$stmt = $conn->prepare('SELECT role FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$role = $stmt->fetchColumn();
if ($role !== 'admin') {
    die('غير مصرح');
}
// معالجة القبول/الرفض
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_id'], $_POST['action'])) {
    $verification_id = intval($_POST['verification_id']);
    $action = $_POST['action'];
    $note = $_POST['note'] ?? '';
    $stmt = $conn->prepare('SELECT user_id FROM user_verification WHERE id = :id');
    $stmt->execute([':id' => $verification_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $uid = $row['user_id'];
        if ($action === 'accept') {
            $conn->beginTransaction();
            $conn->prepare('UPDATE user_verification SET status = "accepted", reviewed_at = NOW() WHERE id = :id')->execute([':id' => $verification_id]);
            $conn->prepare('UPDATE user SET verification_status = "verified", verification_note = NULL WHERE User_ID = :uid')->execute([':uid' => $uid]);
            $conn->commit();
        } elseif ($action === 'reject') {
            $conn->beginTransaction();
            $conn->prepare('UPDATE user_verification SET status = "rejected", reviewed_at = NOW(), note = :note WHERE id = :id')->execute([':id' => $verification_id, ':note' => $note]);
            $conn->prepare('UPDATE user SET verification_status = "rejected", verification_note = :note WHERE User_ID = :uid')->execute([':uid' => $uid, ':note' => $note]);
            // إرسال إشعار داخلي للمستخدم
            $notif_msg = 'تم رفض طلب توثيق حسابك.';
            if (!empty($note)) {
                $notif_msg .= ' سبب الرفض: ' . $note;
            }
            $conn->prepare('INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (:user_id, :message, 0, NOW())')->execute([
                ':user_id' => $uid,
                ':message' => $notif_msg
            ]);
            $conn->commit();
        }
    }
}
// جلب الطلبات قيد المراجعة
$stmt = $conn->prepare('SELECT v.*, u.name, u.email FROM user_verification v JOIN user u ON v.user_id = u.User_ID WHERE v.status = "pending" ORDER BY v.created_at ASC');
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مراجعة توثيقات المستخدمين</title>
    <link rel="stylesheet" href="css/project.css">
    <style>
        .admin-table { width:100%; border-collapse:collapse; margin:30px auto; background:#fff; }
        .admin-table th, .admin-table td { border:1px solid #ddd; padding:10px; text-align:center; }
        .admin-table th { background:#1abc5b; color:#fff; }
        .admin-table tr:nth-child(even) { background:#f7f7f7; }
        .admin-actions button { margin:0 5px; padding:6px 18px; border-radius:5px; border:none; font-weight:bold; }
        .admin-actions .accept { background:#1abc5b; color:#fff; }
        .admin-actions .reject { background:#e74c3c; color:#fff; }
        .note-input { width:90%; padding:4px; }
        .logout-btn { display:block; margin:30px auto 10px auto; background:#e74c3c; color:#fff; border:none; padding:10px 30px; border-radius:6px; font-size:1.1em; font-weight:bold; cursor:pointer; text-decoration:none; text-align:center; }
        .logout-btn:hover { background:#c0392b; }
    </style>
</head>
<body style="background:#f5f5f5;">
    <a href="?logout=1" class="logout-btn">تسجيل الخروج</a>
    <div style="max-width:1100px;margin:40px auto;">
        <h2 style="text-align:center;">طلبات التوثيق قيد المراجعة</h2>
        <table class="admin-table">
            <tr>
                <th>المستخدم</th>
                <th>البريد الإلكتروني</th>
                <th>الاسم القانوني</th>
                <th>نوع الوثيقة</th>
                <th>الوثيقة</th>
                <th>تاريخ الطلب</th>
                <th>ملاحظات الرفض</th>
                <th>إجراءات</th>
            </tr>
            <?php foreach($requests as $req): ?>
            <tr>
                <td><?php echo htmlspecialchars($req['name']); ?></td>
                <td><?php echo htmlspecialchars($req['email']); ?></td>
                <td><?php echo htmlspecialchars($req['legal_name']); ?></td>
                <td><?php echo htmlspecialchars($req['doc_type']); ?></td>
                <td><a href="<?php echo $req['doc_path']; ?>" target="_blank">عرض الوثيقة</a></td>
                <td><?php echo htmlspecialchars($req['created_at']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="verification_id" value="<?php echo $req['id']; ?>">
                        <input type="text" name="note" class="note-input" placeholder="سبب الرفض (اختياري)">
                        <button type="submit" name="action" value="accept" class="accept">قبول</button>
                        <button type="submit" name="action" value="reject" class="reject">رفض</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php if (empty($requests)): ?>
            <div style="text-align:center;color:#888;">لا توجد طلبات قيد المراجعة حالياً.</div>
        <?php endif; ?>
    </div>
</body>
</html>
