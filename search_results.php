<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';
// جلب بيانات المستخدم
if (!isset($user_name) || !isset($profile_photo)) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            $stmt = $conn->prepare('SELECT u.name, p.profile_photo FROM user u LEFT JOIN profile p ON u.User_ID = p.User_ID WHERE u.User_ID = :user_id');
            $stmt->execute([':user_id' => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_name = $row && isset($row['name']) ? htmlspecialchars($row['name']) : 'مستخدم';
            $profile_photo = $row && !empty($row['profile_photo']) ? $row['profile_photo'] : 'image/p.png';
        } catch (PDOException $e) {
            $user_name = 'مستخدم';
            $profile_photo = 'image/p.png';
        }
    } else {
        $user_name = 'مستخدم';
        $profile_photo = 'image/p.png';
    }
}
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchTerm = htmlspecialchars($searchTerm);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج البحث</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/headerDash.css">
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/search_result.css">
    <link rel="stylesheet" href="css/rating.css">
    <link rel="stylesheet" href="css/request-btn.css">
    <script src="notification.js"></script>
</head>
<body class="dashboard-page">
<?php include 'headerDash.php'; ?>
<main class="main-content">
    <div class="search-results-container">
        <h1>نتائج البحث عن: <?php echo $searchTerm; ?></h1>
        <div id="no-results">لا توجد نتائج مطابقة.</div>
        <section id="jobs-section">
            <h2 id="jobs-title"><i class="fas fa-briefcase"></i> الوظائف</h2>
            <div id="jobs-results" class="jobs-container"></div>
        </section>
        <section id="professionals-section">
            <h2 id="professionals-title"><i class="fas fa-user-tie"></i> المهنيون</h2>
            <div id="professionals-results" class="jobs-container"></div>
        </section>
    </div>
</main>
<script>
        async function fetchSearchResults() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const searchTerm = urlParams.get('search') || '';
                const response = await fetch(`search_all.php?search=${encodeURIComponent(searchTerm)}`);
                const results = await response.json();
                const jobsContainer = document.getElementById('jobs-results');
                const professionalsContainer = document.getElementById('professionals-results');
                const jobsSection = document.getElementById('jobs-section');
                const professionalsSection = document.getElementById('professionals-section');
                jobsContainer.innerHTML = '';
                professionalsContainer.innerHTML = '';

                const jobResults = results.filter(r => r.type === 'job');
                const professionalResults = results.filter(r => r.type === 'professional');

                if (jobResults.length === 0 && professionalResults.length === 0) {
                    document.getElementById('no-results').style.display = 'block';
                } else {
                    document.getElementById('no-results').style.display = 'none';
                }

                // إظهار/إخفاء أقسام النتائج حسب النتائج
                if (jobResults.length === 0) {
                    jobsSection.style.display = 'none';
                } else {
                    jobsSection.style.display = '';
                    jobResults.forEach(job => {
                        const jobBox = document.createElement('div');
                        jobBox.className = 'job-box';
                        jobBox.innerHTML = `
                            <h3><i class='fas fa-briefcase'></i> ${job.title}</h3>
                            <p>${job.description}</p>
                            <p><strong>الموقع:</strong> ${job.location}</p>
                            <p><strong>الراتب:</strong> ${job.salary}</p>
                        `;
                        jobsContainer.appendChild(jobBox);
                    });
                }

                if (professionalResults.length === 0) {
                    professionalsSection.style.display = 'none';
                } else {
                    professionalsSection.style.display = '';
                    professionalResults.forEach(professional => {
                        const professionalBox = document.createElement('div');
                        professionalBox.className = 'job-box';
                        professionalBox.innerHTML = `
                            <h3><i class='fas fa-user-tie'></i> ${professional.name}</h3>
                            <p>${professional.profession}</p>
                            <p><strong>الموقع:</strong> ${professional.location}</p>
                            <p><strong>الخبرة:</strong> ${professional.experience}</p>
                        `;
                        professionalsContainer.appendChild(professionalBox);
                    });
                }
            } catch (error) {
                console.error('Error fetching search results:', error);
            }
        }
        document.addEventListener('DOMContentLoaded', fetchSearchResults);
    </script>
</body>
</html>
