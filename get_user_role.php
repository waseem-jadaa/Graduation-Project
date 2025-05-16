<?php
session_start();
include 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['role' => null, 'user_id' => null]);
    exit;
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT role FROM user WHERE User_ID = :user_id');
$stmt->execute([':user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode(['role' => $row ? $row['role'] : null, 'user_id' => $user_id]);
