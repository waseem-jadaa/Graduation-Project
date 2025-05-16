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
    <style>
        .request-btn {
            background: #1abc5b;
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 10px 28px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #0001;
        }
        .request-btn:disabled {
            background: #ccc;
            color: #888;
            cursor: not-allowed;
        }
        .request-btn:hover:not(:disabled) {
            background: #159c48;
            box-shadow: 0 4px 16px #0002;
        }
    </style>
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
                const response = await fetch('get_professionals.php');
                const professionals = await response.json();
                const professionalsContainer = document.getElementById('professionals-container');
                professionalsContainer.innerHTML = '';
                const userInfo = await getUserRoleAndId();
                professionals.forEach(professional => {
                    const professionalBox = document.createElement('div');
                    professionalBox.className = 'job-box';
                    professionalBox.innerHTML = `
                        <h3>${professional.name}</h3>
                        <p>${professional.profession}</p>
                        <p><strong>الموقع:</strong> ${professional.location}</p>
                        <p><strong>الخبرة:</strong> ${professional.experience} سنوات</p>
                        <button class="request-btn">اطلبه الآن</button>
                    `;
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
