<?php
// save_job.php
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
if (!$job_id) {
    echo json_encode(['error' => 'invalid_job']);
    exit;
}
try {
    // تحقق إذا كان محفوظ مسبقاً
    $stmt = $conn->prepare('SELECT COUNT(*) FROM saved_jobs WHERE user_id = :uid AND job_id = :jid');
    $stmt->execute([':uid' => $user_id, ':jid' => $job_id]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['error' => 'already_saved']);
        exit;
    }
    $stmt = $conn->prepare('INSERT INTO saved_jobs (user_id, job_id) VALUES (:uid, :jid)');
    $stmt->execute([':uid' => $user_id, ':jid' => $job_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'db_error']);
}
