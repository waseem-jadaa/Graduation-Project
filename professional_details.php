<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get the professional ID from URL parameter
$professional_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$professional_id) {
    header('Location: professionals.php');
    exit;
}

try {
    // Fetch professional's basic information
    $stmt = $conn->prepare('
        SELECT u.*, p.*, 
        (SELECT AVG(rating) FROM professional_ratings WHERE professional_id = u.User_ID) as avg_rating,
        (SELECT COUNT(*) FROM professional_ratings WHERE professional_id = u.User_ID) as rating_count
        FROM user u 
        LEFT JOIN profile p ON u.User_ID = p.User_ID 
        WHERE u.User_ID = :id AND u.role = "job_seeker"
    ');
    $stmt->execute([':id' => $professional_id]);
    $professional = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$professional) {
        header('Location: professionals.php');
        exit;
    }

    // Get work images from profile table
    $work_images = [];
    for ($i = 1; $i <= 5; $i++) {
        $image_column = 'work_image_' . $i;
        // Use trim() to handle potential whitespace in database entries
        if (!empty($professional[$image_column]) && trim($professional[$image_column]) !== '') {
            $work_images[] = [
                'image_path' => $professional[$image_column],
                'image_order' => $i // You can use this for ordering if needed
            ];
        }
    }

} catch (PDOException $e) {
    die("Error fetching professional data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل المهني - <?php echo htmlspecialchars($professional['first_name'] . ' ' . $professional['last_name']); ?></title>
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/rating.css">
    <link rel="stylesheet" href="css/request-btn.css">
    <link rel="stylesheet" href="css/professional_details.css">
</head>
<body class="dashboard-page">
    <?php include 'headerDash.php'; ?>
    
    <main class="main-content">
        <div class="professional-details">
            <div class="professional-header">
                <img src="<?php echo htmlspecialchars($professional['profile_photo'] ?? 'image/p.png'); ?>" 
                     alt="صورة المهني" 
                     class="profile-photo">
                
                <div class="professional-info">
                    <h1 class="professional-name">
                        <?php echo htmlspecialchars($professional['first_name'] . ' ' . $professional['last_name']); ?>
                        <?php if ($professional['verification_status'] === 'verified'): ?>
                            <span class="verification-badge">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e4/Twitter_Verified_Badge.svg" 
                                     alt="موثق" 
                                     title="حساب موثق">
                            </span>
                        <?php endif; ?>
                    </h1>
                    <p class="professional-title"><?php echo htmlspecialchars($professional['bio'] ?? 'مهني'); ?></p>
                    
                    <div class="rating-section">
                        <div class="rating-stars" data-professional-id="<?php echo $professional_id; ?>">
                            <?php
                            $avg_rating = round($professional['avg_rating'] ?? 0);
                            for ($i = 1; $i <= 5; $i++) {
                                echo '<span style="color:' . ($i <= $avg_rating ? '#FFD700' : '#ccc') . ';">★</span>';
                            }
                            ?>
                        </div>
                        <span class="rating-count">
                            (<?php echo $professional['rating_count'] ?? 0; ?> تقييم)
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>الموقع</h3>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($professional['location'] ?? 'غير محدد'); ?></p>
                </div>
                
                <div class="info-card">
                    <h3>الخبرة</h3>
                    <p><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($professional['experience'] ?? '0'); ?> سنوات</p>
                </div>
                
                <div class="info-card">
                    <h3>المهارات</h3>
                    <p><?php echo htmlspecialchars($professional['skills'] ?? 'غير محدد'); ?></p>
                </div>
            </div>

            <div class="work-images">
                <h2>معرض الأعمال</h2>
                <?php if (!empty($work_images)): ?>
                    <div class="images-grid">
                        <?php foreach ($work_images as $image): ?>
                            <div class="work-image-container">
                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" 
                                     alt="صورة عمل" 
                                     class="work-image">
                                <div class="image-overlay">
                                    <p>عمل منجز</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-images">
                        <p>لا توجد صور أعمال متاحة حالياً</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="action-buttons">
                <button class="btn-request" data-professional-id="<?php echo $professional_id; ?>">
                    اطلبه الآن
                </button>
            </div>
        </div>
    </main>

    <script src="js/professional_details.js"></script>

    <!-- The Modal for Image Enlargement -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>
</body>
</html> 