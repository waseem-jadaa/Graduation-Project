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

// Check if already applied
$stmt = $conn->prepare('SELECT * FROM application WHERE job_ID = :job_id AND user_ID = :user_id');
$stmt->execute([':job_id' => $job_id, ':user_id' => $user_id]);
if ($stmt->fetch()) {
    echo json_encode(['error' => 'already_applied']);
    exit;
}

// Insert application
$stmt = $conn->prepare('INSERT INTO application (job_ID, user_ID, status) VALUES (:job_id, :user_id, "pending")');
$stmt->execute([':job_id' => $job_id, ':user_id' => $user_id]);

// Notify employer
$stmt = $conn->prepare('SELECT employer_ID, title FROM job WHERE job_ID = :job_id');
$stmt->execute([':job_id' => $job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);
if ($job) {
    $employer_id = $job['employer_ID'];
    $job_title = htmlspecialchars($job['title']);
    $message = "تقدم شخص جديد لوظيفة: $job_title. يمكنك قبول أو رفض الطلب.";
    $link = "manage_applications.php?job_id=$job_id";
    $stmt = $conn->prepare('INSERT INTO notifications (user_id, message, link, is_read, created_at) VALUES (:user_id, :message, :link, 0, NOW())');
    $stmt->execute([':user_id' => $employer_id, ':message' => $message, ':link' => $link]);
}

echo json_encode(['success' => true]);
