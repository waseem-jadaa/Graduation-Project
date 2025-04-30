<?php
// call db
$host = "127.0.0.1";
$port = "3308"; 
$dbname = "job_portal";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // get data professionals from db
    $stmt = $conn->prepare("SELECT user.User_ID, user.name, profile.first_name, profile.last_name, profile.bio, profile.location, profile.experience 
FROM user 
INNER JOIN profile ON user.User_ID = profile.User_ID 
WHERE user.role = 'job_seeker'
");
    $stmt->execute();

    $professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // print JSON
    header('Content-Type: application/json');
    echo json_encode($professionals, JSON_PRETTY_PRINT);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
