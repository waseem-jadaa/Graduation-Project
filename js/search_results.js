// إعدادات التقسيم للصفحات (عدد النتائج الكلي في الصفحة بغض النظر عن النوع)
const RESULTS_PER_PAGE = 1; // يمكنك تغيير هذا الرقم حسب رغبتك

let allResults = [];
let currentPage = 1;

// دالة لجلب نوع الحساب من السيرفر
async function getUserRole() {
    try {
        const response = await fetch('get_user_role.php');
        const data = await response.json();
        return data.role;
    } catch (error) {
        console.error('Error fetching user role:', error);
        return null;
    }
}

async function getUserId() {
    try {
        const response = await fetch('get_user_role.php');
        const data = await response.json();
        return data.user_id;
    } catch (error) {
        console.error('Error fetching user ID:', error);
        return null;
    }
}

function renderPagination(total, perPage, currentPage, onPageChange, wrapperId) {
    const totalPages = Math.ceil(total / perPage);
    const wrapper = document.getElementById(wrapperId);
    wrapper.innerHTML = '';
    if (totalPages <= 1) {
        wrapper.style.display = 'none';
        return;
    }
    wrapper.style.display = 'flex';
    const nav = document.createElement('nav');
    nav.className = 'pagination-nav';

    // زر الأول
    const firstBtn = document.createElement('button');
    firstBtn.className = 'pagination-btn';
    firstBtn.textContent = 'الأول';
    firstBtn.disabled = currentPage === 1;
    firstBtn.onclick = () => onPageChange(1);
    nav.appendChild(firstBtn);

    // زر السابق
    const prevBtn = document.createElement('button');
    prevBtn.className = 'pagination-btn';
    prevBtn.textContent = 'السابق';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => onPageChange(currentPage - 1);
    nav.appendChild(prevBtn);

    // أرقام الصفحات (عرض أول صفحتين، آخر صفحتين، وحول الصفحة الحالية)
    let pageNumbers = [];
    for (let i = 1; i <= totalPages; i++) {
        if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - 1 && i <= currentPage + 1) ||
            (currentPage <= 3 && i <= 3) ||
            (currentPage >= totalPages - 2 && i >= totalPages - 2)
        ) {
            pageNumbers.push(i);
        } else if (
            (i === currentPage - 2 && currentPage > 4) ||
            (i === currentPage + 2 && currentPage < totalPages - 3)
        ) {
            pageNumbers.push('...');
        }
    }
    // إزالة التكرار في ...
    pageNumbers = pageNumbers.filter((v, i, a) => v === '...' ? a[i - 1] !== '...' : true);

    pageNumbers.forEach(num => {
        if (num === '...') {
            const span = document.createElement('span');
            span.className = 'pagination-btn';
            span.textContent = '...';
            span.style.cursor = 'default';
            nav.appendChild(span);
        } else {
            const btn = document.createElement('button');
            btn.className = 'pagination-btn' + (num === currentPage ? ' active-page' : '');
            btn.textContent = num;
            btn.disabled = num === currentPage;
            btn.onclick = () => onPageChange(num);
            nav.appendChild(btn);
        }
    });

    // زر التالي
    const nextBtn = document.createElement('button');
    nextBtn.className = 'pagination-btn';
    nextBtn.textContent = 'التالي';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.onclick = () => onPageChange(currentPage + 1);
    nav.appendChild(nextBtn);

    // زر الأخير
    const lastBtn = document.createElement('button');
    lastBtn.className = 'pagination-btn';
    lastBtn.textContent = 'الأخير';
    lastBtn.disabled = currentPage === totalPages;
    lastBtn.onclick = () => onPageChange(totalPages);
    nav.appendChild(lastBtn);

    wrapper.appendChild(nav);
}

