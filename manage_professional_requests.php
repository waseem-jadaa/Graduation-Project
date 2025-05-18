<?php
// صفحة إدارة طلبات المهنيين للمهني لقبول أو رفض الطلب
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// جلب جميع الطلبات (ليس فقط pending)
$stmt = $conn->prepare('SELECT a.application_ID, a.status, a.employer_id, u.name as employer_name FROM application a JOIN user u ON a.employer_id = u.User_ID WHERE a.job_ID IS NULL AND a.user_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات أصحاب العمل</title>
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/manage_prof_req_.css">
    <style>
        /* تم نقل جميع الأكواد إلى manage_prof_req_.css */
    </style>
</head>
<body>
    <div class="main-header">طلبات أصحاب العمل</div>
    <a href="dashboard.php" class="back-btn">&larr; العودة للصفحة الرئيسية</a>
    <table class="requests-table">
        <tr><th>صاحب العمل</th><th>الحالة</th><th>إجراءات</th></tr>
        <?php foreach($requests as $req): ?>
        <tr>
            <td><?= htmlspecialchars($req['employer_name']) ?></td>
            <td>
                <?php if($req['status'] === 'pending'): ?>
                    <span style="color:#f39c12;font-weight:bold;">قيد الانتظار</span>
                <?php elseif($req['status'] === 'accepted'): ?>
                    <span style="color:#1abc5b;font-weight:bold;">مقبول</span>
                <?php else: ?>
                    <span style="color:#e74c3c;font-weight:bold;">مرفوض</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if($req['status'] === 'pending'): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="request_id" value="<?= $req['application_ID'] ?>">
                        <button name="action" value="accept" class="action-btn">قبول</button>
                        <button name="action" value="reject" class="action-btn" style="background:#e74c3c;">رفض</button>
                    </form>
                <?php elseif($req['status'] === 'accepted'): ?>
                    <span style="color:#1abc5b;font-weight:bold;">تم القبول</span>
                <?php else: ?>
                    <span style="color:#e74c3c;font-weight:bold;">تم الرفض</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php
    // معالجة القبول أو الرفض
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action'] === 'accept' ? 'accepted' : 'rejected';
        // تحديث الطلب
        $stmt = $conn->prepare('UPDATE application SET status = :status WHERE application_ID = :id AND user_ID = :user_id');
        $stmt->execute([':status' => $action, ':id' => $request_id, ':user_id' => $user_id]);
        // جلب صاحب العمل
        $stmt = $conn->prepare('SELECT employer_id FROM application WHERE application_ID = :id');
        $stmt->execute([':id' => $request_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $employer_id = $row['employer_id'];
            $msg = $action === 'accepted' ? "تم قبول طلبك من المهني." : "تم رفض طلبك من المهني.";
            $stmt = $conn->prepare('INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (:user_id, :message, "", 0, NOW())');
            $stmt->execute([':user_id' => $employer_id, ':message' => $msg]);
        }
        echo '<script>alert("تم تحديث حالة الطلب.");window.location.href=window.location.pathname;</script>';
    }
    ?>
</body>
</html>
