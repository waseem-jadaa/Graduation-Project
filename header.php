<?php
session_start();
?>
<header class="main-header">
  <div class="container">
    <nav>
      <ul class="nav-links">
        <li><a href="main.php">الرئيسية</a></li>
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
          <li><a href="jobs.php">الوظائف المتاحة</a></li>
          
        <?php endif; ?>
        <li><a href="about-us.php">من نحن</a></li>
        <li><a href="contact.php">اتصل بنا</a></li>
      </ul>
      <div class="auth-buttons">
        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
          <a href="logout.php" class="btn btn-login">تسجيل الخروج</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-login">تسجيل الدخول</a>
          <a href="signup.php" class="btn btn-signup">حساب جديد</a>
        <?php endif; ?>
      </div>
      <div class="logo">Pal<span>Fursa</span>
        <a href="main.php"></a>
      </div>
    </nav>
  </div>
</header>
