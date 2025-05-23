<?php
// صفحة الإعدادات للمستخدم
// مبدئياً، هذه الصفحة تعرض إعدادات وهمية ويمكن ربطها بقاعدة البيانات لاحقاً
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        $change_pass_msg = 'تم تغيير كلمة المرور بنجاح (وهمياً).';
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
    <style>
        /* إصلاح تضارب project.css مع headerDash.css بحيث تظهر عناصر السايدبار دائماً */
        .sidebar-drawer .menu-item {
            color: #222 !important;
            background: none !important;
            opacity: 1 !important;
        }
        .sidebar-drawer .menu-item span, .sidebar-drawer .menu-item i {
            opacity: 1 !important;
            color: inherit !important;
        }
        .sidebar-drawer .menu-item {
            transition: background 0.2s, color 0.2s;
        }
        .sidebar-drawer .menu-item:hover, .sidebar-drawer .menu-item.active {
            background: #f5f7fa !important;
            color: var(--sidebar-accent2) !important;
        }
    </style>
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
            <div class="msg"> <?php echo $change_pass_msg; ?> </div>
        </form>
    </div>
    <script src="headerDash.js"></script>
</body>
</html>
