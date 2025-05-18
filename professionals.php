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
    <title>المهنيين</title>
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/rating.css">
    <link rel="stylesheet" href="css/request-btn.css">
    <script>
        async function getUserRoleAndId() {
            const res = await fetch('get_user_role.php');
            if (res.ok) {
                const data = await res.json();
                return data;
            }
            return {role: null, user_id: null};
        }
        async function fetchProfessionals() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const searchTerm = urlParams.get('search') || '';
                const response = await fetch(`get_professionals.php?search=${encodeURIComponent(searchTerm)}`);
                const professionals = await response.json();
                const professionalsContainer = document.getElementById('professionals-container');
                professionalsContainer.innerHTML = '';
                const userInfo = await getUserRoleAndId();

                if (professionals.length === 0) {
                    professionalsContainer.innerHTML = '<p>لا توجد نتائج مطابقة.</p>';
                    return;
                }

                professionals.forEach(async professional => {
                    const professionalBox = document.createElement('div');
                    professionalBox.className = 'job-box';
                    // إضافة عنصر التقييم بالنجوم
                    let ratingHtml = '<div class="rating-stars" data-professional-id="' + professional.User_ID + '"><span>جاري التحميل...</span></div>';
                    professionalBox.innerHTML = `
                        <h3>${professional.name}</h3>
                        <p>${professional.profession}</p>
                        <p><strong>الموقع:</strong> ${professional.location}</p>
                        <p><strong>الخبرة:</strong> ${professional.experience} سنوات</p>
                        ${ratingHtml}
                        <button class="request-btn">اطلبه الآن</button>
                    `;
                    // جلب التقييم من الخادم وإظهار واجهة تفاعلية للتقييم إذا كان المستخدم صاحب عمل
                    const ratingDiv = professionalBox.querySelector('.rating-stars');
                    fetch('get_professional_rating.php?professional_id=' + professional.User_ID)
                        .then(res => res.json())
                        .then(data => {
                            let avg = data && typeof data.avg_rating !== 'undefined' ? Math.round(data.avg_rating) : 0;
                            let stars = '';
                            for (let i = 1; i <= 5; i++) {
                                stars += `<span class="star" data-star="${i}" style="color:${i <= avg ? '#FFD700' : '#ccc'};font-size:1.3em;cursor:pointer;">★</span>`;
                            }
                            ratingDiv.innerHTML = `<span class="stars-container">${stars}</span>`;

                            // إذا كان المستخدم صاحب عمل، فعّل التقييم
                            if (userInfo.role === 'employer' && userInfo.user_id != professional.User_ID) {
                                const starSpans = ratingDiv.querySelectorAll('.star');
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
                                            body: `professional_id=${professional.User_ID}&rating=${selected}`
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
                                ratingDiv.addEventListener('mouseleave', function() {
                                    starSpans.forEach((s, i) => {
                                        s.style.color = i < avg ? '#FFD700' : '#ccc';
                                    });
                                });
                            }
                        })
                        .catch(() => {
                            ratingDiv.innerHTML = '<span style="color:#888;">لا يوجد تقييم</span>';
                        });
                    // منطق الزر
                    const btn = professionalBox.querySelector('.request-btn');
                    if (userInfo.role === 'employer' && userInfo.user_id != professional.User_ID) {
                        btn.disabled = false;
                        btn.addEventListener('click', function() {
                            fetch('request_professional.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: 'professional_id=' + encodeURIComponent(professional.User_ID)
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
                        });
                    } else if (userInfo.role === 'employer' && userInfo.user_id == professional.User_ID) {
                        btn.disabled = true;
                        btn.textContent = 'لا يمكنك طلب نفسك';
                    } else if (userInfo.role === 'job_seeker') {
                        btn.disabled = false;
                        btn.addEventListener('click', function() {
                            alert('لا يمكنك طلب مهني فانت مهني بالاصل');
                        });
                    } else {
                        btn.disabled = true;
                        btn.textContent = 'غير متاح';
                    }
                    professionalsContainer.appendChild(professionalBox);
                });
            } catch (error) {
                console.error('Error fetching professionals:', error);
            }
        }
        document.addEventListener('DOMContentLoaded', fetchProfessionals);
    </script>
</head>
<body class="dashboard-page">
    <?php include 'headerDash.php'; ?>
    
    <main class="main-content">
        <div class="container">
            <h1>المهنيين المتاحين</h1>
            <div id="professionals-container" class="jobs-container">
                
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
