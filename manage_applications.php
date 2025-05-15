<?php
// صفحة إدارة الطلبات لصاحب العمل لقبول أو رفض الطلبات
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
if (!$job_id) {
    echo 'Job not found';
    exit;
}
// التأكد أن المستخدم هو صاحب الوظيفة
$stmt = $conn->prepare('SELECT * FROM job WHERE job_ID = :job_id AND employer_ID = :user_id');
$stmt->execute([':job_id' => $job_id, ':user_id' => $user_id]);
if (!$stmt->fetch()) {
    echo 'غير مصرح';
    exit;
}
// جلب الطلبات
$stmt = $conn->prepare('SELECT a.application_ID, a.status, u.User_ID, u.name FROM application a JOIN user u ON a.user_ID = u.User_ID WHERE a.job_ID = :job_id');
$stmt->execute([':job_id' => $job_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة طلبات الوظيفة</title>
    <link rel="stylesheet" href="project.css">
    <script>
    function respond(appId, status) {
        fetch('respond_application.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'application_id=' + appId + '&status=' + status
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('تم تحديث حالة الطلب');
                location.reload();
            } else {
                alert('حدث خطأ');
            }
        });
    }
    </script>
</head>
<body>
    <h2>طلبات التقديم على الوظيفة</h2>
    <table border="1" style="width:80%;margin:auto;text-align:center;">
        <tr><th>المتقدم</th><th>الحالة</th><th>إجراءات</th></tr>
        <?php foreach($applications as $app): ?>
        <tr>
            <td><?= htmlspecialchars($app['name']) ?></td>
            <td><?= $app['status'] ?></td>
            <td>
                <?php if($app['status'] === 'pending'): ?>
                    <button onclick="respond(<?= $app['application_ID'] ?>, 'accepted')">قبول</button>
                    <button onclick="respond(<?= $app['application_ID'] ?>, 'rejected')">رفض</button>
                <?php else: ?>
                    ---
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
