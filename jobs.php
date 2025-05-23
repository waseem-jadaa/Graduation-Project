<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$searchTerm = htmlspecialchars($searchTerm);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الوظائف</title>
    <link rel="stylesheet" href="css/project.css">
    <script>
        async function getUserRole() {
            // جلب نوع الحساب من السيرفر
            const res = await fetch('get_user_role.php');
            if (res.ok) {
                const data = await res.json();
                return data.role;
            }
            return null;
        }
        async function fetchJobs() {
            try {
                const response = await fetch(`get_jobs.php?search=${encodeURIComponent('<?php echo $searchTerm; ?>')}`);
                const jobs = await response.json();
                const jobsContainer = document.getElementById('jobs-container');
                jobsContainer.innerHTML = '';

                if (jobs.length === 0) {
                    jobsContainer.innerHTML = '<p>لا توجد نتائج مطابقة.</p>';
                    return;
                }


                jobs.forEach(job => {
                    const jobBox = document.createElement('div');
                    jobBox.className = 'job-card';
                    jobBox.innerHTML = `
                        <div class="job-header">
                            <img src="https://img.icons8.com/color/48/briefcase--v1.png" alt="وظائف عامة" class="company-logo">
                            <div class="job-title">
                                <h3>${job.title}</h3>
                                <p>${job.description}</p>
                            </div>
                            <div class="job-save" data-job-id="${job.job_ID}" data-saved="${job.saved ? '1' : '0'}">
                                <i class="${job.saved ? 'fas' : 'far'} fa-bookmark"></i>
                            </div>
                        </div>
                        <div class="job-details">
                            <p><i class="fas fa-map-marker-alt"></i> ${job.location}</p>
                            <p><i class="fas fa-money-bill-wave"></i> ${job.salary}</p>
                        </div>
                        <div class="job-actions">
                            <button class="btn-apply" data-job-id="${job.job_ID}">تقدم الآن</button>
                            <button class="btn-details">التفاصيل</button>
                        </div>
                    `;
                    jobsContainer.appendChild(jobBox);
                });

                // إضافة منطق الحفظ/إزالة الحفظ للوظائف
                document.querySelectorAll('.job-save').forEach(btn => {
                    // تفعيل اللون عند الحفظ
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
                                    btn.dataset.saved = '0';
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
                                    btn.dataset.saved = '1';
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

                // إضافة منطق التقديم على الوظيفة
                setTimeout(async () => {
                    const userRole = await getUserRole();
                    document.querySelectorAll('.btn-apply').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            if (userRole === 'employer') {
                                e.preventDefault();
                                alert('لا يمكنك التقديم على الوظائف انت صاحب عمل وليس باحث عن عمل');
                                return;
                            }
                            const jobId = this.getAttribute('data-job-id');
                            this.disabled = true;
                            fetch('apply_job.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'job_id=' + encodeURIComponent(jobId)
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.textContent = 'تم التقديم';
                                    this.classList.add('applied');
                                    alert('تم إرسال طلبك بنجاح! سيتم إشعار صاحب العمل.');
                                } else if (data.error === 'already_applied') {
                                    this.textContent = 'تم التقديم مسبقاً';
                                    alert('لقد تقدمت لهذه الوظيفة مسبقاً.');
                                } else {
                                    alert('حدث خطأ أثناء التقديم.');
                                }
                            })
                            .catch(() => {
                                alert('حدث خطأ في الاتصال بالخادم.');
                            })
                            .finally(() => {
                                this.disabled = false;
                            });
                        });
                    });
                }, 500);
            } catch (error) {
                console.error('Error fetching jobs:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchJobs);
    </script>
</head>
<body class="dashboard-page">
    <?php include 'headerDash.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <h1>الوظائف المتاحة</h1>
            <div id="jobs-container" class="jobs-container">
            </div>
        </div>
    </main>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var userProfile = document.querySelector('.user-profile');
      if (userProfile) {
        userProfile.addEventListener('click', function(e) {
          this.classList.toggle('active');
          e.stopPropagation();
        });
        document.addEventListener('click', function() {
          userProfile.classList.remove('active');
        });
      }
    });
    </script>
</body>
</html>