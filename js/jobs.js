async function getUserRole() {
    const res = await fetch('get_user_role.php');
    if (res.ok) {
        const data = await res.json();
        return data.role;
    }
    return null;
}

async function fetchJobs(page = 1) {
    try {
        const response = await fetch(`get_jobs.php?search=${encodeURIComponent(searchTerm)}&page=${page}`);
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
            const isSaved = job.saved && Number(job.saved) > 0;
            jobBox.innerHTML = `
                <div class="job-header">
                    <img src="https://img.icons8.com/color/48/briefcase--v1.png" alt="وظائف عامة" class="company-logo">
                    <div class="job-title">
                        <h3>${job.title}</h3>
                    </div>
                    <div class="job-save">
                        <i class="${isSaved ? 'fas' : 'far'} fa-bookmark" data-job-id="${job.job_ID}" style="${isSaved ? 'color:#27ae60' : ''}"></i>
                    </div>
                </div>
                <div class="job-details">
                    <p><i class="fas fa-map-marker-alt"></i> ${job.location}</p>
                    <p><i class="fas fa-money-bill-wave"></i> ${job.salary}</p>
                    <p><i class="fas fa-building"></i> ${job.employer_name}</p>
                </div>
                <div class="job-details-full">
                    <h4>تفاصيل الوظيفة:</h4>
                    <p>${job.description}</p>
                </div>
                <div class="job-actions">
                    ${job.status === 'filled' ? 
                        '<button class="btn-apply" disabled style="background-color: #888;">نفذت</button>' : 
                        `<button class="btn-apply" data-job-id="${job.job_ID}">تقدم الآن</button>`}
                    <button class="btn-details" onclick="toggleDetails(this)">التفاصيل</button>
                </div>
            `;
            jobsContainer.appendChild(jobBox);
        });

        setupJobSaveListeners();
        setupJobApplyListeners();
        renderPagination(currentPage, totalPages);
    } catch (error) {
        console.error('Error fetching jobs:', error);
    }
}

function setupJobSaveListeners() {
    document.querySelectorAll('.job-save i').forEach(icon => {
        icon.addEventListener('click', function(e) {
            const jobId = this.getAttribute('data-job-id');
            const isSaved = this.classList.contains('fas');
            
            if (isSaved) {
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
                        this.classList.remove('fas');
                        this.classList.add('far');
                        this.style.color = '';
                        alert('تم إزالة الوظيفة من المحفوظات بنجاح!');
                    } else if (data.action === 'saved') {
                        this.classList.remove('far');
                        this.classList.add('fas');
                        this.style.color = '#27ae60';
                        alert('تم حفظ الوظيفة بنجاح!');
                    }
                } else if (data.error === 'already_saved') {
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
}

function setupJobApplyListeners() {
    const jobsContainer = document.getElementById('jobs-container');
    jobsContainer.addEventListener('click', async function(event) {
        const btn = event.target.closest('.btn-apply');
        if (!btn || btn.disabled) return;

        const userRole = await getUserRole();
        if (userRole === 'employer') {
            event.preventDefault();
            alert('لا يمكنك التقديم على الوظائف انت صاحب عمل وليس باحث عن عمل');
            return;
        }

        const jobId = btn.getAttribute('data-job-id');
        if (!jobId) {
            alert('معرف الوظيفة غير متوفر. يرجى مراجعة الإدارة.');
            return;
        }

        btn.disabled = true;
        btn.style.pointerEvents = 'none';

        try {
            const response = await fetch('apply_job.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'job_id=' + encodeURIComponent(jobId)
            });
            
            const data = await response.json();
            
            if (data.error === 'already_applied') {
                btn.textContent = 'تم التقديم مسبقاً';
            } else if (data.success) {
                btn.textContent = 'تم التقديم';
                alert('تم إرسال طلبك بنجاح! سيتم إشعار صاحب العمل.');
            } else if (data.error) {
                alert('خطأ أثناء التقديم: ' + data.error);
                btn.disabled = false;
                btn.style.pointerEvents = 'auto';
            } else {
                alert('حدث خطأ غير معروف أثناء التقديم.');
                btn.disabled = false;
                btn.style.pointerEvents = 'auto';
            }
        } catch (error) {
            alert('حدث خطأ في الاتصال بالخادم أو استجابة غير صالحة.');
            btn.disabled = false;
            btn.style.pointerEvents = 'auto';
        }
    });
}

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

    nav.appendChild(createPageBtn('الأول', 1, current === 1));
    nav.appendChild(createPageBtn('السابق', current - 1, current === 1));

    let start = Math.max(1, current - 2);
    let end = Math.min(total, current + 2);
    if (current <= 3) end = Math.min(5, total);
    if (current >= total - 2) start = Math.max(1, total - 4);
    for (let i = start; i <= end; i++) {
        nav.appendChild(createPageBtn(i, i, i === current, true));
    }

    nav.appendChild(createPageBtn('التالي', current + 1, current === total));
    nav.appendChild(createPageBtn('الأخير', total, current === total));

    container.appendChild(nav);
}

function createPageBtn(text, page, disabled, isNumber) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pagination-btn';
    btn.textContent = text;
    
    const isNavBtn = ['الأول', 'السابق', 'التالي', 'الأخير'].includes(text);
    
    if (isNumber && disabled) {
        btn.classList.add('active-page');
        btn.disabled = true;
    } 
    else if (isNavBtn && disabled) {
        btn.disabled = true;
        btn.style.color = '#ccc';
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

function toggleDetails(button) {
    const detailsDiv = button.closest('.job-card').querySelector('.job-details-full');
    detailsDiv.classList.toggle('show');
    button.textContent = detailsDiv.classList.contains('show') ? 'إخفاء التفاصيل' : 'التفاصيل';
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    fetchJobs();
}); 