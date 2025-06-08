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

// جلب عدد الإشعارات غير المقروءة
$unread_notifications = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $unread_notifications = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $unread_notifications = 0;
    }
}

// جلب حالة التوثيق
$verification_status = null;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare('SELECT verification_status FROM user WHERE User_ID = :user_id');
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $verification_status = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $verification_status = null;
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/headerDash.css">
<link rel="stylesheet" href="css/notification-dropdown.css">
<link rel="stylesheet" href="css/messages-dropdown.css">
<link rel="stylesheet" href="css/work-images.css">
<script src="js/notification.js"></script>
<script src="js/headerDash.js"></script>
<script src="js/messages-dropdown.js"></script>
<script src="js/work-images.js?t=<?php echo time(); ?>" defer></script>
<header class="dashboard-header">
  <div class="container">

  <nav style="width:100%; display:flex; align-items:center; justify-content:space-between; flex-direction: row;">
      <!-- Left: Sidebar toggle + Logo -->
      <div style="display:flex; align-items:center; gap:1rem;">
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="تبديل القائمة الجانبية"><i class="fas fa-bars"></i></button>
        <div class="logo">
          <a href="dashboard.php" style="text-decoration: none; color: inherit;">
            <i class="fas fa-handshake"></i>
            <span>Fursa<span style="color: var(--primary);">Pal</span></span>
          </a>
        </div>
      </div>
      <!-- Center: Search bar with voice and suggestions -->
      <div style="display: flex; align-items: center; gap: 10px; width: 100%; max-width: 500px; margin: 0 1.5rem;">
        <!-- Voice search button -->
        <button id="voice-search-btn" title="بحث صوتي" 
                style="background: none; border: none; cursor: pointer; padding: 8px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-microphone"></i>
        </button>
        <!-- Search bar with suggestions -->
        <div class="search-bar" style="position: relative; flex-grow: 1;">
            <input type="text" placeholder="ابحث عن وظائف أو مهنيين..." 
                   style="width: 100%; padding: 8px 40px 8px 8px; border: 1px solid #ccc; border-radius: 4px;">
            <button style="position: absolute; left: auto; right: 5px; top: 50%; transform: translateY(-50%); 
                         background: none; border: none; cursor: pointer;">
                <i class="fas fa-search"></i>
            </button>
            <div class="search-suggestions" style="position:absolute;right:0;left:0;top:100%;background:#fff;z-index:1000;border:1px solid #ccc;display:none;"></div>
        </div>
      </div>
      <!-- Right: Notifications + User + Logout -->
      <div class="user-actions" style="display:flex; align-items:center; gap:1.2rem; position:relative;">
        <div class="notification-bell" id="notificationBell">
          <i class="fas fa-bell"></i>
          <?php if ($unread_notifications > 0): ?>
            <span class="notification-count"><?php echo $unread_notifications; ?></span>
          <?php endif; ?>
          <div class="notification-dropdown" id="notificationDropdown" style="display:none;">
            <div class="notif-header">
              <span>الإشعارات</span>
              <div class="notif-tabs">
                <button class="notif-tab active" data-tab="all">الكل</button>
                <button class="notif-tab" data-tab="unread">غير مقروءة</button>
              </div>
            </div>
            <div class="notif-mark-all">تمييز الكل كمقروءة</div>
            <div class="notification-list" id="notificationList">
              <div style="padding:20px;text-align:center;color:#888;">جاري التحميل...</div>
            </div>
          </div>
        </div>
        <div class="messages-bell" id="messagesBell">
          <i class="fas fa-envelope"></i>
          <span class="messages-count" id="messagesCount" style="display:none;"></span>
          <div class="messages-dropdown" id="messagesDropdown"></div>
        </div>
        <div class="user-profile" id="userProfileMenuBtn">
          <img src="<?php echo $profile_photo; ?>" alt="صورة المستخدم">
          <span><?php echo $user_name; ?>
            <?php if ($verification_status === 'verified'): ?>
              <img src="https://upload.wikimedia.org/wikipedia/commons/e/e4/Twitter_Verified_Badge.svg" alt="موثق" style="width:20px;height:20px;margin-right:3px;vertical-align:middle;" title="حساب موثق">
            <?php endif; ?>
          </span>
          <span id="userProfileArrow" style="cursor:pointer;"><i class="fas fa-chevron-down"></i></span>
          <div class="user-profile-menu" id="userProfileMenu">
            <a href="profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
            <a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a>
            <a href="#" id="logoutMenuBtn"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
</header>
<!-- Sidebar Drawer -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar-drawer" id="sidebarDrawer" dir="rtl">
  <div class="sidebar-header">
    <div class="logo">
          <i class="fas fa-handshake"></i>
          <span>Fursa<span style="color: var(--primary);">Pal</span></span>
        </div>
    <button class="sidebar-toggle-btn" id="sidebarCloseBtn" aria-label="إغلاق القائمة الجانبية"><i class="fas fa-times"></i></button>
  </div>
  <nav class="sidebar-menu">
    <a class="menu-item" href="dashboard.php">
      <i class="fas fa-home"></i>
      <span>الرئيسية</span>
    </a>
    <a class="menu-item" href="jobs.php">
      <i class="fas fa-briefcase"></i>
      <span>الوظائف</span>
    </a>
    <a class="menu-item" href="professionals.php">
      <i class="fas fa-user-tie"></i>
      <span>المهنيون</span>
    </a>
    <a class="menu-item" href="saved_jobs.php">
      <i class="fas fa-bookmark"></i>
      <span>المحفوظات</span>
    </a>
    <a class="menu-item" href="messages.php">
      <i class="fas fa-envelope"></i>
      <span>الرسائل</span>
    </a>
  </nav>
</aside>
