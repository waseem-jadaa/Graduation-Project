<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); //اعادة التوجيه في حال لم يتم تسجيل الدخول 
    exit();
}
include 'db.php';

// Fetch user name from database
$user_id = $_SESSION['user_id'];
$user_name = '';
try {
    $stmt = $conn->prepare('SELECT name FROM user WHERE User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $user_name = htmlspecialchars($row['name']);
    } else {
        $user_name = 'مستخدم';
    }
} catch (PDOException $e) {
    $user_name = 'مستخدم';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FursaPal - لوحة التحكم</title>
    <link rel="stylesheet" href="project.css" />
    <link rel="stylesheet" href="dashboard.php" />
  </head>

  <body class="dashboard-page">
    
    <?php include 'headerDash.php'; ?>
    
    
    <main class="main-content">
      <div class="container">
       
        <section class="welcome-card">
          <div class="welcome-content">
            <h1>مرحباً بعودتك، <?php echo $user_name; ?>!</h1>
            <p>لديك 3 وظائف مقترحة و 5 رسائل جديدة</p>
            <button class="btn-explore">استكشف الوظائف الجديدة</button>
            <button class="btn-job-announcement" onclick="location.href='job_post.php'">نشر وظيفة</button>
          </div>
          
        </section>

        
        <section class="quick-stats">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
              <h3>12</h3>
              <p>وظيفة متاحة</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
              <h3>5</h3>
              <p>طلبات مقبولة</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-comments"></i>
            </div>
            <div class="stat-info">
              <h3>8</h3>
              <p>محادثات جديدة</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
              <h3>4.8</h3>
              <p>تقييمك</p>
            </div>
          </div>
        </section>

        <?php
        // Fetch suggested jobs from the database
        $suggested_jobs = [];
        try {
            $stmt = $conn->prepare('SELECT * FROM job ORDER BY RAND() LIMIT 3');
            $stmt->execute();
            $suggested_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo '<p>حدث خطأ أثناء جلب الوظائف المقترحة.</p>';
        }
        ?>

        <section class="suggested-jobs">
          <div class="section-header">
            <h2>وظائف مقترحة لك</h2>
            <a href="#" class="view-all">عرض الكل</a>
          </div>

          <div class="jobs-grid">
            <?php foreach ($suggested_jobs as $job): ?>
              <div class="job-card">
                <div class="job-header">
                  <img src="image/n.png" alt="شعار الشركة" class="company-logo">
                  <div class="job-title">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p><?php echo htmlspecialchars($job['description']); ?></p>
                  </div>
                  <div class="job-save">
                    <i class="far fa-bookmark"></i>
                  </div>
                </div>
                <div class="job-details">
                  <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
                  <p><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary']); ?></p>
                </div>
                <div class="job-actions">
                  <button class="btn-apply">تقدم الآن</button>
                  <button class="btn-details">التفاصيل</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post-job'])) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $location = $_POST['location'];
            $salary = $_POST['salary'];
            $employer_id = $_SESSION['user_id'];

            try {
                $stmt = $conn->prepare('INSERT INTO job (employer_ID, title, description, location, salary) VALUES (:employer_id, :title, :description, :location, :salary)');
                $stmt->execute([
                    ':employer_id' => $employer_id,
                    ':title' => $title,
                    ':description' => $description,
                    ':location' => $location,
                    ':salary' => $salary
                ]);
                echo '<p>تمت إضافة الوظيفة بنجاح!</p>';
            } catch (PDOException $e) {
                echo '<p>حدث خطأ أثناء إضافة الوظيفة.</p>';
            }
        }
        ?>

        <section class="activity-section">
          <div class="section-header">
            <h2>آخر الأنشطة</h2>
          </div>
          
          <div class="activity-timeline">
            <div class="activity-item">
              <div class="activity-icon">
                <i class="fas fa-briefcase"></i>
              </div>
              <div class="activity-content">
                <p>تم قبول طلبك لوظيفة "نجار" في شركة الامانة للنجارة</p>
                <span class="activity-time">منذ ساعتين</span>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon">
                <i class="fas fa-comment"></i>
              </div>
              <div class="activity-content">
                <p>لديك اتصال من شركة الامل للالمنيوم </p>
                <span class="activity-time">منذ 5 ساعات</span>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon">
                <i class="fas fa-user-plus"></i>
              </div>
              <div class="activity-content">
                <p>قام محمد علي بمشاهدة ملفك الشخصي</p>
                <span class="activity-time">منذ يوم واحد</span>
              </div>
            </div>
            
            <div class="activity-item">
              <div class="activity-icon">
                <i class="fas fa-star"></i>
              </div>
              <div class="activity-content">
                <p>حصلت على تقييم جديد 5 نجوم من صاحب العمل</p>
                <span class="activity-time">منذ يومين</span>
              </div>
            </div>
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

    

    <script>
      function openJobAnnouncement() {
        document.getElementById('job-announcement-modal').style.display = 'block';
      }

      function closeJobAnnouncement() {
        document.getElementById('job-announcement-modal').style.display = 'none';
      }
      
      document.addEventListener('DOMContentLoaded', function() {
       
        document.querySelectorAll('.job-save').forEach(btn => {
          btn.addEventListener('click', function() {
            this.querySelector('i').classList.toggle('far');
            this.querySelector('i').classList.toggle('fas');
          });
        });
        
       
        document.querySelector('.user-profile').addEventListener('click', function() {
          this.classList.toggle('active');
        });
      });
    </script>
  </body>
</html>