<?php
include 'db.php';

header('Content-Type: application/json');

try {
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    $results = [];


    if ($searchTerm !== '') {
        // Search jobs (only published)
        $stmtJobs = $conn->prepare('SELECT j.job_ID, j.title, j.description, j.location, j.salary, u.name as employer_name 
            FROM job j 
            JOIN user u ON j.employer_ID = u.User_ID 
            WHERE j.status = "published" AND (j.title LIKE :searchTerm OR j.description LIKE :searchTerm OR j.location LIKE :searchTerm)');
        $stmtJobs->execute([':searchTerm' => "%$searchTerm%"]);
        $jobs = $stmtJobs->fetchAll(PDO::FETCH_ASSOC);

        foreach ($jobs as $job) {
            $results[] = [
                'type' => 'job',
                'job_ID' => $job['job_ID'],
                'title' => $job['title'],
                'description' => $job['description'],
                'location' => $job['location'],
                'salary' => $job['salary'],
                'employer_name' => $job['employer_name']
            ];
        }

        // Search professionals
        $stmtProfessionals = $conn->prepare("SELECT user.User_ID, CONCAT(profile.first_name, ' ', profile.last_name) AS name, profile.bio AS profession, profile.location, profile.experience, profile.profile_photo FROM user INNER JOIN profile ON user.User_ID = profile.User_ID WHERE user.role = 'job_seeker' AND (profile.first_name LIKE :searchTerm OR profile.last_name LIKE :searchTerm OR CONCAT(profile.first_name, ' ', profile.last_name) LIKE :searchTerm OR profile.location LIKE :searchTerm)");
        $stmtProfessionals->execute([':searchTerm' => "%$searchTerm%"]);
        $professionals = $stmtProfessionals->fetchAll(PDO::FETCH_ASSOC);

        foreach ($professionals as $professional) {
            $results[] = [
                'type' => 'professional',
                'id' => $professional['User_ID'],
                'name' => $professional['name'],
                'profession' => $professional['profession'],
                'location' => $professional['location'],
                'experience' => $professional['experience'],
                'profile_photo' => $professional['profile_photo']
            ];
        }
    }

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch results']);
}
?>
