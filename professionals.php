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
    <link rel="stylesheet" href="project.css">
    <script>
        async function fetchProfessionals() {
            try {
                const response = await fetch('get_professionals.php');
                const professionals = await response.json();
                const professionalsContainer = document.getElementById('professionals-container');
                professionalsContainer.innerHTML = '';

                professionals.forEach(professional => {
                    const professionalBox = document.createElement('div');
                    professionalBox.className = 'job-box';
                    professionalBox.innerHTML = `
                        <h3>${professional.name}</h3>
                        <p>${professional.profession}</p>
                        <p><strong>الموقع:</strong> ${professional.location}</p>
                        <p><strong>التقييم:</strong> ${professional.rating}</p>
                    `;
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
