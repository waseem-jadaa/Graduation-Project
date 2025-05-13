<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

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
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<header class="dashboard-header">
      <div class="container">
        <nav>
          <div class="logo">
            <i class="fas fa-handshake"></i>
            <span>Fursa<span style="color: var(--primary);">Pal</span></span>
          </div>
          
          <div class="search-bar">
            <input type="text" placeholder="ابحث عن وظائف أو مهنيين...">
            <button><i class="fas fa-search"></i></button>
          </div>
          
          <div class="user-actions">
            <div class="notification-bell">
              <i class="fas fa-bell"></i>
              <span class="notification-count">3</span>
            </div>
            <div class="user-profile">
              <img src="<?php echo $profile_photo; ?>" alt="صورة المستخدم">
              <span><?php echo $user_name; ?></span>
              <i class="fas fa-chevron-down"></i>
              <div class="user-profile-menu">
                <a href="profile.php">الملف الشخصي</a>
              </div>
            </div>
            <div class="logout-icon" style="cursor: pointer; margin-left: 20px;">
              <i class="fas fa-sign-out-alt" onclick="location.href='main.php'"></i>
            </div>
          </div>
        </nav>
      </div>
    </header>