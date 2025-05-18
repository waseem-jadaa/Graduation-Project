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
<link rel="stylesheet" href="css/headerDash.css">
<script src="notification.js"></script>
<header class="dashboard-header">
  <div class="container">

  <nav style="width:100%; display:flex; align-items:center; justify-content:space-between; flex-direction: row;">
      <!-- Left: Sidebar toggle + Logo -->
      <div style="display:flex; align-items:center; gap:1rem;">
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="تبديل القائمة الجانبية"><i class="fas fa-bars"></i></button>
        <div class="logo">
          <i class="fas fa-handshake"></i>
          <span>Fursa<span style="color: var(--primary);">Pal</span></span>
        </div>
      </div>
      
      <!-- Right: Notifications + User + Logout -->
      <div class="user-actions" style="display:flex; align-items:center; gap:1.2rem;">
        <div class="notification-bell">
          <i class="fas fa-bell"></i>
          <span class="notification-count">3</span>
        </div>
        <div class="user-profile">
          <?php 
            // أضف متغير وقت لإجبار المتصفح على تحميل الصورة الجديدة دائماً
            $profile_photo_url = $profile_photo;
            if (strpos($profile_photo_url, 'uploads/profile_photos/') === 0 && file_exists($profile_photo_url)) {
                $profile_photo_url .= '?t=' . filemtime($profile_photo_url);
            }
          ?>
          <img src="<?php echo $profile_photo_url; ?>" alt="صورة المستخدم">
          <span><?php echo $user_name; ?></span>
          <i class="fas fa-chevron-down"></i>
          <div class="user-profile-menu">
            <a href="profile.php">الملف الشخصي</a>
          </div>
        </div>
        <div class="logout-icon" style="cursor: pointer; margin-left: 10px;" onclick="location.href='main.php'">
          <i class="fas fa-sign-out-alt"></i>
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
    <div class="menu-section">الرئيسية</div>
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
    <div class="menu-section">الإعدادات</div>
    <a class="menu-item" href="settings.php">
      <i class="fas fa-cog"></i>
      <span>الإعدادات</span>
    </a>
  </nav>
</aside>
<script>
// Sidebar Drawer Logic
const sidebarDrawer = document.getElementById('sidebarDrawer');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

function openSidebar() {
  sidebarDrawer.classList.add('open');
  sidebarOverlay.classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeSidebar() {
  sidebarDrawer.classList.remove('open');
  sidebarOverlay.classList.remove('active');
  document.body.style.overflow = '';
}
sidebarToggleBtn.addEventListener('click', openSidebar);
sidebarCloseBtn.addEventListener('click', closeSidebar);
sidebarOverlay.addEventListener('click', closeSidebar);
// Close sidebar on ESC
window.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeSidebar();
});
</script>