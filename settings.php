<?php
// صفحة الإعدادات للمستخدم
// مبدئياً، هذه الصفحة تعرض إعدادات وهمية ويمكن ربطها بقاعدة البيانات لاحقاً
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; // تأكد من تضمين ملف الاتصال بقاعدة البيانات
include 'headerDash.php';

// معالجة تغيير كلمة المرور (مثال بسيط)
$change_pass_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';
    if ($new_pass !== $confirm_pass) {
        $change_pass_msg = 'كلمة المرور الجديدة غير متطابقة.';
    } else {
        // هنا من المفترض التحقق من كلمة المرور القديمة وتحديث الجديدة في قاعدة البيانات
        // مثال بسيط: تحقق من كلمة المرور القديمة (استخدم الهاش في بيئة الإنتاج)
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare('SELECT password FROM `user` WHERE User_ID = :user_id');
        $stmt->execute([':user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($old_pass, $user['password'])) {
            $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('UPDATE `user` SET password = :new_pass WHERE User_ID = :user_id');
            $stmt->execute([':new_pass' => $hashed_new_pass, ':user_id' => $user_id]);
            $change_pass_msg = 'تم تغيير كلمة المرور بنجاح.';
        } else {
            $change_pass_msg = 'كلمة المرور الحالية غير صحيحة.';
        }
    }
}

// معالجة تحديث إعدادات البريد الإلكتروني
$settings_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_email_settings'])) {
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    
    try {
        $stmt = $conn->prepare("UPDATE `user` SET email_notifications = :email_notifications WHERE User_ID = :user_id");
        $stmt->execute([
            ':email_notifications' => $email_notifications,
            ':user_id' => $_SESSION['user_id']
        ]);
        $settings_msg = 'تم تحديث إعدادات البريد الإلكتروني بنجاح';
    } catch (PDOException $e) {
        $settings_msg = 'حدث خطأ أثناء تحديث الإعدادات: ' . $e->getMessage();
        error_log("Error updating email settings: " . $e->getMessage());
    }
}

// جلب الإعدادات الحالية للمستخدم لعرضها في النموذج
$user_email_settings = ['email_notifications' => TRUE]; // قيمة افتراضية
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("SELECT email_notifications FROM `user` WHERE User_ID = :user_id");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $user_email_settings = $result;
        }
    } catch (PDOException $e) {
        error_log("Error fetching email settings: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإعدادات</title>
    <link rel="stylesheet" href="css/settings.css">
    <link rel="stylesheet" href="css/project.css">
</head>
<body>
    <div class="settings-container">
        <h2>الإعدادات</h2>
        <form class="settings-form" method="post">
            <div class="setting-group">
                <label for="mode">وضع الموقع:</label>
                <select id="mode" name="mode">
                    <option value="light">فاتح</option>
                    <option value="dark">داكن</option>
                </select>
            </div>
            <div class="setting-group">
                <label for="notifications">الإشعارات:</label>
                <input type="checkbox" id="notifications" name="notifications" checked>
                <span>تشغيل/إيقاف</span>
            </div>
            <div class="setting-group">
                <label for="mute_time">كتم الإشعارات في وقت محدد:</label>
                <input type="time" id="mute_start" name="mute_start"> إلى 
                <input type="time" id="mute_end" name="mute_end">
            </div>
            <div class="setting-group">
                <label for="lang">اللغة:</label>
                <select id="lang" name="lang">
                    <option value="ar">العربية</option>
                    <option value="en">English</option>
                </select>
            </div>
            <div class="setting-group">
                <label for="privacy">الخصوصية:</label>
                <select id="privacy" name="privacy">
                    <option value="public">عام</option>
                    <option value="private">خاص</option>
                </select>
            </div>
            <button type="submit" class="save-btn">حفظ التغييرات</button>
        </form>
        <hr>
        <!-- قسم إعدادات البريد الإلكتروني -->
        <form class="email-settings-form" method="post">
            <h3>إعدادات البريد الإلكتروني</h3>
            <div class="setting-group">
                <label for="email_notifications">إشعارات البريد الإلكتروني للوظائف الجديدة:</label>
                <input type="checkbox" id="email_notifications" name="email_notifications" 
                       <?php echo $user_email_settings['email_notifications'] ? 'checked' : ''; ?>>
                <span>استلام إشعارات بالبريد الإلكتروني عند نشر وظائف جديدة</span>
            </div>
            <button type="submit" name="update_email_settings" class="save-btn">حفظ إعدادات البريد الإلكتروني</button>
            <?php if (isset($settings_msg)): ?>
                <div class="msg <?php echo (strpos($settings_msg, 'بنجاح') !== false) ? 'success-msg' : 'error-msg'; ?>">
                    <?php echo $settings_msg; ?>
                </div>
            <?php endif; ?>
        </form>
        <hr>
        <form class="change-password-form" method="post">
            <h3>تغيير كلمة المرور</h3>
            <div class="setting-group">
                <label for="old_password">كلمة المرور الحالية:</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div class="setting-group">
                <label for="new_password">كلمة المرور الجديدة:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="setting-group">
                <label for="confirm_password">تأكيد كلمة المرور الجديدة:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="save-btn">تغيير كلمة المرور</button>
            <div class="msg <?php echo (strpos($change_pass_msg, 'بنجاح') !== false) ? 'success-msg' : 'error-msg'; ?>"> <?php echo $change_pass_msg; ?> </div>
        </form>
    </div>
    <script src="js/headerDash.js"></script>
</body>
</html>
