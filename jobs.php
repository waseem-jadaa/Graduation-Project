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
    <link rel="stylesheet" href="css/jobs.css">
    <link rel="stylesheet" href="css/pagination.css">
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
    
    <div id="pagination-container-wrapper">
        <div id="pagination-container"></div>
    </div>

    <script>
        // Pass PHP variable to JavaScript
        const searchTerm = '<?php echo $searchTerm; ?>';
    </script>
    <script src="js/jobs.js"></script>
</body>
</html>