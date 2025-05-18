<?php
// get_professional_rating.php
// إرجاع متوسط التقييم وعدد التقييمات لمهني معين
include 'db.php';
header('Content-Type: application/json');
$professional_id = isset($_GET['professional_id']) ? intval($_GET['professional_id']) : 0;
if (!$professional_id) {
    echo json_encode(['error' => 'invalid_professional']);
    exit;
}
$stmt = $conn->prepare('SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM professional_ratings WHERE professional_id = :pid');
$stmt->execute([':pid' => $professional_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode([
    'avg_rating' => round($row['avg_rating'], 2),
    'total' => intval($row['total'])
]);
