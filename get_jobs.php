<?php
include 'db.php';

header('Content-Type: application/json');

try {
    session_start();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $latest = isset($_GET['latest']) ? intval($_GET['latest']) : 0;

    // Pagination
    $perPage = isset($_GET['perPage']) && is_numeric($_GET['perPage']) && $_GET['perPage'] > 0 ? (int)$_GET['perPage'] : 5;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;

    if ($latest) {
        // جلب آخر 5 وظائف منشورة فقط
        $stmt = $conn->prepare("SELECT job_ID, title, description, location FROM job WHERE status = 'published' ORDER BY job_ID DESC LIMIT 5");
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($jobs);
        exit;
    }

    // Count total
    if ($searchTerm !== '') {
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM job WHERE status = 'published' AND (title LIKE :searchTerm OR description LIKE :searchTerm OR location LIKE :searchTerm)");
        $countStmt->execute([':searchTerm' => "%$searchTerm%"]);
        $total = $countStmt->fetchColumn();
        $stmt = $conn->prepare("SELECT j.job_ID, j.title, j.description, j.location, j.salary, j.status, u.name as employer_name, (SELECT COUNT(*) FROM saved_jobs WHERE saved_jobs.job_id = j.job_ID AND saved_jobs.user_id = :uid) AS saved 
            FROM job j 
            JOIN user u ON j.employer_ID = u.User_ID 
            WHERE j.status = 'published' AND (j.title LIKE :searchTerm OR j.description LIKE :searchTerm OR j.location LIKE :searchTerm) 
            LIMIT :offset, :perPage");
        $stmt->bindValue(':searchTerm', "%$searchTerm%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM job WHERE status = 'published'");
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        $stmt = $conn->prepare("SELECT j.job_ID, j.title, j.description, j.location, j.salary, j.status, u.name as employer_name, (SELECT COUNT(*) FROM saved_jobs WHERE saved_jobs.job_id = j.job_ID AND saved_jobs.user_id = :uid) AS saved 
            FROM job j 
            JOIN user u ON j.employer_ID = u.User_ID 
            WHERE j.status = 'published' 
            LIMIT :offset, :perPage");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPages = ceil($total / $perPage);
    echo json_encode([
        'jobs' => $jobs,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'total' => $total
    ], JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch jobs']);
}
?>