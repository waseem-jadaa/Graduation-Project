<?php
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare('SELECT * FROM user WHERE User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Safely fetch user data with default values if keys are missing
$user_name = htmlspecialchars($user['name'] ?? 'غير معروف');
$user_email = htmlspecialchars($user['email'] ?? 'غير متوفر');
$user_phone = htmlspecialchars($user['phone'] ?? 'غير متوفر');
$user_address = htmlspecialchars($user['address'] ?? 'غير متوفر');
$user_city = htmlspecialchars($user['city'] ?? 'غير متوفر');
$user_postal_code = htmlspecialchars($user['postal_code'] ?? 'غير متوفر');
$user_country = htmlspecialchars($user['country'] ?? 'غير متوفر');

// Fetch profile data from the `profile` table
try {
    $stmt = $conn->prepare('SELECT p.*, u.email, u.role FROM profile p JOIN user u ON p.User_ID = u.User_ID WHERE p.User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if profile exists and handle based on role
    if (!$profile) {
        // Use user table data for employee profile
        $first_name = htmlspecialchars($user['name'] ?? 'غير معروف');
        $last_name = 'غير متوفر'; // Default value for last name
        $email = htmlspecialchars($user['email'] ?? 'غير متوفر');
        $role = htmlspecialchars($user['role'] === 'employee' ? 'صاحب عمل' : 'غير متوفر');
        $bio = 'يرجى تحديث بياناتك.';
        $skills = 'غير متوفر';
        $location = 'غير متوفر';
        $experience = 'غير متوفر';
        $id_photo = 'image/p.png';
    }
} catch (PDOException $e) {
    die("Error fetching profile data: " . $e->getMessage());
}

// Ensure all variables are set for the form
$first_name = $first_name ?? htmlspecialchars($profile['first_name'] ?? 'غير معروف');
$last_name = $last_name ?? htmlspecialchars($profile['last_name'] ?? 'غير معروف');
$bio = $bio ?? htmlspecialchars($profile['bio'] ?? 'غير متوفر');
$skills = $skills ?? htmlspecialchars($profile['skills'] ?? 'غير متوفر');
$location = $location ?? htmlspecialchars($profile['location'] ?? 'غير متوفر');
$experience = $experience ?? htmlspecialchars($profile['experience'] ?? 'غير متوفر');
$id_photo = $id_photo ?? htmlspecialchars($profile['id_photo'] ?? 'image/p.png');
$email = $email ?? htmlspecialchars($profile['email'] ?? 'غير متوفر');
// استخدم قيمة role الحقيقية من قاعدة البيانات (employer/job_seeker)
$role = $profile['role'] ?? ($role ?? 'غير متوفر');

// جلب حالة التوثيق
$stmt = $conn->prepare('SELECT verification_status FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$verification_status = $stmt->fetchColumn() ?: 'not_verified';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data
    $first_name = htmlspecialchars($_POST['first_name'] ?? '');
    $last_name = htmlspecialchars($_POST['last_name'] ?? '');
    $bio = htmlspecialchars($_POST['bio'] ?? '');
    $skills = htmlspecialchars($_POST['skills'] ?? '');
    $location = htmlspecialchars($_POST['location'] ?? '');
    $experience = htmlspecialchars($_POST['experience'] ?? '');

    // معرفة نوع الحساب من قاعدة البيانات
    $stmt = $conn->prepare('SELECT u.role FROM user u WHERE u.User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $userRoleRow = $stmt->fetch(PDO::FETCH_ASSOC);
    $realRole = $userRoleRow ? $userRoleRow['role'] : '';

    // إذا كان صاحب عمل employer، تجاهل المهارات والخبرة
    if ($realRole === 'employer') {
        $skills = '';
        $experience = '0';
        // اجعل الحقول غير مطلوبة حتى لا تمنع الفورم من الإرسال
        unset($_POST['skills']);
        unset($_POST['experience']);
    }

    // معالجة رفع صورة جديدة
    $profile_photo_path = $profile['profile_photo'] ?? null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profile_photos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileExtension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $profile_photo_path = $uploadDir . $user_id . '_profile.' . $fileExtension;
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_photo_path);
    }

    try {
        // تحديث البيانات مع الصورة إذا تم رفعها
        if ($profile_photo_path) {
            $stmt = $conn->prepare('UPDATE profile SET first_name = :first_name, last_name = :last_name, bio = :bio, skills = :skills, location = :location, experience = :experience, profile_photo = :profile_photo WHERE User_ID = :user_id');
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':bio' => $bio,
                ':skills' => $skills,
                ':location' => $location,
                ':experience' => $experience,
                ':profile_photo' => $profile_photo_path,
                ':user_id' => $user_id
            ]);
        } else {
            $stmt = $conn->prepare('UPDATE profile SET first_name = :first_name, last_name = :last_name, bio = :bio, skills = :skills, location = :location, experience = :experience WHERE User_ID = :user_id');
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':bio' => $bio,
                ':skills' => $skills,
                ':location' => $location,
                ':experience' => $experience,
                ':user_id' => $user_id
            ]);
        }

        echo '<p>تم تحديث الملف الشخصي بنجاح.</p>';
        echo '<script>setTimeout(function(){ location.reload(); }, 800);</script>';
    } catch (PDOException $e) {
        die("Error updating profile data: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي</title>
    <link rel="stylesheet" href="css/project.css">
    <link rel="stylesheet" href="css/profile.css">
    <style>
        body, .profile-container, .profile-form, .profile-details, .profile-header {
            direction: rtl !important;
            text-align: right;
        }
        .profile-details label, .profile-details input, .profile-details textarea {
            text-align: right;
        }
    </style>
</head>
<body>
<?php
// استبدال هيدر البروفايل بهيدر لوحة التحكم
include 'headerDash.php';
?>

<div class="profile-container">
    
    <form class="profile-form" method="POST" action="updateProfile.php" enctype="multipart/form-data">
        <div class="profile-header" style="text-align:center; position:relative;">
            <label for="profile_photo" style="cursor:pointer; display:inline-block; position:relative;">
                <img id="profilePhotoPreview" src="<?php echo !empty($profile['profile_photo']) ? $profile['profile_photo'] . '?t=' . time() : $id_photo; ?>" alt="الصورة الشخصية" style="width:160px;height:160px;border-radius:50%;margin-bottom:1rem;border:4px solid white;box-shadow:0 4px 10px rgba(0,0,0,0.2);object-fit:cover;">
                <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;">
                <span id="cameraIcon" style="position:absolute;bottom:18px;left:18px;background:#fff;border-radius:50%;padding:8px;box-shadow:0 2px 6px rgba(0,0,0,0.15);border:1px solid #ddd;">
                    <i class="fas fa-camera" style="font-size:22px;color:#444;"></i>
                </span>
            </label>
            <h2 style="margin-bottom: 0.2em;display:inline-block;">
                <?php echo $first_name . ' ' . $last_name; ?>
                <?php if ($verification_status === 'verified'): ?>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/e4/Twitter_Verified_Badge.svg" alt="موثق" style="width: 30px;height:30px;margin-right:3px;vertical-align:middle;" title="حساب موثق">
                <?php endif; ?>
            </h2>
            <h3 style="color:antiquewhite; font-size: 1.1em; margin-top: 0; margin-bottom: 1em; font-weight: normal;">
                <?php echo $email; ?>
            </h3>
            <?php if ($verification_status !== 'verified'): ?>
                <div style="margin-top:10px;">
                    <a href="verification.php" style="color:#1da1f2;text-decoration:underline;font-weight:bold;display:inline-block;">
                        <span style="font-size:1.1em;">🔒 اضغط هنا لتوثيق حسابك</span>
                    </a>
                </div>
            <?php else: ?>
                <div style="margin-top:10px;color:#1da1f2;font-weight:bold;">موثق</div>
            <?php endif; ?>
        </div>
        <div class="profile-details" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            <div style="flex: 1 1 200px; min-width: 200px;">
                <label for="first_name">الاسم الأول:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo $first_name; ?>" disabled required>
            </div>
            <div style="flex: 1 1 200px; min-width: 200px;">
                <label for="last_name">اسم العائلة:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>" disabled required>
            </div>
        <div style="flex: 1 1 300px; min-width: 250px;">
                <label for="bio"><strong>المهنة:</strong></label>
                <textarea id="bio" name="bio" disabled required><?php echo $bio; ?></textarea>
            </div>
            
            
            <div style="flex: 1 1 200px; min-width: 200px;">
                <label for="skills">المهارات:</label>
                <input type="text" id="skills" name="skills" value="<?php echo $skills; ?>" disabled required>
            </div>
            <div style="flex: 1 1 200px; min-width: 200px;">
                <label for="location">الموقع:</label>
                <input type="text" id="location" name="location" value="<?php echo $location; ?>" disabled required>
            </div>
            <div style="flex: 1 1 200px; min-width: 200px;">
                <label for="experience">سنوات الخبرة:</label>
                <input type="text" id="experience" name="experience" value="<?php echo $experience; ?>" disabled required>
            </div>
           
        </div>
        <div class="edit-button" style="text-align:center; margin-top: 20px;">
            <button type="button" id="editBtn">✏️ تعديل</button>
            <button type="submit" id="saveBtn" style="display:none;">حفظ التعديلات</button>
        </div>
    </form>
</div>

<script src="headerDash.js"></script>
<script>
// Add edit functionality for the form
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const inputs = document.querySelectorAll('.profile-form input, .profile-form textarea');
const profilePhotoInput = document.getElementById('profile_photo');
const profilePhotoPreview = document.getElementById('profilePhotoPreview');
const cameraIcon = document.getElementById('cameraIcon');

editBtn.addEventListener('click', function() {
    inputs.forEach(input => {
        if (input.name !== 'email' && input.name !== 'role') {
            input.disabled = false;
        }
    });
    profilePhotoInput.disabled = false;
    cameraIcon.style.display = 'inline-block';
    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
});

profilePhotoInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            profilePhotoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
        // Show Save button and hide Edit button when a new photo is selected
        saveBtn.style.display = 'inline-block';
        editBtn.style.display = 'none';
    }
});

(function() {
    var role = <?php echo json_encode($role); ?>;
    if (role === 'employer') {
        // إخفاء المهارات وسنوات الخبرة
        var skillsGroup = document.getElementById('skills').parentElement;
        var experienceGroup = document.getElementById('experience').parentElement;
        if (skillsGroup) {
            skillsGroup.querySelector('input').disabled = true;
            skillsGroup.querySelector('input').removeAttribute('required');
            skillsGroup.style.display = 'none';
        }
        if (experienceGroup) {
            experienceGroup.querySelector('input').disabled = true;
            experienceGroup.querySelector('input').removeAttribute('required');
            experienceGroup.style.display = 'none';
        }
        // تغيير تسمية المهنة إلى مجال العمل
        var bioLabel = document.querySelector('label[for="bio"]');
        if (bioLabel) bioLabel.innerHTML = '<strong>مجال العمل:</strong>';
    }
})();
</script>
</body>
</html>