<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); //اعادة التوجيه في حال لم يتم تسجيل الدخول 
    exit();
}
include 'db.php';

// Fetch user name and role from database
$user_id = $_SESSION['user_id'];
$user_name = '';
$role = '';
try {
    $stmt = $conn->prepare('SELECT name, role FROM user WHERE User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $user_name = htmlspecialchars($row['name']);
        $role = $row['role'];
    } else {
        $user_name = 'مستخدم';
        $role = '';
    }
} catch (PDOException $e) {
    $user_name = 'مستخدم';
    $role = '';
}

// جلب عدد الإشعارات غير المقروءة
$unread_notifications = 0;
try {
    $stmt = $conn->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
    $stmt->execute([':user_id' => $user_id]);
    $unread_notifications = $stmt->fetchColumn();
} catch (PDOException $e) {
    $unread_notifications = 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FursaPal - لوحة التحكم</title>
    <link rel="stylesheet" href="css/project.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <link rel="stylesheet" href="css/rating.css">
    <link rel="stylesheet" href="css/request-btn.css">
    <link rel="stylesheet" href="css/new_jobs_modal.css">
  </head>

  <body class="dashboard-page">
    <?php include 'headerDash.php'; ?>
    
    <main class="main-content">
      <div class="container">
        <section class="welcome-card">
          <div class="welcome-content">
            <h1>مرحباً بعودتك، <?php echo $user_name; ?>!</h1>
            <p>
              <?php if ($unread_notifications > 0): ?>
                <span style="color:antiquewhite;font-weight:bold;">لديك <?php echo $unread_notifications; ?> إشعار جديد غير مقروء</span>
              <?php else: ?>
                لا توجد إشعارات جديدة حالياً.
              <?php endif; ?>
            </p>
            <?php if ($role === 'job_seeker'): ?>
              <button class="btn-explore" id="btn-explore-jobs">استكشف الوظائف الجديدة</button>
            <?php endif; ?>
            <?php if ($role === 'employer'): ?>
              <button class="btn-job-announcement" onclick="location.href='job_post.php'">نشر وظيفة</button>
              <button class="btn-manage-jobs" onclick="location.href='manage_jobs.php'">إدارة وظائفي</button>
            <?php endif; ?>
          </div>
        </section>

        <!-- نافذة منبثقة لعرض الوظائف الجديدة -->
        <div id="new-jobs-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:9999;align-items:center;justify-content:center;">
          <div style="background:#fff;padding:30px 20px;border-radius:12px;max-width:500px;width:90vw;max-height:80vh;overflow-y:auto;position:relative;">
            <button onclick="document.getElementById('new-jobs-modal').style.display='none'" style="position:absolute;top:10px;left:10px;font-size:1.2em;">&times;</button>
            <h2 style="margin-bottom:20px;">الوظائف الجديدة</h2>
            <div id="new-jobs-list">جاري التحميل...</div>
          </div>
        </div>

        <?php if ($role === 'job_seeker'): ?>
        <section class="quick-stats">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
              <h3>
                <?php
                // عدد الوظائف المتاحة (المنشورة فقط)
                $stmt = $conn->prepare('SELECT COUNT(*) FROM job WHERE status = "published"');
                $stmt->execute();
                echo $stmt->fetchColumn();
                ?>
              </h3>
              <p>وظيفة متاحة</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
              <h3>
                <?php
                // عدد الطلبات المقبولة لهذا المهني
                $stmt = $conn->prepare('SELECT COUNT(*) FROM application WHERE user_ID = :uid AND status = "accepted"');
                $stmt->execute([':uid' => $user_id]);
                echo $stmt->fetchColumn();
                ?>
              </h3>
              <p>طلبات مقبولة</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-comments"></i>
            </div>
            <div class="stat-info">
              <h3>
                <?php
                // عدد المحادثات الجديدة (غير المقروءة)
                $stmt = $conn->prepare('SELECT COUNT(DISTINCT sender_ID) FROM message WHERE receiver_ID = :me AND seen = 0');
                $stmt->execute([':me' => $user_id]);
                echo $stmt->fetchColumn();
                ?>
              </h3>
              <p>محادثات جديدة</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
              <h3>
                <?php
                // متوسط التقييم للمهني
                $stmt = $conn->prepare('SELECT AVG(rating) FROM professional_ratings WHERE professional_id = :uid');
                $stmt->execute([':uid' => $user_id]);
                $avg = $stmt->fetchColumn();
                echo $avg ? round($avg, 1) : '0';
                ?>
              </h3>
              <p>تقييمك</p>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <?php if ($role === 'job_seeker'): ?>
        <!-- AI Job Recommendations Section -->
        <section class="ai-recommend-section" style="margin-bottom: 32px;">
          <div class="section-header" style="display:flex;align-items:center;gap:10px;">
            <h2 style="margin:0;">وظائف مقترحة مطابقة لمهنتك ومهاراتك </h2>
            <button id="ai-help-btn" style="background:none;border:none;cursor:pointer;font-size:1.3em;" title="معلومات"><span>ℹ️</span></button>
          </div>
          <div id="ai-recommend-loading" style="color:#888;margin:16px 0;">جاري جلب التوصيات الذكية...</div>
          <div id="ai-recommend-jobs" class="jobs-grid"></div>
        </section>
        <!-- AI Help Modal -->
        <div id="ai-help-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:9999;align-items:center;justify-content:center;">
          <div style="background:#fff;padding:30px 20px;border-radius:12px;max-width:400px;width:90vw;max-height:80vh;overflow-y:auto;position:relative;">
            <button onclick="document.getElementById('ai-help-modal').style.display='none'" style="position:absolute;top:10px;left:10px;font-size:1.2em;">&times;</button>
            <h3 style="margin-bottom:10px;">كيف تعمل التوصيات الذكية؟</h3>
            <p style="font-size:1.05em;line-height:1.7;">هذه الوظائف مقترحة بناءً على مهاراتك ومهنتك في ملفك الشخصي، حيث يتم مطابقة بياناتك مع الوظائف المتاحة باستخدام خوارزمية ذكية . يمكنك مراجعة الوظائف والتقديم مباشرة إذا أعجبتك التوصيات.</p>
          </div>
        </div>
        <?php endif; ?>

        <?php
        // Fetch suggested jobs or professionals based on role
        $suggested_professionals = [];
        $suggested_jobs = [];
        if ($role === 'employer') {
            try {
                $stmt = $conn->prepare("SELECT user.User_ID, CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience, profile.profile_photo FROM user INNER JOIN profile ON user.User_ID = profile.User_ID WHERE user.role = 'job_seeker' ORDER BY RAND() LIMIT 3");
                $stmt->execute();
                $suggested_professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo '<p>حدث خطأ أثناء جلب المهنيين المقترحين.</p>';
            }
        } else {
            // جلب الوظائف المحفوظة للمستخدم الحالي
            $saved_job_ids = [];
            $stmt = $conn->prepare('SELECT job_id FROM saved_jobs WHERE user_id = :uid');
            $stmt->execute([':uid' => $user_id]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $saved_job_ids[] = $row['job_id'];
            }
            // جلب الوظائف المقترحة كما كان سابقاً
            try {
                $stmt = $conn->prepare('SELECT j.*, u.name as employer_name FROM job j 
                    JOIN user u ON j.employer_ID = u.User_ID 
                    WHERE j.status = "published" 
                    ORDER BY RAND() LIMIT 3');
                $stmt->execute();
                $suggested_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo '<p>حدث خطأ أثناء جلب الوظائف المقترحة.</p>';
            }
            // بعد جلب الوظائف المقترحة، أضف حقل saved
            foreach ($suggested_jobs as &$job) {
                $job['saved'] = in_array($job['job_ID'], $saved_job_ids) ? 1 : 0;
            }
        }
        ?>

        <section class="suggested-jobs">
          <div class="section-header">
            <?php if ($role === 'employer'): ?>
              <h2>مهنيون مقترحون لك</h2>
              <a href="professionals.php" class="view-all">عرض الكل</a>
            <?php else: ?>
              <h2>وظائف مقترحة أخرى</h2>
              <a href="jobs.php" class="view-all">عرض الكل</a>
            <?php endif; ?>
          </div>

          <div class="jobs-grid">
            <?php if ($role === 'employer'): ?>
              <?php foreach ($suggested_professionals as $pro): ?>
                <div class="job-card">
                  <div class="job-header">
                    <img src="<?php echo htmlspecialchars($pro['profile_photo'] ?? 'image/p.png'); ?>" alt="صورة الحساب" class="company-logo">
                    <div class="job-title">
                      <h3><?php echo htmlspecialchars($pro['name']); ?></h3>
                      <p><?php echo htmlspecialchars($pro['profession']); ?></p>
                    </div>
                  </div>
                  <div class="job-details">
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($pro['location']); ?></p>
                    <p><i class="fas fa-briefcase"></i> خبرة: <?php echo htmlspecialchars($pro['experience']); ?> سنوات</p>
                    <div class="rating-stars-dashboard" data-professional-id="<?php echo $pro['User_ID']; ?>">
                      <span>جاري التحميل...</span>
                    </div>
                  </div>
                  <div class="job-actions">
                    <button class="btn-request-professional" data-professional-id="<?php echo $pro['User_ID']; ?>">اطلبه الآن</button>
                    <a href="professional_details.php?id=<?php echo htmlspecialchars($pro['User_ID']); ?>" class="btn-details">التفاصيل</a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <?php foreach ($suggested_jobs as $job): ?>
                <div class="job-card">
                  <div class="job-header">
                    <img src="https://img.icons8.com/color/48/briefcase--v1.png" alt="وظائف عامة" class="company-logo">
                    <div class="job-title">
                      <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    </div>
                    <div class="job-save<?php echo !empty($job['saved']) ? ' saved' : ''; ?>" data-job-id="<?php echo $job['job_ID']; ?>" data-saved="<?php echo !empty($job['saved']) ? '1' : '0'; ?>">
                      <i class="<?php echo !empty($job['saved']) ? 'fas' : 'far'; ?> fa-bookmark"></i>
                    </div>
                  </div>
                  <div class="job-details">
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary']); ?></p>
                    <p><i class="fas fa-building"></i> <?php echo htmlspecialchars($job['employer_name']); ?></p>
                  </div>
                  <div class="job-details-full">
                    <h4>تفاصيل الوظيفة:</h4>
                    <p><?php echo htmlspecialchars($job['description']); ?></p>
                  </div>
                  <div class="job-actions">
                    <button class="btn-apply" data-job-id="<?php echo $job['job_ID']; ?>">تقدم الآن</button>
                    <button class="btn-details" onclick="toggleDetails(this)">التفاصيل</button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </section>

        <section class="activity-section">
          <div class="section-header">
            <h2>آخر الأنشطة</h2>
          </div>
          
          <div class="activity-timeline" id="activity-timeline">
            <div style="text-align:center;color:#888;">جاري التحميل...</div>
          </div>
        </section>
      </div>
    </main>

    <div class="mobile-bottom-nav">
      <a href="#" class="mobile-nav-item active">
        <i class="fas fa-home"></i>
        <span>الرئيسية</span>
      </a>
      <a href="#" class="mobile-nav-item">
        <i class="fas fa-briefcase"></i>
        <span>الوظائف</span>
      </a>
      <a href="#" class="mobile-nav-item">
        <i class="fas fa-envelope"></i>
        <span>الرسائل</span>
      </a>
      <a href="#" class="mobile-nav-item">
        <i class="fas fa-user"></i>
        <span>حسابي</span>
      </a>
    </div>

    <script src="js/dashboard.js"></script>
  </body>
</html>