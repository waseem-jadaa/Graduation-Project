<?php
// لوحة تحكم الأدمن لمراجعة طلبات التوثيق والوظائف
include 'db.php';
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: main.php');
    exit();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT role FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$role = $stmt->fetchColumn();
if ($role !== 'admin') {
    die('غير مصرح');
}

// Determine current view: 'users' or 'jobs', default to 'users'
$view = isset($_GET['view']) && $_GET['view'] === 'jobs' ? 'jobs' : 'users';

// --- Handle Actions (Accept/Reject) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'], $_POST['action'])) {
    $action_type = $_POST['action_type']; // 'user_verification' or 'job'
    $action = $_POST['action']; // 'accept' or 'reject'
    $note = $_POST['note'] ?? '';

    if ($action_type === 'user_verification' && isset($_POST['verification_id'])) {
        $id = intval($_POST['verification_id']);
        $stmt = $conn->prepare('SELECT user_id FROM user_verification WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $uid = $row['user_id'];
            $conn->beginTransaction();
            if ($action === 'accept') {
                $conn->prepare('UPDATE user_verification SET status = "accepted", reviewed_at = NOW() WHERE id = :id')->execute([':id' => $id]);
                $conn->prepare('UPDATE user SET verification_status = "verified", verification_note = NULL WHERE User_ID = :uid')->execute([':uid' => $uid]);
                // Send internal notification to user (new logic for acceptance)
                $notif_msg = 'تمت الموافقة على توثيق حسابك.';
                // إضافة sender_id للأدمن (User_ID=25)
                $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, is_read, created_at) VALUES (:user_id, :sender_id, :message, 0, NOW())')->execute([
                    ':user_id' => $uid,
                    ':sender_id' => 25, // Assuming admin User_ID is 25
                    ':message' => $notif_msg
                ]);
            } elseif ($action === 'reject') {
                $conn->prepare('UPDATE user_verification SET status = "rejected", reviewed_at = NOW(), note = :note WHERE id = :id')->execute([':id' => $id, ':note' => $note]);
                // Change verification_status to 'not_verified' to allow re-submission
                $conn->prepare('UPDATE user SET verification_status = "not_verified", verification_note = :note WHERE User_ID = :uid')->execute([':note' => $note, ':uid' => $uid]);
                // Send internal notification to user (existing logic)
                $notif_msg = 'تم رفض طلب توثيق حسابك.';
                if (!empty($note)) {
                    $notif_msg .= ' سبب الرفض: ' . $note;
                }
                // إضافة sender_id للأدمن (User_ID=25)
                $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, is_read, created_at) VALUES (:user_id, :sender_id, :message, 0, NOW())')->execute([
                    ':user_id' => $uid,
                    ':sender_id' => 25,
                    ':message' => $notif_msg
                ]);
            }
            $conn->commit();
        }
        // Redirect back to the user verification view after action
         header('Location: admin_verifications.php?view=users');
         exit();

    } elseif ($action_type === 'job' && isset($_POST['job_id'])) {
        $id = intval($_POST['job_id']);
        $note = $_POST['note'] ?? '';
        $conn->beginTransaction();
        // جلب صاحب الوظيفة وعنوانها
        $stmt = $conn->prepare('SELECT employer_ID, title FROM job WHERE job_ID = :id');
        $stmt->execute([':id' => $id]);
        $jobInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        $employer_id = $jobInfo ? $jobInfo['employer_ID'] : null;
        $job_title = $jobInfo ? $jobInfo['title'] : '';
        if ($action === 'accept') {
            $conn->prepare('UPDATE job SET status = "published", rejection_note = NULL WHERE job_ID = :id')->execute([':id' => $id]);
            // إشعار القبول
            if ($employer_id) {
                $notif_msg = 'تمت الموافقة على نشر وظيفتك: "' . htmlspecialchars($job_title) . '" من قِبل صفحة الأدمن.';
                // إضافة sender_id للأدمن (User_ID=25)
                $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, is_read, created_at) VALUES (:user_id, :sender_id, :message, 0, NOW())')->execute([
                    ':user_id' => $employer_id,
                    ':sender_id' => 25,
                    ':message' => $notif_msg
                ]);
            }
        } elseif ($action === 'reject') {
            $conn->prepare('UPDATE job SET status = "rejected", rejection_note = :note WHERE job_ID = :id')->execute([':id' => $id, ':note' => $note]);
            // إشعار الرفض مع السبب
            if ($employer_id) {
                $notif_msg = 'تم رفض نشر وظيفتك: "' . htmlspecialchars($job_title) . '" من قِبل صفحة الأدمن.';
                if (!empty($note)) {
                    $notif_msg .= ' سبب الرفض: ' . htmlspecialchars($note);
                }
                // إضافة sender_id للأدمن (User_ID=25)
                $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, is_read, created_at) VALUES (:user_id, :sender_id, :message, 0, NOW())')->execute([
                    ':user_id' => $employer_id,
                    ':sender_id' => 25,
                    ':message' => $notif_msg
                ]);
            }
        }
        $conn->commit();
         // Redirect back to the jobs review view after action
         header('Location: admin_verifications.php?view=jobs');
         exit();
    }
}
// --- End Handle Actions ---