function renderResultsPage(page) {
    const jobsContainer = document.getElementById('jobs-results');
    const professionalsContainer = document.getElementById('professionals-results');
    jobsContainer.innerHTML = '';
    professionalsContainer.innerHTML = '';

    // تقسيم النتائج لهذه الصفحة
    const start = (page - 1) * RESULTS_PER_PAGE;
    const end = start + RESULTS_PER_PAGE;
    const pageResults = allResults.slice(start, end);

    // عرض النتائج حسب النوع
    let jobsCount = 0;
    let professionalsCount = 0;
    pageResults.forEach(result => {
        if (result.type === 'job') {
            jobsCount++;
            const jobBox = document.createElement('div');
            jobBox.className = 'job-box';
            jobBox.innerHTML = `
                <div class="job-header">
                    <img src="https://img.icons8.com/color/48/briefcase--v1.png" alt="وظائف عامة" class="company-logo">
                    <div class="job-title">
                        <h3>${result.title}</h3>
                    </div>
                </div>
                <div class="job-details">
                    <p><i class="fas fa-map-marker-alt"></i> ${result.location}</p>
                    <p><i class="fas fa-money-bill-wave"></i> ${result.salary}</p>
                    <p><i class="fas fa-building"></i> ${result.employer_name}</p>
                </div>
                <div class="job-details-full">
                    <h4>تفاصيل الوظيفة:</h4>
                    <p>${result.description}</p>
                </div>
                <div class="job-actions">
                    <button class="btn-apply" data-job-id="${result.job_ID}">تقدم الآن</button>
                    <button class="btn-details" onclick="toggleDetails(this)">التفاصيل</button>
                </div>
            `;
            jobsContainer.appendChild(jobBox);
        } else if (result.type === 'professional') {
            professionalsCount++;
            const professionalBox = document.createElement('div');
            professionalBox.className = 'job-box';
            professionalBox.innerHTML = `
                <div class="job-header">
                    <img src="${result.profile_photo || 'image/p.png'}" alt="صورة الحساب" class="company-logo">
                    <div class="job-title">
                        <h3>${result.name}</h3>
                        <p>${result.profession}</p>
                    </div>
                </div>
                <div class="job-details">
                    <p><i class="fas fa-map-marker-alt"></i> ${result.location}</p>
                    <p><i class="fas fa-briefcase"></i> خبرة: ${result.experience} سنوات</p>
                </div>
                <div class="professional-actions">
                    <button class="request-btn" data-professional-id="${result.id}">اطلبه الآن</button>
                    <a href="professional_details.php?id=${result.id}" class="btn-details">التفاصيل</a>
                </div>
            `;

            const btn = professionalBox.querySelector('.request-btn');
            // إضافة منطق منع صاحب العمل من التقديم
            (async () => {
                const userRole = await getUserRole();
                const userId = await getUserId();

                if (userRole === 'employer' && userId != result.User_ID) {
                    btn.disabled = false;
                    btn.addEventListener('click', function() {
                        btn.disabled = true;
                        fetch('request_professional.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'professional_id=' + encodeURIComponent(result.id)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                btn.textContent = 'تم إرسال الطلب';
                                btn.classList.add('applied');
                                alert('تم إرسال طلبك لهذا المهني!');
                            } else if (data.error === 'already_requested') {
                                btn.textContent = 'تم الطلب مسبقاً';
                                alert('لقد أرسلت طلباً لهذا المهني مسبقاً.');
                            } else if (data.error === 'unauthorized') {
                                alert('يجب تسجيل الدخول لطلب المهنيين.');
                                window.location.href = 'login.php';
                            } else {
                                alert('حدث خطأ أثناء إرسال الطلب.');
                                btn.disabled = false;
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('حدث خطأ في الاتصال بالخادم.');
                            btn.disabled = false;
                        });
                    });
                } else if (userRole === 'employer' && userId == result.User_ID) {
                    btn.disabled = true;
                    btn.textContent = 'لا يمكنك طلب نفسك';
                } else if (userRole === 'job_seeker') {
                    btn.disabled = true;
                    btn.textContent = 'غير متاح للمهنيين';
                } else {
                    btn.disabled = true;
                    btn.textContent = 'غير متاح';
                }
            })();

            professionalsContainer.appendChild(professionalBox);
        }
    });

    // إضافة منطق منع صاحب العمل من التقديم
    setTimeout(async () => {
        const userRole = await getUserRole();
        const userId = await getUserId();

        // منطق زر التقديم على الوظائف
        document.querySelectorAll('.btn-apply').forEach(function(btn) {
            if (userRole === 'employer') {
                btn.disabled = true;
                btn.textContent = 'غير متاح لاصحاب العمل';
            } else if (userRole === 'job_seeker') {
                btn.disabled = false;
                btn.textContent = 'تقدم الآن';
                btn.addEventListener('click', function(e) {
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
                        } else if (data.error === 'unauthorized') {
                            alert('يجب تسجيل الدخول للتقديم على الوظائف.');
                            window.location.href = 'login.php';
                        } else {
                            console.error('Error details:', data);
                            alert('حدث خطأ أثناء التقديم: ' + (data.error || 'خطأ غير معروف'));
                            btn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
                        btn.disabled = false;
                    });
                });
            } else {
                btn.disabled = true;
                btn.textContent = 'غير متاح';
            }
        });

        // إظهار أو إخفاء أقسام النتائج حسب وجود نتائج في الصفحة الحالية
        document.getElementById('jobs-section').style.display = jobsCount > 0 ? '' : 'none';
        document.getElementById('professionals-section').style.display = professionalsCount > 0 ? '' : 'none';

        // شريط التنقل في أسفل الصفحة فقط
        renderPagination(
            allResults.length,
            RESULTS_PER_PAGE,
            page,
            (newPage) => {
                currentPage = newPage;
                renderResultsPage(newPage);
            },
            'pagination-bottom-wrapper'
        );
    }, 300);
}

async function fetchSearchResults() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const searchTerm = urlParams.get('search') || '';
        const response = await fetch(`search_all.php?search=${encodeURIComponent(searchTerm)}`);
        const results = await response.json();
        allResults = results;
        currentPage = 1;

        if (allResults.length === 0) {
            document.getElementById('no-results').style.display = 'block';
            document.getElementById('jobs-section').style.display = 'none';
            document.getElementById('professionals-section').style.display = 'none';
            document.getElementById('pagination-bottom-wrapper').style.display = 'none';
        } else {
            document.getElementById('no-results').style.display = 'none';
            renderResultsPage(currentPage);
        }
    } catch (error) {
        console.error('Error fetching search results:', error);
    }
}

function toggleDetails(button) {
    const detailsDiv = button.closest('.job-box').querySelector('.job-details-full');
    detailsDiv.classList.toggle('show');
    button.textContent = detailsDiv.classList.contains('show') ? 'إخفاء التفاصيل' : 'التفاصيل';
}

document.addEventListener('DOMContentLoaded', fetchSearchResults); 