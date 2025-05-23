<?php
// صفحة رفع وثائق التوثيق
include 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// جلب حالة التوثيق الحالية
$stmt = $conn->prepare('SELECT verification_status, verification_note FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$verification_status = $row['verification_status'] ?? 'not_verified';
$verification_note = $row['verification_note'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc_type = $_POST['doc_type'] ?? '';
    $legal_name = $_POST['legal_name'] ?? '';
    $allowed_types = ['ID', 'Passport', 'Licence'];
    if (empty($legal_name)) {
        $error = 'يرجى إدخال الاسم القانوني';
    } elseif (!in_array($doc_type, $allowed_types)) {
        $error = 'نوع الوثيقة غير صالح';
    } elseif (!isset($_FILES['doc_file']) || $_FILES['doc_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'يرجى رفع صورة الوثيقة.';
    } else {
        $ext = strtolower(pathinfo($_FILES['doc_file']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed_ext)) {
            $error = 'صيغة الملف غير مدعومة.';
        } elseif ($_FILES['doc_file']['size'] > 2*1024*1024) {
            $error = 'حجم الملف يجب أن لا يتجاوز 2 ميجابايت.';
        } else {
            $uploadDir = 'uploads/verification_docs/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
            $fileName = $user_id . '_' . time() . '.' . $ext;
            $filePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['doc_file']['tmp_name'], $filePath)) {
                // حفظ الطلب في جدول جديد
                $stmt = $conn->prepare('INSERT INTO user_verification (user_id, doc_type, doc_path, legal_name, status, created_at) VALUES (:user_id, :doc_type, :doc_path, :legal_name, "pending", NOW())');
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':doc_type' => $doc_type,
                    ':doc_path' => $filePath,
                    ':legal_name' => $legal_name
                ]);
                // تحديث حالة المستخدم
                $stmt = $conn->prepare('UPDATE user SET verification_status = "pending" WHERE User_ID = :user_id');
                $stmt->execute([':user_id' => $user_id]);
                $success = true;
                $verification_status = 'pending';
            } else {
                $error = 'فشل رفع الملف.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>توثيق الحساب</title>
    <link rel="stylesheet" href="css/project.css">
    <style>
        .verify-container { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); padding: 30px; }
        .verify-container h2 { color: #1abc5b; }
        .verify-container label { font-weight: bold; }
        .verify-container input, .verify-container select { width: 100%; margin-bottom: 15px; padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        .verify-container button { background: #1abc5b; color: #fff; border: none; padding: 10px 30px; border-radius: 5px; font-size: 1.1em; cursor: pointer; }
        .verify-container .msg { margin: 15px 0; color: #1abc5b; font-weight: bold; }
        .verify-container .error { color: #e74c3c; }
    </style>
</head>
<body>
<?php include 'headerDash.php'; ?>
<div class="verify-container">
    <h2>توثيق الحساب</h2>
    <p>للحصول على علامة التوثيق الزرقاء، يرجى رفع إحدى الوثائق الرسمية التالية لإثبات هويتك.</p>
    <ul>
        <li>بطاقة هوية (ID)</li>
        <li>جواز سفر (Passport)</li>
        <li>رخصة قيادة (Licence)</li>
    </ul>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="msg">تم إرسال طلبك بنجاح. سيتم مراجعته خلال 72 ساعة.</div>
    <?php elseif ($verification_status === 'pending'): ?>
        <div class="msg">طلبك قيد المراجعة.</div>
    <?php elseif ($verification_status === 'verified'): ?>
        <div class="msg">تم توثيق حسابك ✅</div>
    <?php elseif ($verification_status === 'rejected'): ?>
        <div class="error">عذرًا، بياناتك غير متطابقة ولا يمكننا توثيق حسابك.<br><?php echo htmlspecialchars($verification_note); ?></div>
    <?php endif; ?>
    <?php if ($verification_status !== 'verified' && $verification_status !== 'pending'): ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="legal_name">الاسم القانوني:</label>
        <input type="text" name="legal_name" id="legal_name" required>
        <label for="doc_type">نوع الوثيقة:</label>
        <select name="doc_type" id="doc_type" required>
            <option value="">اختر نوع الوثيقة</option>
            <option value="ID">بطاقة هوية</option>
            <option value="Passport">جواز سفر</option>
            <option value="Licence">رخصة قيادة</option>
        </select>
        <label for="doc_file">صورة الوثيقة:</label>
        <input type="file" name="doc_file" id="doc_file" accept="image/*" required>
        <button type="submit">إرسال طلب التوثيق</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
