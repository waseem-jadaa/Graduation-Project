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
        // Read the suggested status from the hidden input, default to published if not set (shouldn't happen with JS flow)
        $status = $_POST['suggested_status'] ?? 'published';



        try {
            // Include the status column in the INSERT statement
            $stmt = $conn->prepare('INSERT INTO job (employer_ID, title, description, location, salary, status) VALUES (:employer_id, :title, :description, :location, :salary, :status)');
            $stmt->execute([
                ':employer_id' => $employer_id,
                ':title' => $title,
                ':description' => $description,
                ':location' => $location,
                ':salary' => $salary,
                ':status' => $status // Bind the status parameter
            ]);
            // Redirect to clear POST data
            if ($status === 'pending_review') {
                header('Location: job_post.php?pending_review=1');
            } else {
                header('Location: job_post.php?success=1');
            }
            exit();
        } catch (PDOException $e) {
            // Log and display specific database errors
            error_log("Database Error: " . $e->getMessage());
            echo '<p style="color:red;">حدث خطأ في قاعدة البيانات أثناء إضافة الوظيفة:</p>';
            echo '<p style="color:red;">' . htmlspecialchars($e->getMessage()) . '</p>';
            // Prevent redirect on error to display the message
            // header('Location: job_post.php?error=db');
            // exit();
        } catch (Exception $e) {
             // Catch any other exceptions
             error_log("General Error: " . $e->getMessage());
             echo '<p style="color:red;">حدث خطأ عام أثناء إضافة الوظيفة:</p>';
             echo '<p style="color:red;">' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    } else {
        echo '<p>Invalid CSRF token.</p>';
    }
} else {
    // Added for debugging: Indicate if the request is not POST or post-job is not set
    // error_log("Request method is not POST or post-job not set.");
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
        <?php
        if (isset($_GET['success'])) {
            echo '<div style="background:#e8f9f1;color:#1abc5b;padding:12px 18px;border-radius:7px;margin-bottom:18px;text-align:center;font-weight:bold;">تم نشر الوظيفة بنجاح!</div>';
        } elseif (isset($_GET['pending_review'])) {
            echo '<div style="background:#fffbe8;color:#e67e22;padding:12px 18px;border-radius:7px;margin-bottom:18px;text-align:center;font-weight:bold;">تم إرسال الوظيفة للمراجعة من قبل المسؤول، سيتم نشرها بعد الموافقة.</div>';
        }
        ?>
        <h2>إضافة وظيفة جديدة</h2>
        <form id="post-job-form" method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <!-- Added hidden input for suggested status from AI review -->
            <input type="hidden" id="suggested-status" name="suggested_status" value="">
            <!-- Ensure post-job is always sent even with JS submit -->
            <input type="hidden" name="post-job" value="1">
            <label for="job-title">العنوان:</label>
            <input type="text" id="job-title" name="title" required>

            <label for="job-description">الوصف:</label>
            <textarea id="job-description" name="description" required></textarea>
            <!-- زر تحسين الوصف بالذكاء الاصطناعي بشكل عصري -->
            <div style="margin:8px 0; display:flex; align-items:center; gap:8px; justify-content:flex-start; direction:rtl;">
                <img src="ai-logo.svg" id="improve-desc" style="width:38px;height:38px;cursor:pointer;vertical-align:middle;filter:drop-shadow(0 2px 6px #1abc5b44);transition:transform 0.2s;" title="تحسين الوصف" onmouseover="this.style.transform='scale(1.13)'" onmouseout="this.style.transform='scale(1)'" />
                <span id="improve-desc-text" onclick="runImproveDesc()" style="color:#6c47ff;font-weight:bold;cursor:pointer;font-size:1.08em;user-select:none;transition:color 0.2s;">تحسين الوصف</span>
                <!-- Original loading indicator for description improvement -->
                <span id="improve-loading" style="display:none;color:#1abc5b;font-size:0.95em;margin-right:8px;">جاري التحسين...</span>
            </div>

            <label for="job-location">الموقع:</label>
            <input type="text" id="job-location" name="location" required>

            <label for="job-salary">الراتب:</label>
            <input type="text" id="job-salary" name="salary" required>

            <button type="submit" name="post-job">إضافة الوظيفة</button>
            <!-- Added loading indicator for content review near the submit button -->
            <span id="review-loading" style="display:none;color:#1abc5b;font-size:0.95em;margin-left:15px;">جاري مراجعة محتوى الوظيفة...</span>
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
    // Use the correct loading indicator for description improvement
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
        // Hide the correct loading indicator
        document.getElementById('improve-loading').style.display = 'none';
    });
}
document.getElementById('improve-desc').onclick = runImproveDesc;
document.getElementById('improve-desc-text').onclick = runImproveDesc;

// New script for job content review before submission
document.getElementById('post-job-form').addEventListener('submit', function(event) {
    // Prevent default form submission
    event.preventDefault();

    const form = event.target;
    const titleInput = document.getElementById('job-title');
    const descriptionInput = document.getElementById('job-description');
    const suggestedStatusInput = document.getElementById('suggested-status');
    // Use the correct loading indicator for review
    const reviewLoadingSpan = document.getElementById('review-loading');

    const title = titleInput.value.trim();
    const description = descriptionInput.value.trim();

    if (!title || !description) {
        alert('يرجى ملء جميع الحقول المطلوبة.'); // Basic validation
        return;
    }

    // Show loading indicator for review
    reviewLoadingSpan.style.display = 'inline';

    // Send data to the review script
    fetch('review_job_content.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title: title, description: description })
    })
    .then(response => {
        reviewLoadingSpan.style.display = 'none'; // Hide loading indicator for review
        if (!response.ok) {
             // Handle HTTP errors
            return response.json().then(errorData => {
                console.error('HTTP Error during review:', errorData);
                alert('خطأ أثناء مراجعة المحتوى: ' + (errorData.error || response.statusText));
                throw new Error('Review failed'); // Stop further processing
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Review response data:', data);
        if (data.suggested_status) {
            // Set the suggested status in the hidden field
            suggestedStatusInput.value = data.suggested_status;
            console.log('Suggested status set to:', suggestedStatusInput.value);

            // Inform user if review is pending
            if (data.suggested_status === 'pending_review') {
                 alert('تم إرسال الوظيفة للمراجعة من قبل المسؤول قبل النشر.');
            } else {
                 alert('تمت مراجعة الوظيفة ويمكن نشرها مباشرة.'); // Optional: inform user it will be published
            }

            console.log('Attempting programmatic form submission...');
            // Now, submit the form programmatically
            form.submit();
            console.log('Form submission attempted.'); // This line might not run if submit navigates immediately

        } else if (data.error) {
            console.error('Error in review data:', data.error);
            alert('خطأ في استجابة المراجعة: ' + data.error);
        } else {
            console.error('Unexpected review response data:', data);
            alert('حدث خطأ غير متوقع بعد المراجعة.');
        }
    })
    .catch(error => {
        console.error('Fetch error during review:', error);
        reviewLoadingSpan.style.display = 'none'; // Ensure loading is hidden on error
         if (error.message !== 'Review failed') {
            alert('حدث خطأ أثناء الاتصال بخدمة المراجعة.');
         }
    });
});
</script>