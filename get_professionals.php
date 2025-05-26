<?php

$host = "127.0.0.1";
$port = "3308"; 
$dbname = "job_portal";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // get data professionals from db
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';


    // Pagination
    $perPage = 3; // عدد العناصر في كل صفحة
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;

    // Count total
    if ($searchTerm !== '') {
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM user INNER JOIN profile ON user.User_ID = profile.User_ID WHERE user.role = 'job_seeker' AND (profile.first_name LIKE :searchTerm OR profile.last_name LIKE :searchTerm OR CONCAT(profile.first_name, ' ', profile.last_name) LIKE :searchTerm OR profile.location LIKE :searchTerm)");
        $countStmt->execute([':searchTerm' => "%$searchTerm%"]);
        $total = $countStmt->fetchColumn();
        $stmt = $conn->prepare("SELECT user.User_ID, CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience 
        FROM user 
        INNER JOIN profile ON user.User_ID = profile.User_ID 
        WHERE user.role = 'job_seeker' AND (profile.first_name LIKE :searchTerm OR profile.last_name LIKE :searchTerm OR CONCAT(profile.first_name, ' ', profile.last_name) LIKE :searchTerm OR profile.location LIKE :searchTerm)
        LIMIT :offset, :perPage");
        $stmt->bindValue(':searchTerm', "%$searchTerm%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $countStmt = $conn->prepare("SELECT COUNT(*) FROM user INNER JOIN profile ON user.User_ID = profile.User_ID WHERE user.role = 'job_seeker'");
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        $stmt = $conn->prepare("SELECT user.User_ID, CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience 
        FROM user 
        INNER JOIN profile ON user.User_ID = profile.User_ID 
        WHERE user.role = 'job_seeker'
        LIMIT :offset, :perPage");
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
    }

    $professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalPages = ceil($total / $perPage);

    header('Content-Type: application/json');
    echo json_encode([
        'professionals' => $professionals,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'total' => $total
    ], JSON_PRETTY_PRINT);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>