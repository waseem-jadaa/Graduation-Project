<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Verify user is an employer
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT role FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['role'] !== 'employer') {
    header('Location: dashboard.php');
    exit();
}

// Handle job deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_job'])) {
    $job_id = $_POST['job_id'];
    
    // جلب المتقدمين بدون تكرار
    $stmt = $conn->prepare('SELECT DISTINCT user_ID FROM application WHERE job_ID = :job_id');
    $stmt->execute([':job_id' => $job_id]);
    $applicants = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // جلب صورة صاحب العمل
    $stmt_photo = $conn->prepare('SELECT p.profile_photo FROM profile p WHERE p.User_ID = :uid');
    $stmt_photo->execute([':uid' => $user_id]);
    $photo_row = $stmt_photo->fetch(PDO::FETCH_ASSOC);
    $employer_photo = $photo_row && !empty($photo_row['profile_photo']) ? $photo_row['profile_photo'] : 'image/p.png';

    // إرسال الإشعار للمتقدمين (قبل الحذف)
    foreach ($applicants as $applicant_id) {
        $stmt = $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, link, is_read, created_at, employer_photo) VALUES (:user_id, :sender_id, :message, :link, 0, NOW(), :employer_photo)');
        $stmt->execute([
            ':user_id' => $applicant_id,
            ':sender_id' => $user_id,
            ':message' => 'تم إغلاق الوظيفة التي تقدمت لها',
            ':link' => 'job.php?job_id=' . $job_id,
            ':employer_photo' => $employer_photo
        ]);
    }

    // حذف الطلبات المرتبطة بالوظيفة
    $stmt = $conn->prepare('DELETE FROM application WHERE job_ID = :job_id');
    $stmt->execute([':job_id' => $job_id]);

    // حذف الوظيفة من الوظائف المحفوظة
    $stmt = $conn->prepare('DELETE FROM saved_jobs WHERE job_id = :job_id');
    $stmt->execute([':job_id' => $job_id]);

    // حذف الوظيفة نفسها
    $stmt = $conn->prepare('DELETE FROM job WHERE job_ID = :job_id AND employer_ID = :employer_id');
    $stmt->execute([
        ':job_id' => $job_id,
        ':employer_id' => $user_id
    ]);
    
    header('Location: manage_jobs.php?success=deleted');
    exit();
}

// Handle job updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_job'])) {
    $job_id = $_POST['job_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $salary = $_POST['salary'];
    
    // Update job details
    $stmt = $conn->prepare('UPDATE job SET title = :title, description = :description, location = :location, salary = :salary WHERE job_ID = :job_id AND employer_ID = :employer_id');
    $stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':location' => $location,
        ':salary' => $salary,
        ':job_id' => $job_id,
        ':employer_id' => $user_id
    ]);
    
    // Get applicants to notify them (distinct)
    $stmt = $conn->prepare('SELECT DISTINCT user_ID FROM application WHERE job_ID = :job_id');
    $stmt->execute([':job_id' => $job_id]);
    $applicants = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // جلب صورة صاحب العمل
    $stmt_photo = $conn->prepare('SELECT p.profile_photo FROM profile p WHERE p.User_ID = :uid');
    $stmt_photo->execute([':uid' => $user_id]);
    $photo_row = $stmt_photo->fetch(PDO::FETCH_ASSOC);
    $employer_photo = $photo_row && !empty($photo_row['profile_photo']) ? $photo_row['profile_photo'] : 'image/p.png';
    
    // Notify applicants about the update
    foreach ($applicants as $applicant_id) {
        $stmt = $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, link, is_read, created_at, employer_photo) VALUES (:user_id, :sender_id, :message, :link, 0, NOW(), :employer_photo)');
        $stmt->execute([
            ':user_id' => $applicant_id,
            ':sender_id' => $user_id,
            ':message' => 'تم تحديث تفاصيل الوظيفة التي تقدمت لها',
            ':link' => 'job.php?job_id=' . $job_id,
            ':employer_photo' => $employer_photo
        ]);
    }
    
    header('Location: manage_jobs.php?success=updated');
    exit();
}

// Fetch employer's jobs
$stmt = $conn->prepare('SELECT * FROM job WHERE employer_ID = :employer_id AND status IN ("published", "filled") ORDER BY created_at DESC');
$stmt->execute([':employer_id' => $user_id]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الوظائف - FursaPal</title>
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/manage_jobs.css">
</head>
<body>
    <?php include 'headerDash.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <h1>إدارة الوظائف</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">
                    <?php if ($_GET['success'] === 'updated'): ?>
                        تم تحديث الوظيفة بنجاح
                    <?php elseif ($_GET['success'] === 'deleted'): ?>
                        تم حذف الوظيفة بنجاح
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($jobs)): ?>
                <p>لا توجد وظائف منشورة حالياً.</p>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card" id="job-<?php echo $job['job_ID']; ?>">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <span class="job-status <?php echo $job['status'] === 'filled' ? 'status-filled' : 'status-published'; ?>">
                            <?php echo $job['status'] === 'filled' ? 'نفذت' : 'منشورة'; ?>
                        </span>
                        <p><strong>الموقع:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                        <p><strong>الراتب:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                        <p><strong>الوصف:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                        <p><strong>تاريخ النشر:</strong> <?php echo date('Y-m-d', strtotime($job['created_at'])); ?></p>
                        
                        <div class="job-actions">
                            <?php if ($job['status'] === 'published'): ?>
                                <button class="btn-edit" onclick="toggleEditForm(<?php echo $job['job_ID']; ?>)">تعديل</button>
                                <button class="btn-delete" onclick="confirmDelete(<?php echo $job['job_ID']; ?>)">حذف</button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="edit-form" id="edit-form-<?php echo $job['job_ID']; ?>">
                            <form method="POST" action="">
                                <input type="hidden" name="job_id" value="<?php echo $job['job_ID']; ?>">
                                <div class="form-group">
                                    <label for="title-<?php echo $job['job_ID']; ?>">عنوان الوظيفة</label>
                                    <input type="text" id="title-<?php echo $job['job_ID']; ?>" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="location-<?php echo $job['job_ID']; ?>">الموقع</label>
                                    <input type="text" id="location-<?php echo $job['job_ID']; ?>" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="salary-<?php echo $job['job_ID']; ?>">الراتب</label>
                                    <input type="text" id="salary-<?php echo $job['job_ID']; ?>" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="description-<?php echo $job['job_ID']; ?>">الوصف</label>
                                    <textarea id="description-<?php echo $job['job_ID']; ?>" name="description" required><?php echo htmlspecialchars($job['description']); ?></textarea>
                                </div>
                                <button type="submit" name="update_job" class="btn-edit">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="js/manage_jobs.js"></script>
</body>
</html> 