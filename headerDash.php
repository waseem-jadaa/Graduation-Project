<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($user_name)) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            $stmt = $conn->prepare('SELECT name FROM user WHERE User_ID = :user_id');
            $stmt->execute([':user_id' => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_name = $row ? htmlspecialchars($row['name']) : 'مستخدم';
        } catch (PDOException $e) {
            $user_name = 'مستخدم';
        }
    } else {
        $user_name = 'مستخدم';
    }
}
?>
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
              <img src="image\p.png" alt="صورة المستخدم">
              <span><?php echo $user_name; ?></span>
              <i class="fas fa-chevron-down"></i>
            </div>
          </div>
        </nav>
      </div>
    </header>