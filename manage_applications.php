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
    <title>طلبات التقديم على الوظيفة</title>
    <link rel="stylesheet" href="css/project.css">
    <style>
        body {
            background: #f7f9fa;
            font-family: 'Cairo', Tahoma, Arial, sans-serif;
        }
        .main-header {
            background: #1abc5b;
            color: #fff;
            padding: 1.2rem 0 1rem 0;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 1px;
            border-radius: 0 0 18px 18px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px #0001;
        }
        .back-btn {
            display: inline-block;
            background: #fff;
            color: #1abc5b;
            border: 2px solid #1abc5b;
            border-radius: 25px;
            padding: 8px 28px;
            font-size: 1rem;
            font-weight: bold;
            margin: 1rem 0 2rem 0;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #1abc5b;
            color: #fff;
        }
        .requests-table {
            width: 90%;
            margin: 2rem auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px #0002;
            overflow: hidden;
        }
        .requests-table th, .requests-table td {
            padding: 1rem;
            text-align: center;
        }
        .requests-table th {
            background: #f0f0f0;
            color: #1abc5b;
            font-size: 1.1rem;
        }
        .requests-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .requests-table tr:nth-child(odd) {
            background: #fff;
        }
        .action-btn {
            background: #1abc5b;
            color: #fff;
            border: none;
            border-radius: 18px;
            padding: 7px 22px;
            font-size: 1rem;
            font-weight: bold;
            margin: 0 5px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .action-btn:hover {
            background: #159c48;
        }
    </style>
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
                // تحديث الحالة مباشرة بدون إعادة تحميل
                let statusCell = document.getElementById('status-' + appId);
                let actionsCell = document.getElementById('actions-' + appId);
                if(status === 'accepted') {
                    statusCell.innerHTML = '<span style="color:#1abc5b;font-weight:bold;">مقبول</span>';
                    actionsCell.innerHTML = '<span style="color:#1abc5b;font-weight:bold;">تم القبول</span>';
                } else {
                    statusCell.innerHTML = '<span style="color:#e74c3c;font-weight:bold;">مرفوض</span>';
                    actionsCell.innerHTML = '<span style="color:#e74c3c;font-weight:bold;">تم الرفض</span>';
                }
            } else {
                alert('حدث خطأ');
            }
        });
    }
    </script>
</head>
<body>
    <div class="main-header">طلبات التقديم على الوظيفة</div>
    <a href="dashboard.php" class="back-btn">&larr; العودة للصفحة الرئيسية</a>
    <table class="requests-table">
        <tr><th>المتقدم</th><th>الحالة</th><th>إجراءات</th></tr>
        <?php foreach($applications as $app): ?>
        <tr id="row-<?= $app['application_ID'] ?>">
            <td><?= htmlspecialchars($app['name']) ?></td>
            <td id="status-<?= $app['application_ID'] ?>">
                <?php if($app['status'] === 'pending'): ?>
                    <span style="color:#f39c12;font-weight:bold;">قيد الانتظار</span>
                <?php elseif($app['status'] === 'accepted'): ?>
                    <span style="color:#1abc5b;font-weight:bold;">مقبول</span>
                <?php else: ?>
                    <span style="color:#e74c3c;font-weight:bold;">مرفوض</span>
                <?php endif; ?>
            </td>
            <td id="actions-<?= $app['application_ID'] ?>">
                <?php if($app['status'] === 'pending'): ?>
                    <button onclick="respond(<?= $app['application_ID'] ?>, 'accepted')" class="action-btn">قبول</button>
                    <button onclick="respond(<?= $app['application_ID'] ?>, 'rejected')" class="action-btn" style="background:#e74c3c;">رفض</button>
                <?php elseif($app['status'] === 'accepted'): ?>
                    <span style="color:#1abc5b;font-weight:bold;">تم القبول</span>
                <?php else: ?>
                    <span style="color:#e74c3c;font-weight:bold;">تم الرفض</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
