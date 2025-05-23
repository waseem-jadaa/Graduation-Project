<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
include 'db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch user name from database
$user_id = $_SESSION['user_id'];
$user_name = '';
try {
    $stmt = $conn->prepare('SELECT name FROM user WHERE User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $user_name = htmlspecialchars($row['name']);
    } else {
        $user_name = 'مستخدم';
    }
} catch (PDOException $e) {
    $user_name = 'مستخدم';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post-job'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $location = $_POST['location'];
        $salary = $_POST['salary'];
        $employer_id = $_SESSION['user_id'];

        try {
            $stmt = $conn->prepare('INSERT INTO job (employer_ID, title, description, location, salary) VALUES (:employer_id, :title, :description, :location, :salary)');
            $stmt->execute([
                ':employer_id' => $employer_id,
                ':title' => $title,
                ':description' => $description,
                ':location' => $location,
                ':salary' => $salary
            ]);
            // Redirect to clear POST data
            header('Location: job_post.php?success=1');
            exit();
        } catch (PDOException $e) {
            echo '<p>حدث خطأ أثناء إضافة الوظيفة.</p>';
        }
    } else {
        echo '<p>Invalid CSRF token.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعلان عن وظيفة</title>
    <link rel="stylesheet" href="css/project.css">
</head>
<body>
    <?php include 'headerDash.php'; ?>
    <!-- تم إلغاء استدعاء السايدبار القديم -->

    <div class="post-job-section">
        <h2>إضافة وظيفة جديدة</h2>
        <form id="post-job-form" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <label for="job-title">العنوان:</label>
            <input type="text" id="job-title" name="title" required>

            <label for="job-description">الوصف:</label>
            <textarea id="job-description" name="description" required></textarea>
            <!-- زر تحسين الوصف بالذكاء الاصطناعي بشكل عصري -->
            <div style="margin:8px 0; display:flex; align-items:center; gap:8px; justify-content:flex-start; direction:rtl;">
                <img src="ai-logo.svg" id="improve-desc" style="width:38px;height:38px;cursor:pointer;vertical-align:middle;filter:drop-shadow(0 2px 6px #1abc5b44);transition:transform 0.2s;" title="تحسين الوصف" onmouseover="this.style.transform='scale(1.13)'" onmouseout="this.style.transform='scale(1)'" />
                <span id="improve-desc-text" onclick="runImproveDesc()" style="color:#6c47ff;font-weight:bold;cursor:pointer;font-size:1.08em;user-select:none;transition:color 0.2s;">تحسين الوصف</span>
                <span id="improve-loading" style="display:none;color:#1abc5b;font-size:0.95em;margin-right:8px;">جاري التحسين...</span>
            </div>

            <label for="job-location">الموقع:</label>
            <input type="text" id="job-location" name="location" required>

            <label for="job-salary">الراتب:</label>
            <input type="text" id="job-salary" name="salary" required>

            <button type="submit" name="post-job">إضافة الوظيفة</button>
        </form>
    </div>

</body>
</html>
<script>
function runImproveDesc() {
    const desc = document.getElementById('job-description').value;
    if (!desc.trim()) {
        alert('يرجى إدخال وصف أولاً');
        return;
    }
    document.getElementById('improve-loading').style.display = 'inline';
    fetch('improve_description.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ description: desc })
    })
    .then(res => res.json())
    .then(data => {
        if (data.job_description) {
            document.getElementById('job-description').value = data.job_description;
        } else if (data.error) {
            alert('خطأ: ' + data.error);
        } else {
            alert('حدث خطأ غير متوقع.');
        }
    })
    .catch(() => {
        alert('حدث خطأ أثناء الاتصال بخدمة التحسين.');
    })
    .finally(() => {
        document.getElementById('improve-loading').style.display = 'none';
    });
}
document.getElementById('improve-desc').onclick = runImproveDesc;
document.getElementById('improve-desc-text').onclick = runImproveDesc;
</script>