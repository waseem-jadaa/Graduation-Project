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
                    jobBox.className = 'job-box';
                    jobBox.innerHTML = `
                        <h3>${job.title}</h3>
                        <p>${job.description}</p>
                        <p><strong>الموقع:</strong> ${job.location}</p>
                        <p><strong>الراتب:</strong> ${job.salary}</p>
                    `;
                    jobsContainer.appendChild(jobBox);
                });
            } catch (error) {
                console.error('Error fetching jobs:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', fetchJobs);
    </script>
</head>
<body class="dashboard-page">
    <?php include 'headerDash.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="container">
            <h1>الوظائف المتاحة</h1>
            <div id="jobs-container" class="jobs-container">
                <!-- Job boxes will be dynamically loaded here -->
            </div>
        </div>
    </main>
</body>
</html>