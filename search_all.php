<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    $results = [];

    if ($searchTerm !== '') {
        // Search jobs
        $stmtJobs = $conn->prepare('SELECT title, description, location, salary FROM job WHERE title LIKE :searchTerm OR description LIKE :searchTerm OR location LIKE :searchTerm');
        $stmtJobs->execute([':searchTerm' => "%$searchTerm%"]);
        $jobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);

        foreach ($jobs as $job) {
            $results[] = [
                'type' => 'job',
                'title' => $job['title'],
                'description' => $job['description'],
                'location' => $job['location'],
                'salary' => $job['salary']
            ];
        }

        // Search professionals
        $stmtProfessionals = $conn->prepare("SELECT CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience FROM user INNER JOIN profile ON user.User_ID = profile.User_ID WHERE user.role = 'job_seeker' AND (profile.first_name LIKE :searchTerm OR profile.last_name LIKE :searchTerm OR CONCAT(profile.first_name, ' ', profile.last_name) LIKE :searchTerm OR profile.location LIKE :searchTerm)");
        $stmtProfessionals->execute([':searchTerm' => "%$searchTerm%"]);
        $professionals = $stmtProfessionals->fetchAll(PDO::FETCH_ASSOC);

        foreach ($professionals as $professional) {
            $results[] = [
                'type' => 'professional',
                'name' => $professional['name'],
                'profession' => $professional['profession'],
                'location' => $professional['location'],
                'experience' => $professional['experience']
            ];
        }
    }

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch results']);
}
?>
