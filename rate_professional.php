<?php
// rate_professional.php
// endpoint لإضافة تقييم مهني من صاحب عمل
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}
$employer_id = $_SESSION['user_id'];
$professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review = isset($_POST['review']) ? trim($_POST['review']) : '';
if (!$professional_id || $rating < 1 || $rating > 5) {
    echo json_encode(['error' => 'invalid_data']);
    exit;
}
// تحقق أن صاحب العمل تعامل مع المهني (له طلب مقبول)
$stmt = $conn->prepare('SELECT COUNT(*) FROM application WHERE employer_id = :eid AND user_ID = :pid AND status = "accepted"');
$stmt->execute([':eid' => $employer_id, ':pid' => $professional_id]);
if ($stmt->fetchColumn() == 0) {
    echo json_encode(['error' => 'not_allowed']);
    exit;
}
// تحقق من عدم وجود تقييم سابق
$stmt = $conn->prepare('SELECT COUNT(*) FROM professional_ratings WHERE employer_id = :eid AND professional_id = :pid');
$stmt->execute([':eid' => $employer_id, ':pid' => $professional_id]);
if ($stmt->fetchColumn() > 0) {
    echo json_encode(['error' => 'already_rated']);
    exit;
}
$stmt = $conn->prepare('INSERT INTO professional_ratings (professional_id, employer_id, rating, review) VALUES (:pid, :eid, :rating, :review)');
$stmt->execute([
    ':pid' => $professional_id,
    ':eid' => $employer_id,
    ':rating' => $rating,
    ':review' => $review
]);
echo json_encode(['success' => true]);
