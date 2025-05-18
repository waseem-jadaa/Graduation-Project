<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $latest = isset($_GET['latest']) ? intval($_GET['latest']) : 0;

    if ($latest) {
        // جلب آخر 5 وظائف مضافة (معالجة في حال لم يوجد عمود created_at)
        $stmt = $conn->prepare('SELECT job_ID, title, description, location FROM job ORDER BY job_ID DESC LIMIT 5');
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($jobs);
        exit;
    }

    if ($searchTerm !== '') {
        $stmt = $conn->prepare('SELECT title, description, location, salary FROM job WHERE title LIKE :searchTerm OR description LIKE :searchTerm OR location LIKE :searchTerm');
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
    } else {
        $stmt = $conn->prepare('SELECT title, description, location, salary FROM job');
        $stmt->execute();
    }
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($jobs);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch jobs']);
}
?>