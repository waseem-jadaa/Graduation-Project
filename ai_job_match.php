<?php
// ai_job_match.php
session_start();
header('Content-Type: application/json');
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$stmt = $conn->prepare('SELECT bio, skills FROM profile WHERE User_ID = :uid');
$stmt->execute([':uid' => $user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$profile) {
    echo json_encode(['error' => 'User profile not found']);
    exit();
}

$user_bio = strtolower(trim($profile['bio'] ?? ''));
$user_skills = array_filter(array_map('trim', explode(',', strtolower($profile['skills'] ?? ''))));

// Fetch all published jobs
$stmt = $conn->prepare('SELECT j.job_ID, j.title, j.description, j.location, j.salary, u.name as employer_name, 
    (SELECT COUNT(*) FROM saved_jobs WHERE saved_jobs.job_id = j.job_ID AND saved_jobs.user_id = :uid) AS saved 
    FROM job j 
    JOIN user u ON j.employer_ID = u.User_ID 
    WHERE j.status = "published"');
$stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$recommendations = [];
foreach ($jobs as $job) {
    $score = 0;
    $job_title = strtolower($job['title']);
    $job_desc = strtolower($job['description']);
    // Match skills
    foreach ($user_skills as $skill) {
        if ($skill && (strpos($job_title, $skill) !== false || strpos($job_desc, $skill) !== false)) {
            $score += 2; // Skill match
        }
    }
    // Match profession/bio
    if ($user_bio && (strpos($job_title, $user_bio) !== false || strpos($job_desc, $user_bio) !== false)) {
        $score += 3; // Profession match
    }
    // Add to recommendations if score > 0
    if ($score > 0) {
        $job['match_score'] = $score;
        $recommendations[] = $job;
    }
}
// Sort by match score descending
usort($recommendations, function($a, $b) {
    return $b['match_score'] <=> $a['match_score'];
});
// Return top 5
$top_recommendations = array_slice($recommendations, 0, 5);
echo json_encode(['recommended_jobs' => $top_recommendations]); 