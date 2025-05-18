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
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FursaPal - لوحة التحكم</title>
    <link rel="stylesheet" href="css/project.css" />
    <link rel="stylesheet" href="dashboard.php" />
    <link rel="stylesheet" href="css/rating.css">
    <link rel="stylesheet" href="css/request-btn.css">
  </head>

  <body class="dashboard-page">
    
    <?php include 'headerDash.php'; ?>
    
    
    <main class="main-content">
      <div class="container">
       
        <section class="welcome-card">
          <div class="welcome-content">
            <h1>مرحباً بعودتك، <?php echo $user_name; ?>!</h1>
            <p>لديك 3 وظائف مقترحة و 5 رسائل جديدة</p>
            <?php if ($role === 'job_seeker'): ?>
              <button class="btn-explore" id="btn-explore-jobs">استكشف الوظائف الجديدة</button>
            <?php endif; ?>
            <?php if ($role === 'employer'): ?>
            <button class="btn-job-announcement" onclick="location.href='job_post.php'">نشر وظيفة</button>
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
                // عدد الوظائف المتاحة
                $stmt = $conn->prepare('SELECT COUNT(*) FROM job');
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
              <h3>8</h3>
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
                $stmt = $conn->prepare('SELECT * FROM job ORDER BY RAND() LIMIT 3');
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
              <h2>وظائف مقترحة لك</h2>
              <a href="#" class="view-all">عرض الكل</a>
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
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <?php foreach ($suggested_jobs as $job): ?>
                <div class="job-card">
                  <div class="job-header">
                    <img src="image/n.png" alt="شعار الشركة" class="company-logo">
                    <div class="job-title">
                      <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                      <p><?php echo htmlspecialchars($job['description']); ?></p>
                    </div>
                    <div class="job-save<?php echo !empty($job['saved']) ? ' saved' : ''; ?>" data-job-id="<?php echo $job['job_ID']; ?>" data-saved="<?php echo !empty($job['saved']) ? '1' : '0'; ?>">
                      <i class="<?php echo !empty($job['saved']) ? 'fas' : 'far'; ?> fa-bookmark"></i>
                    </div>
                  </div>
                  <div class="job-details">
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($job['salary']); ?></p>
                  </div>
                  <div class="job-actions">
                    <button class="btn-apply" data-job-id="<?php echo $job['job_ID']; ?>">تقدم الآن</button>
                    <button class="btn-details">التفاصيل</button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
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
          // تفعيل اللون الأخضر عند الحفظ
          if (btn.dataset.saved === '1') {
            btn.classList.add('saved');
            btn.querySelector('i').classList.remove('far');
            btn.querySelector('i').classList.add('fas');
          }
          btn.addEventListener('click', function(e) {
            e.preventDefault();
            var jobId = btn.getAttribute('data-job-id');
            if (!jobId) {
              alert('معرف الوظيفة غير متوفر.');
              return;
            }
            if (btn.classList.contains('saved')) {
              // إزالة الحفظ
              fetch('remove_saved_job.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'job_id=' + encodeURIComponent(jobId)
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  btn.classList.remove('saved');
                  btn.querySelector('i').classList.remove('fas');
                  btn.querySelector('i').classList.add('far');
                } else {
                  alert('حدث خطأ أثناء إزالة الحفظ.');
                }
              })
              .catch(() => {
                alert('حدث خطأ في الاتصال بالخادم.');
              });
            } else {
              // إضافة الحفظ
              fetch('save_job.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'job_id=' + encodeURIComponent(jobId)
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  btn.classList.add('saved');
                  btn.querySelector('i').classList.remove('far');
                  btn.querySelector('i').classList.add('fas');
                } else if (data.error === 'already_saved') {
                  alert('هذه الوظيفة محفوظة بالفعل.');
                } else if (data.error === 'unauthorized') {
                  alert('يجب تسجيل الدخول لحفظ الوظيفة.');
                } else {
                  alert('حدث خطأ أثناء الحفظ.');
                }
              })
              .catch(() => {
                alert('حدث خطأ في الاتصال بالخادم.');
              });
            }
          });
        });
        
        document.querySelectorAll('.job-save').forEach(btn => {
          btn.addEventListener('click', function() {
            this.querySelector('i').classList.toggle('far');
            this.querySelector('i').classList.toggle('fas');
          });
        });
        
        // تفعيل زر التقديم على الوظيفة
        document.querySelectorAll('.btn-apply').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var jobCard = btn.closest('.job-card');
            if (!jobCard) return;
            // محاولة الحصول على معرف الوظيفة من data attribute
            var jobId = btn.getAttribute('data-job-id');
            if (!jobId) {
              alert('معرف الوظيفة غير متوفر. يرجى مراجعة الإدارة.');
              return;
            }
            btn.disabled = true;
            fetch('apply_job.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: 'job_id=' + encodeURIComponent(jobId)
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                btn.textContent = 'تم التقديم';
                btn.classList.add('applied');
                alert('تم إرسال طلبك بنجاح! سيتم إشعار صاحب العمل.');
              } else if (data.error === 'already_applied') {
                btn.textContent = 'تم التقديم مسبقاً';
                alert('لقد تقدمت لهذه الوظيفة مسبقاً.');
              } else {
                alert('حدث خطأ أثناء التقديم.');
              }
            })
            .catch(() => {
              alert('حدث خطأ في الاتصال بالخادم.');
            })
            .finally(() => {
              btn.disabled = false;
            });
          });
        });
        
        document.querySelectorAll('.btn-request-professional').forEach(function(btn) {
          btn.addEventListener('click', function() {
            var professionalId = btn.getAttribute('data-professional-id');
            if (professionalId) {
              // إرسال الطلب عبر AJAX
              fetch('request_professional.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'professional_id=' + encodeURIComponent(professionalId)
              })
              .then(res => res.json())
              .then(data => {
                if (data.success) {
                  alert('تم إرسال طلبك لهذا المهني!');
                } else if (data.error === 'already_requested') {
                  alert('لقد أرسلت طلباً لهذا المهني مسبقاً.');
                } else {
                  alert('حدث خطأ أثناء إرسال الطلب.');
                }
              })
              .catch(() => {
                alert('حدث خطأ في الاتصال بالخادم.');
              });
            }
          });
        });
        
        document.querySelector('.user-profile').addEventListener('click', function() {
          this.classList.toggle('active');
        });

        document.querySelectorAll('.rating-stars-dashboard').forEach(function(div) {
          var pid = div.getAttribute('data-professional-id');
          fetch('get_user_role.php')
            .then(res => res.json())
            .then(userInfo => {
              fetch('get_professional_rating.php?professional_id=' + pid)
                .then(res => res.json())
                .then(data => {
                  let avg = data && typeof data.avg_rating !== 'undefined' ? Math.round(data.avg_rating) : 0;
                  let stars = '';
                  for (let i = 1; i <= 5; i++) {
                    stars += `<span class="star" data-star="${i}" style="color:${i <= avg ? '#FFD700' : '#ccc'};font-size:1.3em;cursor:pointer;">★</span>`;
                  }
                  div.innerHTML = `<span class="stars-container">${stars}</span>`;

                  // إذا كان المستخدم صاحب عمل، فعّل التقييم
                  if (userInfo.role === 'employer' && userInfo.user_id != pid) {
                    const starSpans = div.querySelectorAll('.star');
                    let selected = 0;
                    // Hover effect
                    starSpans.forEach((star, idx) => {
                      star.addEventListener('mouseenter', function() {
                        starSpans.forEach((s, i) => {
                          s.style.color = i <= idx ? '#FFD700' : '#ccc';
                        });
                      });
                      star.addEventListener('mouseleave', function() {
                        starSpans.forEach((s, i) => {
                          s.style.color = i < selected ? '#FFD700' : '#ccc';
                        });
                      });
                      // Click to rate
                      star.addEventListener('click', function() {
                        selected = idx + 1;
                        // إرسال التقييم للخادم
                        fetch('rate_professional.php', {
                          method: 'POST',
                          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                          body: `professional_id=${pid}&rating=${selected}`
                        })
                        .then(res => res.json())
                        .then(result => {
                          if (result.success) {
                            starSpans.forEach((s, i) => {
                              s.style.color = i < selected ? '#FFD700' : '#ccc';
                            });
                            alert('تم حفظ تقييمك بنجاح!');
                          } else if(result.error === 'already_rated') {
                            alert('لقد قمت بتقييم هذا المهني مسبقاً.');
                          } else if(result.error === 'not_allowed') {
                            alert('يمكنك تقييم المهني فقط بعد التعامل معه.');
                          } else {
                            alert('حدث خطأ أثناء حفظ التقييم.');
                          }
                        });
                      });
                    });
                    // إعادة لون النجوم بعد الخروج من hover
                    div.addEventListener('mouseleave', function() {
                      starSpans.forEach((s, i) => {
                        s.style.color = i < avg ? '#FFD700' : '#ccc';
                      });
                    });
                  }
                })
                .catch(() => {
                  div.innerHTML = '<span style="color:#888;">لا يوجد تقييم</span>';
                });
            });
        });

        var btnExplore = document.getElementById('btn-explore-jobs');
        if (btnExplore) {
          btnExplore.addEventListener('click', function() {
            // جلب الوظائف الجديدة (آخر 5 وظائف مضافة)
            fetch('get_jobs.php?latest=1')
              .then(res => res.json())
              .then(jobs => {
                var html = '';
                if (jobs.length === 0) {
                  html = '<p>لا توجد وظائف جديدة حالياً.</p>';
                } else {
                  jobs.forEach(job => {
                    html += `<div style='border-bottom:1px solid #eee;padding:10px 0;'>
                      <strong>${job.title}</strong><br>
                      <span style='color:#888;'>${job.location}</span><br>
                      <span style='font-size:0.9em;'>${job.created_at || ''}</span>
                      <p style='margin:5px 0 0;'>${job.description || ''}</p>
                    </div>`;
                  });
                }
                document.getElementById('new-jobs-list').innerHTML = html;
                document.getElementById('new-jobs-modal').style.display = 'flex';
              })
              .catch(() => {
                document.getElementById('new-jobs-list').innerHTML = '<p>تعذر جلب الوظائف الجديدة.</p>';
                document.getElementById('new-jobs-modal').style.display = 'flex';
              });
          });
        }
      });
    </script>
  </body>
</html>