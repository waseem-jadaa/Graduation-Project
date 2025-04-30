<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FursaPal - إنشاء حساب جديد</title>
    <link rel="stylesheet" href="project.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body class="signup-page">
  <?php include 'header.php'; ?>
    
    
    <main class="signup-main">

      <div class="signup-bg-pattern">
        <div class="pattern-circle circle-1"></div>
        <div class="pattern-circle circle-2"></div>
        <div class="pattern-dots"></div>
      </div>

      <div class="signup-container">
        <div class="signup-header">
          <h1 class="signup-title">ابدأ رحلتك مع <span>FursaPal</span></h1>
          <p class="signup-subtitle">اختر نوع الحساب الذي يناسبك</p>

          <div class="account-type-selector">
            <input
              type="radio"
              name="account_type"
              id="worker-account"
              value="worker"
              checked
            />
            <label for="worker-account" class="account-type-card">
              <i class="fas fa-user-tie"></i>
              <span>باحث عن عمل</span>
              <p>أنا أبحث عن فرص عمل تناسب مهاراتي</p>
            </label>

            <input
              type="radio"
              name="account_type"
              id="employer-account"
              value="employer"
            />
            <label for="employer-account" class="account-type-card">
              <i class="fas fa-briefcase"></i>
              <span>صاحب عمل</span>
              <p>أريد توظيف محترفين لدي</p>
            </label>
          </div>
        </div>

        <form
          class="signup-form"
          action=""
          method="POST"
          enctype="multipart/form-data"
        >
         
          <div class="form-section">
            <h2 class="section-title">المعلومات الأساسية</h2>
            <div class="form-grid">
              <div class="form-group floating-input">
                <input type="text" id="fullname" name="fullname" required />
                <label for="fullname">الاسم الكامل</label>
                <i class="fas fa-user"></i>
              </div>

              <div class="form-group floating-input">
                <input type="email" id="email" name="email" required />
                <label for="email">البريد الإلكتروني</label>
                <i class="fas fa-envelope"></i>
              </div>

              <div class="form-group floating-input">
                <input
                  type="password"
                  id="password"
                  name="password"
                  required
                  minlength="8"
                />
                <label for="password">كلمة المرور</label>
                <i class="fas fa-lock"></i>
                <button type="button" class="show-password">
                  <i class="far fa-eye"></i>
                </button>
              </div>

              <div class="form-group floating-input">
                <input
                  type="password"
                  id="confirm_password"
                  name="confirm_password"
                  required
                />
                <label for="confirm_password">تأكيد كلمة المرور</label>
                <i class="fas fa-lock"></i>
              </div>
            </div>
          </div>

        
          <div class="form-section" id="worker-fields">
            <h2 class="section-title">المعلومات المهنية</h2>
            <div class="form-grid">
              <div class="form-group floating-input">
                <input type="text" id="profession" name="profession" required />
                <label for="profession">المهنة</label>
                <i class="fas fa-tools"></i>
              </div>

              <div class="form-group floating-input">
                <input type="text" id="skills" name="skills" required />
                <label for="skills">المهارات (مفصولة بفواصل)</label>
                <i class="fas fa-star"></i>
              </div>

              <div class="form-group floating-input">
                <input
                  type="number"
                  id="experience"
                  name="experience"
                  min="0"
                  required
                />
                <label for="experience">سنوات الخبرة</label>
                <i class="fas fa-briefcase"></i>
              </div>
            </div>
          </div>

          <div class="form-section" id="employer-fields" style="display: none">
            <h2 class="section-title">معلومات الشركة</h2>
            <div class="form-grid">
              <div class="form-group floating-input">
                <input type="text" id="company_name" name="company_name" />
                <label for="company_name">اسم الشركة</label>
                <i class="fas fa-building"></i>
              </div>

              <div class="form-group floating-input">
                <input type="text" id="company_field" name="company_field" />
                <label for="company_field">مجال الشركة</label>
                <i class="fas fa-industry"></i>
              </div>
            </div>

            <div class="form-group file-upload">
              <label for="commercial_license">الرخصة التجارية (اختياري)</label>
              <div class="upload-area">
                <input
                  type="file"
                  id="commercial_license"
                  name="commercial_license"
                  accept="image/*,.pdf"
                />
                <i class="fas fa-cloud-upload-alt"></i>
                <span>اسحب وأسقط الملف هنا أو انقر للاختيار</span>
              </div>
              
              <div id="commercial-license-preview" style="display: none; margin-top: 10px;">
                <img src="" alt="Commercial License Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px;" />
              </div>
            </div>
          </div>

         
          <div class="form-section">
            <h2 class="section-title">تحقق الهوية</h2>
            <div class="form-group file-upload">
              <label for="id_photo">صورة الهوية الشخصية</label>
              <div class="upload-area">
                <input
                  type="file"
                  id="id_photo"
                  name="id_photo"
                  accept="image/*"
                  required
                />
                <i class="fas fa-id-card"></i>
                <span>قم بتحميل صورة واضحة للهوية الشخصية</span>
              </div>
              <p class="upload-hint">سيتم استخدام هذه الصورة للتحقق من هويتك</p>
              
              <div id="id-photo-preview" style="display: none; margin-top: 10px;">
                <img src="" alt="ID Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px;" />
              </div>
            </div>
          </div>

         
          <div class="form-group terms-agreement">
            <input type="checkbox" id="terms" name="terms" required />
            <label for="terms"
              >أوافق على <a href="#">الشروط والأحكام</a> و
              <a href="#">سياسة الخصوصية</a></label
            >
          </div>

          
          <button type="submit" class="signup-btn">
            <span>إنشاء حساب</span>
            <i class="fas fa-user-plus"></i>
          </button>

          
          <div class="login-link">
            لديك حساب بالفعل؟ <a href="login.php">سجل الدخول الآن</a>
          </div>
        </form>
      </div>
    </main>

    
    <footer class="main-footer">
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
              <li><a href="#">الشروط والأحكام</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>
            جميع الحقوق محفوظة &copy; <span id="current-year"></span> فرصة بال
          </p>
        </div>
      </div>
    </footer>

    
    <script>
      
      document.querySelectorAll(".account-type-selector input").forEach((radio) => {
        radio.addEventListener("change", function () {
          
          document.querySelectorAll(".signup-form input").forEach((input) => {
            if (input.type !== "radio" && input.type !== "checkbox") {
              input.value = "";
            } else if (input.type === "checkbox") {
              input.checked = false;
            }
          });

          
          const previewContainers = document.querySelectorAll(".file-upload #id-photo-preview");
          previewContainers.forEach((container) => {
            const previewImage = container.querySelector("img");
            previewImage.src = "";
            container.style.display = "none";
          });

          
          if (this.value === "worker") {
            document.getElementById("worker-fields").style.display = "block";
            document.getElementById("employer-fields").style.display = "none";
          } else {
            document.getElementById("worker-fields").style.display = "none";
            document.getElementById("employer-fields").style.display = "block";
          }
        });
      });

      
      document.querySelectorAll(".show-password").forEach((btn) => {
        btn.addEventListener("click", function () {
          const input = this.parentElement.querySelector("input");
          if (input.type === "password") {
            input.type = "text";
            this.innerHTML = '<i class="far fa-eye-slash"></i>';
          } else {
            input.type = "password";
            this.innerHTML = '<i class="far fa-eye"></i>';
          }
        });
      });

      
      document.getElementById("current-year").textContent =
        new Date().getFullYear();

     
      document.getElementById('id_photo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('id-photo-preview');
        const previewImage = previewContainer.querySelector('img');

        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          previewImage.src = '';
          previewContainer.style.display = 'none';
        }
      });

      
      window.addEventListener('load', function() {
        const fileInput = document.getElementById('id_photo');
        const previewContainer = document.getElementById('id-photo-preview');
        const previewImage = previewContainer.querySelector('img');

        fileInput.value = '';
        previewImage.src = '';
        previewContainer.style.display = 'none';
      });

      
      document.getElementById('commercial_license').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('commercial-license-preview');
        const previewImage = previewContainer.querySelector('img');

        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          previewImage.src = '';
          previewContainer.style.display = 'none';
        }
      });

      
      function clearCommercialLicense() {
        const fileInput = document.getElementById('commercial_license');
        const previewContainer = document.getElementById('commercial-license-preview');
        const previewImage = previewContainer.querySelector('img');

        fileInput.value = '';
        previewImage.src = '';
        previewContainer.style.display = 'none';
      }

      window.addEventListener('load', clearCommercialLicense);

      document.querySelectorAll(".account-type-selector input").forEach((radio) => {
        radio.addEventListener("change", clearCommercialLicense);
      });
    </script>
  </body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db.php';

    if (!isset($_POST['account_type'])) {
        die('نوع الحساب مطلوب.');
    }

    if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
        die('يجب الموافقة على الشروط والأحكام.');
    }

    $accountType = $_POST['account_type'];
    if ($accountType !== 'worker' && $accountType !== 'employer') {
        die('نوع حساب غير صالح.');
    }

    // Validate required fields
    $requiredFields = ['fullname', 'email', 'password', 'confirm_password'];
    if ($accountType === 'worker') {
        $requiredFields = array_merge($requiredFields, ['profession', 'skills', 'experience']);
    } else {
        $requiredFields = array_merge($requiredFields, ['company_name', 'company_field']);
    }

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            die("الحقل {$field} مطلوب.");
        }
    }

    // Validate password match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        die('كلمات المرور غير متطابقة.');
    }

    // Basic data
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $accountType === 'worker' ? 'job_seeker' : 'employer';

    try {
        $conn->beginTransaction();

        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT User_ID FROM user WHERE email = :email");
        $checkEmail->execute([':email' => $email]);
        if ($checkEmail->fetch()) {
            die('البريد الإلكتروني مسجل مسبقاً.');
        }

        // Insert into user table
        $sqlUser = "INSERT INTO user (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':role' => $role
        ]);
        
        $userId = $conn->lastInsertId();

        // Handle ID photo upload
        $idPhotoPath = null;
        if (isset($_FILES['id_photo']) && $_FILES['id_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/id_photos/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($_FILES['id_photo']['name'], PATHINFO_EXTENSION);
            $idPhotoPath = $uploadDir . $userId . '_id.' . $fileExtension;
            move_uploaded_file($_FILES['id_photo']['tmp_name'], $idPhotoPath);
        }

        // Split full name
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
        $lastName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

        if ($accountType === 'worker') {
            // Job seeker profile
            $profession = $_POST['profession'];
            $skills = $_POST['skills'];
            $experience = $_POST['experience'];

            $sqlProfile = "INSERT INTO profile (User_ID, first_name, last_name, bio, skills, location, experience, id_photo) 
                          VALUES (:user_id, :first_name, :last_name, :bio, :skills, :location, :experience, :id_photo)";
            $stmtProfile = $conn->prepare($sqlProfile);
            $stmtProfile->execute([
                ':user_id' => $userId,
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':bio' => $profession,
                ':skills' => $skills,
                ':location' => '',
                ':experience' => $experience,
                ':id_photo' => $idPhotoPath
            ]);

        } else {
            // Employer profile
            $companyName = $_POST['company_name'];
            $companyField = $_POST['company_field'];

            // Handle commercial license upload
            $licensePath = null;
            if (isset($_FILES['commercial_license']) && $_FILES['commercial_license']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/licenses/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileExtension = pathinfo($_FILES['commercial_license']['name'], PATHINFO_EXTENSION);
                $licensePath = $uploadDir . $userId . '_license.' . $fileExtension;
                move_uploaded_file($_FILES['commercial_license']['tmp_name'], $licensePath);
            }

            $sqlProfile = "INSERT INTO profile (User_ID, first_name, last_name, bio, skills, location, experience, id_photo, commercial_license) 
                          VALUES (:user_id, :first_name, :last_name, :bio, :skills, :location, :experience, :id_photo, :license)";
            $stmtProfile = $conn->prepare($sqlProfile);
            $stmtProfile->execute([
                ':user_id' => $userId,
                ':first_name' => $firstName,
                ':last_name' => $lastName,
                ':bio' => $companyName . ' - ' . $companyField,
                ':skills' => '',
                ':location' => '',
                ':experience' => '0',
                ':id_photo' => $idPhotoPath,
                ':license' => $licensePath
            ]);
        }

        $conn->commit();
        
        // Set session variables
        session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;

        // Redirect to main page
        header('Location: main.php');
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database Error: " . $e->getMessage());
        die("حدث خطأ أثناء إنشاء الحساب. الرجاء المحاولة مرة أخرى لاحقاً.");
    }
}
?>
