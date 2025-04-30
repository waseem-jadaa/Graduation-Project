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
    
    <header class="dashboard-header">
      <div class="container">
        <nav>
          <div class="logo">
            <i class="fas fa-handshake"></i>
            <span>Fursa<span style="color: var(--primary);">Pal</span></span>
          </div>
          
          <div class="search-bar">
            <input type="text" placeholder="ابحث عن وظائف أو مهنيين...">
            <button><i class="fas fa-search"></i></button>
          </div>
          
          <div class="user-actions">
            <div class="notification-bell">
              <i class="fas fa-bell"></i>
              <span class="notification-count">3</span>
            </div>
            <div class="user-profile">
              <img src="image/P.png" alt="صورة المستخدم">
              <span><?php echo $user_name; ?></span>
              <i class="fas fa-chevron-down"></i>
            </div>
          </div>
        </nav>
      </div>
    </header>

    
    <?php include 'sidebar.php'; ?>

    
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

        <section class="suggested-jobs">
          <div class="section-header">
            <h2>وظائف مقترحة لك</h2>
            <a href="#" class="view-all">عرض الكل</a>
          </div>
          
          <div class="jobs-grid">
            <div class="job-card">
              <div class="job-header">
                <img src="image/n.png" alt="شعار الشركة" class="company-logo">
                <div class="job-title">
                  <h3>نجار</h3>
                  <p>منجرة الأمانة</p>
                </div>
                <div class="job-save">
                  <i class="far fa-bookmark"></i>
                </div>
              </div>
              <div class="job-details">
                <p><i class="fas fa-map-marker-alt"></i> رام الله</p>
                <p><i class="fas fa-clock"></i> دوام كامل</p>
                <p><i class="fas fa-money-bill-wave"></i> $1,500 - $2,000</p>
              </div>
              <div class="job-skills">
                <span>تصميم غرف نوم </span>
                <span>ابواب مميزة</span>
                <span>مطابخ بتصاميم فاخرة </span>
              </div>
              <div class="job-actions">
                <button class="btn-apply">تقدم الآن</button>
                <button class="btn-details">التفاصيل</button>
              </div>
            </div>
            
            <div class="job-card featured">
              <div class="featured-badge">مميز</div>
              <div class="job-header">
                <img src="image/d.avif" alt="شعار الشركة" class="company-logo">
                <div class="job-title">
                  <h3>دهان</h3>
                  <p>جميع انواع الدهانات وتصاميم الجبص</p>
                </div>
                <div class="job-save">
                  <i class="fas fa-bookmark"></i>
                </div>
              </div>
              <div class="job-details">
                <p><i class="fas fa-map-marker-alt"></i> نابلس</p>
                <p><i class="fas fa-clock"></i> دوام جزئي</p>
                <p><i class="fas fa-money-bill-wave"></i> $800 - $1,200</p>
              </div>
              <div class="job-skills">
                <span>دهان فيكتوري</span>
                <span>نصميم جبص باشكال ايطالية</span>
                <span>تفنيش العمل بكفاءة عالية ونظافة </span>
              </div>
              <div class="job-actions">
                <button class="btn-apply">تقدم الآن</button>
                <button class="btn-details">التفاصيل</button>
              </div>
            </div>
            
            <div class="job-card">
              <div class="job-header">
                <img src="image/a.webp" alt="شعار الشركة" class="company-logo">
                <div class="job-title">
                  <h3>المنيوم</h3>
                  <p>شركة الامل للالمنيوم</p>
                </div>
                <div class="job-save">
                  <i class="far fa-bookmark"></i>
                </div>
              </div>
              <div class="job-details">
                <p><i class="fas fa-map-marker-alt"></i> الخليل</p>
                <p><i class="fas fa-clock"></i> دوام كامل</p>
                <p><i class="fas fa-money-bill-wave"></i> $1,800 - $2,500</p>
              </div>
              <div class="job-skills">
                <span>شبابيك + ابواب</span>
                <span>تركيب مطورات </span>
                <span>باب كهربائي </span>
              </div>
              <div class="job-actions">
                <button class="btn-apply">تقدم الآن</button>
                <button class="btn-details">التفاصيل</button>
              </div>
            </div>
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