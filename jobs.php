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
        async function fetchJobs(page = 1) {
    try {
        const response = await fetch(`get_jobs.php?search=${encodeURIComponent('<?php echo $searchTerm; ?>')}&page=${page}`);
        const data = await response.json();
        const jobs = data.jobs || data;
        const totalPages = data.totalPages || 1;
        const currentPage = data.currentPage || 1;
        const jobsContainer = document.getElementById('jobs-container');
        jobsContainer.innerHTML = '';

        if (jobs.length === 0) {
            jobsContainer.innerHTML = '<p>لا توجد نتائج مطابقة.</p>';
            renderPagination(1, 1);
            return;
        }

        jobs.forEach(job => {
            const jobBox = document.createElement('div');
            jobBox.className = 'job-card';
            // تحديد حالة الحفظ
            const isSaved = job.saved && Number(job.saved) > 0;
            jobBox.innerHTML = `
                <div class="job-header">
                    <img src="https://img.icons8.com/color/48/briefcase--v1.png" alt="وظائف عامة" class="company-logo">
                    <div class="job-title">
                        <h3>${job.title}</h3>
                        <p>${job.description}</p>
                    </div>
                    <div class="job-save">
                        <i class="${isSaved ? 'fas' : 'far'} fa-bookmark" data-job-id="${job.job_ID}" style="${isSaved ? 'color:#27ae60' : ''}"></i>
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

        // إضافة منطق الحفظ للوظيفة
        document.querySelectorAll('.job-save i').forEach(icon => {
            icon.addEventListener('click', function(e) {
                const jobId = this.getAttribute('data-job-id');
                const isSaved = this.classList.contains('fas');
                
                if (isSaved) {
                    // إذا كانت الوظيفة محفوظة، نطلب تأكيد الإزالة
                    if (!confirm('هل أنت متأكد من إزالة هذه الوظيفة من المحفوظات؟')) {
                        return;
                    }
                }

                fetch('save_job.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'job_id=' + encodeURIComponent(jobId)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.action === 'removed') {
                            // تم الإزالة بنجاح
                            this.classList.remove('fas');
                            this.classList.add('far');
                            this.style.color = '';
                            alert('تم إزالة الوظيفة من المحفوظات بنجاح!');
                        } else if (data.action === 'saved') {
                            // تم الحفظ بنجاح
                            this.classList.remove('far');
                            this.classList.add('fas');
                            this.style.color = '#27ae60'; // لون الحفظ
                            alert('تم حفظ الوظيفة بنجاح!');
                        }
                    } else if (data.error === 'already_saved') {
                         // هذا الشرط لم يعد يجب ان يحدث مع التعديل الجديد في save_job.php
                        alert('لقد قمت بحفظ هذه الوظيفة مسبقاً.');
                    } else {
                        alert('حدث خطأ أثناء الحفظ/الإزالة.');
                    }
                })
                .catch(() => {
                    alert('حدث خطأ في الاتصال بالخادم.');
                });
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

        renderPagination(currentPage, totalPages);
    } catch (error) {
        console.error('Error fetching jobs:', error);
    }
}

// شريط التنقل بين الصفحات
function renderPagination(current, total) {
    let container = document.getElementById('pagination-container');
    if (!container) return;
    container.innerHTML = '';
    if (total <= 1) return;

    const nav = document.createElement('nav');
    nav.className = 'pagination-nav';
    nav.dir = 'rtl';
    nav.style.display = 'flex';
    nav.style.justifyContent = 'center';
    nav.style.alignItems = 'center';
    nav.style.flexWrap = 'wrap';
    nav.style.gap = '0';

    // زر \"الأول\"
    nav.appendChild(createPageBtn('الأول', 1, current === 1));
    // زر \"السابق\"
    nav.appendChild(createPageBtn('السابق', current - 1, current === 1));

    // أرقام الصفحات (عرض 5 صفحات كحد أقصى)
    let start = Math.max(1, current - 2);
    let end = Math.min(total, current + 2);
    if (current <= 3) end = Math.min(5, total);
    if (current >= total - 2) start = Math.max(1, total - 4);
    for (let i = start; i <= end; i++) {
        nav.appendChild(createPageBtn(i, i, i === current, true));
    }

    // زر \"التالي\"
    nav.appendChild(createPageBtn('التالي', current + 1, current === total));
    // زر \"الأخير\"
    nav.appendChild(createPageBtn('الأخير', total, current === total));

    container.appendChild(nav);
}

function createPageBtn(text, page, disabled, isNumber) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pagination-btn';
    btn.textContent = text;
    
    // تحديد ما إذا كان الزر هو زر تنقل (الأول، السابق، التالي، الأخير)
    const isNavBtn = ['الأول', 'السابق', 'التالي', 'الأخير'].includes(text);
    
    if (isNumber && disabled) {
        btn.classList.add('active-page');
        btn.disabled = true;
    } 
    else if (isNavBtn && disabled) {
        btn.disabled = true;
        btn.style.color = '#ccc'; // تعطيل اللون للزر
        btn.style.cursor = 'not-allowed';
    }
    
    if (!disabled) {
        btn.addEventListener('click', function() {
            fetchJobs(page);
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    }
    return btn;
}

document.addEventListener('DOMContentLoaded', function() {
    fetchJobs();
});

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
    <!-- شريط التنقل بين الصفحات في أسفل الصفحة -->
<div id="pagination-container-wrapper" style="width:100%;display:flex;justify-content:center;position:fixed;bottom:0;left:0;z-index:100;background:#fff;padding:0;box-shadow:none;border-top:1px solid #eee;">
    <div id="pagination-container" style="text-align:center;width:100%;"></div>
</div>
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
    <link rel="stylesheet" href="css/pagination.css">
</body>
</html>