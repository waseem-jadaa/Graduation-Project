<?php
// saved_jobs.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT j.job_ID, j.title, j.description, j.location, j.salary, j.employer_ID, u.name as employer_name
    FROM saved_jobs s
    JOIN job j ON s.job_id = j.job_ID
    JOIN user u ON j.employer_ID = u.User_ID
    WHERE s.user_id = :uid
    ORDER BY s.saved_at DESC');
$stmt->execute([':uid' => $user_id]);
$saved_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الوظائف المحفوظة</title>
    <link rel="stylesheet" href="css/project.css">
   
    <style>
        .saved-jobs-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px #0001;
            padding: 32px 24px;
        }
        .saved-job-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #eee;
            padding: 18px 0;
        }
        .saved-job-info {
            flex: 1;
        }
        .saved-job-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #1a5f7a;
        }
        .saved-job-desc {
            color: #555;
            margin: 6px 0 0 0;
        }
        .saved-job-meta {
            color: #888;
            font-size: 0.95em;
            margin-top: 4px;
        }
        .remove-saved-btn {
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.2s;
        }
        .remove-saved-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
<?php include 'headerDash.php'; ?>
<div class="saved-jobs-container">
    <h2 style="margin-bottom:24px;">الوظائف المحفوظة</h2>
    <?php if (empty($saved_jobs)): ?>
        <p style="color:#888;">لا يوجد وظائف محفوظة حالياً.</p>
    <?php else: ?>
        <?php foreach ($saved_jobs as $job): ?>
            <div class="saved-job-card" data-job-id="<?php echo $job['job_ID']; ?>">
                <div class="saved-job-info">
                    <div class="saved-job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                    <div class="saved-job-desc"><?php echo htmlspecialchars($job['description']); ?></div>
                    <div class="saved-job-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span> |
                        <span><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary']); ?></span> |
                        <span>صاحب العمل: <?php echo htmlspecialchars($job['employer_name']); ?></span>
                    </div>
                </div>
                <button class="remove-saved-btn" data-job-id="<?php echo $job['job_ID']; ?>">حذف</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script src="headerDash.js"></script>
<script>
// حذف وظيفة محفوظة عبر AJAX
  document.querySelectorAll('.remove-saved-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      if (!confirm('هل أنت متأكد من حذف هذه الوظيفة من المحفوظات؟')) return;
      var jobId = btn.getAttribute('data-job-id');
      fetch('remove_saved_job.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'job_id=' + encodeURIComponent(jobId)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          // إزالة العنصر من الصفحة
          btn.closest('.saved-job-card').remove();
        } else {
          alert('حدث خطأ أثناء الحذف.');
        }
      })
      .catch(() => {
        alert('حدث خطأ في الاتصال بالخادم.');
      });
    });
  });
</script>
</body>
</html>
