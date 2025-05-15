<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الوظائف</title>
    <link rel="stylesheet" href="project.css">
    <script>
        async function fetchJobs() {
            try {
                const response = await fetch('get_jobs.php');
                const jobs = await response.json();
                const jobsContainer = document.getElementById('jobs-container');
                jobsContainer.innerHTML = '';

                jobs.forEach(job => {
                    const jobBox = document.createElement('div');
                    jobBox.className = 'job-card';
                    jobBox.innerHTML = `
                        <div class="job-header">
                            <img src="image/n.png" alt="شعار الشركة" class="company-logo">
                            <div class="job-title">
                                <h3>${job.title}</h3>
                                <p>${job.description}</p>
                            </div>
                            <div class="job-save">
                                <i class="far fa-bookmark"></i>
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

                // إضافة منطق التقديم على الوظيفة
                setTimeout(() => {
                    document.querySelectorAll('.btn-apply').forEach(btn => {
                        btn.addEventListener('click', function() {
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