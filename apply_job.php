<?php
// apply_job.php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

if (!$job_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid job id']);
    exit;
}

try {
    // Check if already applied
    $stmt = $conn->prepare('SELECT * FROM application WHERE job_ID = :job_id AND user_ID = :user_id AND status IN ("pending", "accepted")');
    $stmt->execute([':job_id' => $job_id, ':user_id' => $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => 'already_applied']);
        exit;
    }

    // Insert application
    $stmt = $conn->prepare('INSERT INTO application (job_ID, user_ID, status) VALUES (:job_id, :user_id, "pending")');
    $stmt->execute([':job_id' => $job_id, ':user_id' => $user_id]);

    // جلب اسم المتقدم
    $stmt = $conn->prepare('SELECT name FROM user WHERE User_ID = :user_id');
    $stmt->execute([':user_id' => $user_id]);
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);
    $applicant_name = $applicant ? htmlspecialchars($applicant['name']) : 'متقدم جديد';

    // Notify employer
    $stmt = $conn->prepare('SELECT employer_ID, title FROM job WHERE job_ID = :job_id');
    $stmt->execute([':job_id' => $job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($job) {
        $employer_id = $job['employer_ID'];
        $job_title = htmlspecialchars($job['title']);
        $message = "$applicant_name تقدم لوظيفة: $job_title. يمكنك قبول أو رفض الطلب.";
        $link = "manage_applications.php?job_id=$job_id";
        $stmt = $conn->prepare('INSERT INTO notifications (user_id, sender_id, message, link, is_read, created_at) VALUES (:user_id, :sender_id, :message, :link, 0, NOW())');
        $stmt->execute([':user_id' => $employer_id, ':sender_id' => $user_id, ':message' => $message, ':link' => $link]);
    }

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Database error during job application: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while processing your application. Please try again later.']);
} catch (Exception $e) {
    // Log any other unexpected errors
    error_log("Unexpected error during job application: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred. Please try again later.']);
}
