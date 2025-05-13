<?php
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $first_name = htmlspecialchars($_POST['first_name'] ?? '');
    $last_name = htmlspecialchars($_POST['last_name'] ?? '');
    $bio = htmlspecialchars($_POST['bio'] ?? '');
    $skills = htmlspecialchars($_POST['skills'] ?? '');
    $location = htmlspecialchars($_POST['location'] ?? '');
    $experience = htmlspecialchars($_POST['experience'] ?? '');

    $profile_photo_path = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/profile_photos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileExtension = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $profile_photo_path = $uploadDir . $user_id . '_profile.' . $fileExtension;
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_photo_path);
    }

    try {
        if ($profile_photo_path) {
            $stmt = $conn->prepare('UPDATE profile SET first_name = :first_name, last_name = :last_name, bio = :bio, skills = :skills, location = :location, experience = :experience, profile_photo = :profile_photo WHERE User_ID = :user_id');
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':bio' => $bio,
                ':skills' => $skills,
                ':location' => $location,
                ':experience' => $experience,
                ':profile_photo' => $profile_photo_path,
                ':user_id' => $user_id
            ]);
        } else {
            $stmt = $conn->prepare('UPDATE profile SET first_name = :first_name, last_name = :last_name, bio = :bio, skills = :skills, location = :location, experience = :experience WHERE User_ID = :user_id');
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':bio' => $bio,
                ':skills' => $skills,
                ':location' => $location,
                ':experience' => $experience,
                ':user_id' => $user_id
            ]);
        }
        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        die("Error updating profile: " . $e->getMessage());
    }
}
?>