// --- Fetch Data based on view ---
$requests = []; // For user verifications
$jobs_pending_review = []; // For jobs

if ($view === 'users') {
    // جلب الطلبات قيد المراجعة (توثيق)
    $stmt = $conn->prepare('SELECT v.*, u.name, u.email FROM user_verification v JOIN user u ON v.user_id = u.User_ID WHERE v.status = "pending" ORDER BY v.created_at ASC');
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $page_title = 'مراجعة توثيقات المستخدمين';
} elseif ($view === 'jobs') {
    // جلب الوظائف قيد المراجعة
    $stmt = $conn->prepare('SELECT j.*, u.name as employer_name FROM job j JOIN user u ON j.employer_ID = u.User_ID WHERE j.status = "pending_review" ORDER BY j.created_at ASC');
    $stmt->execute();
    $jobs_pending_review = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $page_title = 'مراجعة الوظائف المعلنة';
}
// --- End Fetch Data ---

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?> - لوحة تحكم الأدمن</title>
    <link rel="stylesheet" href="css/project.css">
    <style>
        /* Existing styles */
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
        /* New styles for view switcher */
        .view-switcher { text-align:center; margin-bottom:20px; }
        .view-switcher a { margin:0 15px; text-decoration:none; font-size:1.2em; color:#555; padding-bottom:5px; border-bottom:2px solid transparent; transition:border-color 0.3s; }
        .view-switcher a.active { color:#1abc5b; border-bottom-color:#1abc5b; font-weight:bold; }
    </style>
</head>
<body style="background:#f5f5f5;">
    <a href="?logout=1" class="logout-btn">تسجيل الخروج</a>
    <div style="max-width:1100px;margin:40px auto;">
        <h2 style="text-align:center;"><?php echo $page_title; ?></h2>

        <!-- View Switcher -->
        <div class="view-switcher">
            <a href="?view=users" class="<?php echo $view === 'users' ? 'active' : ''; ?>">مراجعة توثيق الحسابات</a>
            <a href="?view=jobs" class="<?php echo $view === 'jobs' ? 'active' : ''; ?>">مراجعة الوظائف</a>
        </div>

        <!-- Display User Verifications Table -->
        <?php if ($view === 'users'): ?>
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
                <?php if (!empty($requests)): ?>
                    <?php foreach($requests as $req): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req['name']); ?></td>
                        <td><?php echo htmlspecialchars($req['email']); ?></td>
                        <td><?php echo htmlspecialchars($req['legal_name']); ?></td>
                        <td><?php echo htmlspecialchars($req['doc_type']); ?></td>
                        <td><a href="<?php echo htmlspecialchars($req['doc_path']); ?>" target="_blank">عرض الوثيقة</a></td>
                        <td><?php echo htmlspecialchars($req['created_at']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action_type" value="user_verification">
                                <input type="hidden" name="verification_id" value="<?php echo $req['id']; ?>">
                                <input type="text" name="note" class="note-input" placeholder="سبب الرفض (اختياري)">
                                <button type="submit" name="action" value="accept" class="admin-actions accept">قبول</button>
                                <button type="submit" name="action" value="reject" class="admin-actions reject">رفض</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:#888;">لا توجد طلبات توثيق قيد المراجعة حالياً.</td>
                    </tr>
                <?php endif; ?>
            </table>

        <!-- Display Jobs Review Table -->
        <?php elseif ($view === 'jobs'): ?>
            <table class="admin-table">
                <tr>
                    <th>عنوان الوظيفة</th>
                    <th>الوصف</th>
                    <th>الموقع</th>
                    <th>الراتب</th>
                    <th>صاحب العمل</th>
                    <th>تاريخ الإعلان</th>
                    <th>إجراءات</th>
                </tr>
                <?php if (!empty($jobs_pending_review)): ?>
                    <?php foreach($jobs_pending_review as $job): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($job['description'])); ?></td>
                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                        <td><?php echo htmlspecialchars($job['salary']); ?></td>
                        <td><?php echo htmlspecialchars($job['employer_name']); ?></td>
                        <td><?php echo htmlspecialchars($job['created_at']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action_type" value="job">
                                <input type="hidden" name="job_id" value="<?php echo $job['job_ID']; ?>">
                                <input type="text" name="note" class="note-input" placeholder="سبب الرفض (اختياري)">
                                <button type="submit" name="action" value="accept" class="admin-actions accept">قبول</button>
                                <button type="submit" name="action" value="reject" class="admin-actions reject">رفض</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#888;">لا توجد وظائف قيد المراجعة حالياً.</td>
                    </tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>