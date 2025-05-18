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

    if ($searchTerm !== '') {
        $stmt = $conn->prepare("SELECT user.User_ID, CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience, profile.id_photo 
        FROM user 
        INNER JOIN profile ON user.User_ID = profile.User_ID 
        WHERE user.role = 'job_seeker' AND (profile.first_name LIKE :searchTerm OR profile.last_name LIKE :searchTerm OR CONCAT(profile.first_name, ' ', profile.last_name) LIKE :searchTerm OR profile.location LIKE :searchTerm)");
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
    } else {
        $stmt = $conn->prepare("SELECT user.User_ID, CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience, profile.id_photo 
        FROM user 
        INNER JOIN profile ON user.User_ID = profile.User_ID 
        WHERE user.role = 'job_seeker'");
        $stmt->execute();
    }

    $professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    header('Content-Type: application/json');
    echo json_encode($professionals, JSON_PRETTY_PRINT);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
