<?php
// remove_saved_job.php
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit();
}
$user_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
if ($job_id) {
    $stmt = $conn->prepare('DELETE FROM saved_jobs WHERE user_id = :uid AND job_id = :jid');
    $stmt->execute([':uid' => $user_id, ':jid' => $job_id]);
    echo json_encode(['success' => true]);
    exit();
}
echo json_encode(['error' => 'invalid_job_id']);
exit();
