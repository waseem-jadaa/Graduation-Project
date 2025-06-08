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
    <link rel="stylesheet" href="css/pagination.css">
    <link rel="stylesheet" href="css/search_results.css">
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
        <!-- شريط التنقل بين الصفحات في أسفل الصفحة -->
        <div id="pagination-bottom-wrapper" style="margin-top: 32px;"></div>
    </div>
</main>
<script src="js/search_results.js"></script>
</body>
</html>
