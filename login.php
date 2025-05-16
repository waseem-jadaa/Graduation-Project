<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FursaPal - تسجيل الدخول</title>
    <link rel="stylesheet" href="css/project.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body class="login-page">
  <?php include 'header.php'; ?>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      include 'db.php';

      $email = $_POST['email'];
      $password = $_POST['password'];

      try {
          $sql = "SELECT User_ID, password FROM user WHERE email = :email";
          $stmt = $conn->prepare($sql);
          $stmt->execute([':email' => $email]);

          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($user && password_verify($password, $user['password'])) {
              session_start();
              $_SESSION['user_id'] = $user['User_ID'];
              header('Location: dashboard.php');
              exit();
          } else {
              echo "Invalid email or password.";
          }
      } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
      }
  }
  ?>

    <main class="login-main">
      <div class="login-bg-animation">
        <div class="bg-circle circle-1"></div>
        <div class="bg-circle circle-2"></div>
        <div class="bg-circle circle-3"></div>
      </div>

      <div class="login-center-container">
        <div class="login-container">
          <div class="login-header">
            <h1>مرحباً بعودتك!</h1>
            <p>سجل الدخول للوصول إلى فرص العمل المناسبة</p>
          </div>

          <form class="login-form" action="login.php" method="POST">
            <div class="form-group floating-input">
              <input type="email" id="email" name="email" required />
              <label for="email">البريد الإلكتروني</label>
              <i class="fas fa-envelope"></i>
            </div>

            <div class="form-group floating-input">
              <input type="password" id="password" name="password" required />
              <label for="password">كلمة المرور</label>
              <i class="fas fa-lock"></i>
              <button type="button" class="show-password">
                <i class="far fa-eye"></i>
              </button>
            </div>

            <div class="form-options">
              <label class="remember-me">
                <input type="checkbox" name="remember" />
                <span class="checkmark"></span>
                تذكرني
              </label>
              <a href="forgot-password.html" class="forgot-password"
                >نسيت كلمة المرور؟</a
              >
            </div>

            <button type="submit" class="login-btn">
              <span>تسجيل الدخول</span>
              <i class="fas fa-arrow-left"></i>
            </button>

            <div class="social-login">
              <p>أو سجل الدخول باستخدام</p>
              <div class="social-buttons">
                <a href="#" class="social-btn google">
                  <i class="fab fa-google"></i>
                </a>
                <a href="#" class="social-btn facebook">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-btn linkedin">
                  <i class="fab fa-linkedin-in"></i>
                </a>
              </div>
            </div>

            <div class="register-link">
              ليس لديك حساب؟ <a href="signup.php">أنشئ حساباً جديداً</a>
            </div>
          </form>
        </div>
      </div>
    </main>

    <footer>
      <div class="container">
        <div class="footer-grid">
          <div class="footer-about">
            <h3>عن فرصة بال</h3>
            <p>
              منصة توظيف إلكترونية تهدف إلى ربط المهنيين الفلسطينيين بفرص العمل
              المناسبة.
            </p>
          </div>
          <div class="footer-links">
            <h3>روابط سريعة</h3>
            <ul>
              <li><a href="main.php">الرئيسية</a></li>
              <li><a href="#">الوظائف</a></li>
              <li><a href="professionals.php">المهنيون</a></li>
            </ul>
          </div>
          <div class="footer-links">
            <h3>الدعم</h3>
            <ul>
              <li><a href="#">الأسئلة الشائعة</a></li>
              <li><a href="contact.php">اتصل بنا</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>جميع الحقوق محفوظة &copy; 2025 فرصة بال</p>
        </div>
      </div>
    </footer>
  </body>
</html>
