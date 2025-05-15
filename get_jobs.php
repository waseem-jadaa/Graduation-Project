<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->prepare('SELECT job_ID, title, description, location, salary FROM job');
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($jobs);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch jobs']);
}
?>