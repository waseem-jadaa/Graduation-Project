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


        <form
          class="signup-form"
          action=""
          method="POST"
          enctype="multipart/form-data"
        >
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

          <div class="form-section" style="padding: 20px;">
            <h2 class="section-title">المعلومات الأساسية</h2>
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
              <div class="form-group floating-input">
                <input type="text" id="first_name" name="first_name" style="font-size: 0.9rem; padding: 0.5rem;" required />
                <label for="first_name">الاسم الأول</label>
                <i class="fas fa-user"></i>
              </div>

              <div class="form-group floating-input">
                <input type="text" id="last_name" name="last_name" style="font-size: 0.9rem; padding: 0.5rem;" required />
                <label for="last_name">اسم العائلة</label>
                <i class="fas fa-user"></i>
              </div>
            </div>

            <div class="form-group floating-input">
              <input type="email" id="email" name="email" style="font-size: 0.9rem; padding: 0.5rem;" required />
              <label for="email">البريد الإلكتروني</label>
              <i class="fas fa-envelope"></i>
            </div>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
              <div class="form-group floating-input">
                <input
                  type="password"
                  id="password"
                  name="password"
                  style="font-size: 0.9rem; padding: 0.5rem;"
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
                  style="font-size: 0.9rem; padding: 0.5rem;"
                  required
                />
                <label for="confirm_password">تأكيد كلمة المرور</label>
                <i class="fas fa-lock"></i>
              </div>
            </div>
          </div>

          <div class="form-section" style="padding: 20px;">
            <h2 class="section-title">المعلومات المهنية</h2>
            <div class="form-group floating-input">
              <input type="text" id="profession" name="profession" style="font-size: 0.9rem; padding: 0.5rem;" required />
              <label for="profession">المهنة</label>
              <i class="fas fa-tools"></i>
            </div>

            <div class="form-group floating-input">
              <input type="text" id="skills" name="skills" style="font-size: 0.9rem; padding: 0.5rem;" required />
              <label for="skills">المهارات (مفصولة بفواصل)</label>
              <i class="fas fa-star"></i>
            </div>

            <div class="form-group floating-input">
              <input
                type="number"
                id="experience"
                name="experience"
                style="font-size: 0.9rem; padding: 0.5rem;"
                min="0"
                required
              />
              <label for="experience">سنوات الخبرة</label>
              <i class="fas fa-briefcase"></i>
            </div>

            <div class="form-group floating-input">
              <input type="text" id="location" name="location" style="font-size: 0.9rem; padding: 0.5rem;" required />
              <label for="location">الموقع</label>
              <i class="fas fa-map-marker-alt"></i>
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
          </div>

          <div class="form-section" style="padding: 20px;">
            <h2 class="section-title">تحقق الهوية</h2>
            <div class="form-group file-upload">
              <label for="id_photo">صورة الهوية الشخصية</label>
              <div class="upload-area" style="font-size: 0.9rem; padding: 0.5rem;">
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

          <div class="form-section" style="padding: 20px;">
            <h2 class="section-title">الصورة الشخصية</h2>
            <div class="form-group file-upload">
                <label for="profile_photo">الصورة الشخصية</label>
                <div class="upload-area" style="font-size: 0.9rem; padding: 0.5rem;">
                    <input
                        type="file"
                        id="profile_photo"
                        name="profile_photo"
                        accept="image/*"
                        required
                    />
                    <i class="fas fa-user-circle"></i>
                    <span>قم بتحميل صورة شخصية واضحة</span>
                </div>
                <p class="upload-hint">ستظهر هذه الصورة في ملفك الشخصي</p>
                <div id="profile-photo-preview" style="display: none; margin-top: 10px;">
                    <img src="" alt="Profile Photo Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 5px;" />
                </div>
            </div>
        </div>
         
          <div class="form-group terms-agreement" style="margin-bottom: 20px;">
            <input type="checkbox" id="terms" name="terms" required style="margin-left: 10px;" />
            <label for="terms" style="line-height: 1.5;">
              أوافق على <a href="#" style="color: var(--primary); text-decoration: underline;">الشروط والأحكام</a> و
              <a href="#" style="color: var(--primary); text-decoration: underline;">سياسة الخصوصية</a>
            </label>
          </div>

          <button type="submit" class="signup-btn" style="margin: 20px auto; font-size: 0.8rem; padding: 0.5rem 1rem; width: auto; min-width: 120px; display: block;">
            <span>إنشاء حساب</span>
            <i class="fas fa-user-plus"></i>
          </button>

          <div class="login-link" style="margin-top: 15px;">
            لديك حساب بالفعل؟ <a href="login.php" style="color: var(--primary); text-decoration: underline;">سجل الدخول الآن</a>
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
      function updateRequiredFields(accountType) {
  const workerFields = document.querySelectorAll("#worker-fields input");
  const employerFields = document.querySelectorAll("#employer-fields input");

  if (accountType === "worker") {
    workerFields.forEach((input) => input.required = true);
    employerFields.forEach((input) => input.required = false);
  } else {
    workerFields.forEach((input) => input.required = false);
    employerFields.forEach((input) => {
      // Except for the commercial license -- > may cancle
      if (input.id !== "commercial_license") {
        input.required = true;
      } else {
        input.required = false;
      }
     });
  }
}

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
          updateRequiredFields(this.value);
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

      // Add profile photo preview functionality
      document.getElementById('profile_photo').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('profile-photo-preview');
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
        const selectedAccountType = document.querySelector(".account-type-selector input:checked").value;
        updateRequiredFields(selectedAccountType);
        const fileInput = document.getElementById('id_photo');
        const previewContainer = document.getElementById('id-photo-preview');
        const previewImage = previewContainer.querySelector('img');

        fileInput.value = '';
        previewImage.src = '';
        previewContainer.style.display = 'none';
      });

    </script>
  </body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db.php';

    // check error when database OP
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    function handleError($message) {
        error_log($message);
        echo "<script>alert('حدث خطأ: $message');</script>";
        exit();
    }

    error_log("Form submission data: " . print_r($_POST, true));

    if (!isset($_POST['account_type'])) {
        handleError('نوع الحساب مطلوب.');
    }

    if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
        handleError('يجب الموافقة على الشروط والأحكام.');
    }

    $accountType = $_POST['account_type'];
    if ($accountType !== 'worker' && $accountType !== 'employer') {
        handleError('نوع حساب غير صالح.');
    }

    
    $requiredFields = ['first_name', 'last_name', 'email', 'password', 'confirm_password', 'location'];
    if ($accountType === 'worker') {
        $requiredFields = array_merge($requiredFields, ['profession', 'skills', 'experience']);
    } else {
        $requiredFields = array_merge($requiredFields, ['company_name', 'company_field']);
    }

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            handleError("الحقل {$field} مطلوب.");
        }
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        handleError('كلمات المرور غير متطابقة.');
    }

    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $accountType === 'worker' ? 'job_seeker' : 'employer';
    $location = $_POST['location'];

    try {
        $conn->beginTransaction();

        try {
            $checkEmail = $conn->prepare("SELECT User_ID FROM user WHERE email = :email");
            $checkEmail->execute([':email' => $email]);
            if ($checkEmail->fetch()) {
                handleError('البريد الإلكتروني مسجل مسبقاً.');
            }
        } catch (PDOException $e) {
            handleError("Database Error: " . $e->getMessage());
        }
        function logDebug($message) {
            error_log(message: "DEBUG: " . $message);
        }

        
        logDebug("Attempting to insert into user table with data: Name=$firstName $lastName, Email=$email, Role=$role");

        try {
            $sqlUser = "INSERT INTO user (name, email, password, role) VALUES (:name, :email, :password, :role)";
            $stmtUser = $conn->prepare($sqlUser);
            $stmtUser->execute([
                ':name' => $firstName . ' ' . $lastName,
                ':email' => $email,
                ':password' => $password,
                ':role' => $role
            ]);
            $userId = $conn->lastInsertId();
            logDebug("User inserted successfully with ID: $userId");
        } catch (PDOException $e) {
            logDebug("Error inserting into user table: " . $e->getMessage());
            handleError("Database Error: " . $e->getMessage());
        }

        // Handle ID photo upload -- > not done (Not well examined)
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

        // Handle profile photo upload
        $profilePhotoPath = null;
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/profile_photos/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $profilePhotoPath = $uploadDir . $userId . '_profile.' . $fileExtension;
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profilePhotoPath);
        }

        
        logDebug("Attempting to insert into profile table for User ID: $userId");

        try {
            if ($accountType === 'worker') {
                logDebug("Inserting worker profile with Profession=$profession, Skills=$skills, Experience=$experience");
                // Job_seeker profile
                $profession = $_POST['profession'];
                $skills = $_POST['skills'];
                $experience = $_POST['experience'];

                $sqlProfile = "INSERT INTO profile (User_ID, first_name, last_name, bio, skills, location, experience, id_photo, profile_photo) 
                              VALUES (:user_id, :first_name, :last_name, :bio, :skills, :location, :experience, :id_photo, :profile_photo)";
                $stmtProfile = $conn->prepare($sqlProfile);
                $stmtProfile->execute([
                    ':user_id' => $userId,
                    ':first_name' => $firstName,
                    ':last_name' => $lastName,
                    ':bio' => $profession,
                    ':skills' => $skills,
                    ':location' => $location,
                    ':experience' => $experience,
                    ':id_photo' => $idPhotoPath,
                    ':profile_photo' => $profilePhotoPath
                ]);
                logDebug("Worker profile inserted successfully");
            } else {
                logDebug("Inserting employer profile with Company Name=$companyName, Company Field=$companyField");
                // Employer profile
                $companyName = $_POST['company_name'];
                $companyField = $_POST['company_field'];

                $sqlProfile = "INSERT INTO profile (User_ID, first_name, last_name, bio, skills, location, experience, id_photo, profile_photo) 
                              VALUES (:user_id, :first_name, :last_name, :bio, :skills, :location, :experience, :id_photo, :profile_photo)";
                $stmtProfile = $conn->prepare($sqlProfile);
                $stmtProfile->execute([
                    ':user_id' => $userId,
                    ':first_name' => $firstName,
                    ':last_name' => $lastName,
                    ':bio' => $companyName . ' - ' . $companyField,
                    ':skills' => '',
                    ':location' => $location,
                    ':experience' => '0',
                    ':id_photo' => $idPhotoPath,
                    ':profile_photo' => $profilePhotoPath
                ]);
                logDebug("Employer profile inserted successfully");
            }
        } catch (PDOException $e) {
            logDebug("Error inserting into profile table: " . $e->getMessage());
            handleError("Database Error: " . $e->getMessage());
        }

        $conn->commit();
        
        session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_role'] = $role;

        echo "<script>alert('تم إنشاء الحساب بنجاح!'); window.location.href = 'login.php';</script>";
        exit();

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Database Error: " . $e->getMessage());
        handleError("حدث خطأ أثناء إنشاء الحساب. الرجاء المحاولة مرة أخرى لاحقاً.");
    }
}
?>
