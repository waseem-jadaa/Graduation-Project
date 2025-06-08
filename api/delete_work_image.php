<?php
ini_set('display_errors', 0); // Disable display errors on production for security
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
session_start();
require_once '../db.php'; // Changed from config.php to db.php
global $conn; // Ensure $conn is globally accessible

// Simple logging function for delete operations
function log_message($message) {
    file_put_contents('delete_log.txt', date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}

header('Content-Type: application/json');

log_message('Delete script started.');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_message('Invalid method: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$image_number = $data['image_number'] ?? null;

log_message('Received image_number: ' . ($image_number ?? 'NULL'));

if (!$image_number || $image_number < 1 || $image_number > 5) {
    log_message('Invalid image number provided: ' . ($image_number ?? 'NULL'));
    echo json_encode(['success' => false, 'error' => 'رقم الصورة غير صالح']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    log_message('User not logged in. Session user_id is missing.');
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
log_message('User ID: ' . $user_id);

// Fetch the current image path from the database before deleting
$image_path_to_delete = null;
log_message('Attempting to fetch current image path for work_image_' . $image_number);

try {
    log_message('$conn type before fetch: ' . gettype($conn) . (is_object($conn) ? ', PDO class: ' . get_class($conn) : ''));
    $stmt_fetch = $conn->prepare("SELECT work_image_$image_number FROM profile WHERE User_ID = :user_id");
    $stmt_fetch->execute([':user_id' => $user_id]);
    $result_fetch = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

    if ($result_fetch) {
        $image_path_to_delete = $result_fetch['work_image_' . $image_number];
        log_message('Fetched image path from DB: ' . ($image_path_to_delete ?? 'NULL'));
    } else {
        log_message('Could not fetch image path for slot ' . $image_number . '. User profile not found or image slot empty.');
    }

} catch (PDOException $e) {
    log_message('Database fetch error before delete: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error fetching image path.', 'details' => $e->getMessage()]);
    exit;
}

// Delete the image path from the database
log_message('Attempting to delete image path from database for slot ' . $image_number . '.');
log_message('$conn type before update: ' . gettype($conn) . (is_object($conn) ? ', PDO class: ' . get_class($conn) : ''));

try {
    $stmt = $conn->prepare("UPDATE profile SET work_image_$image_number = NULL WHERE User_ID = :user_id");
    if ($stmt->execute([':user_id' => $user_id])) {
        log_message('Database updated successfully. work_image_' . $image_number . ' set to NULL.');

        // Attempt to delete the physical file from the server if path exists
        if ($image_path_to_delete && file_exists(__DIR__ . '/..' . $image_path_to_delete)) {
            $absolute_path = __DIR__ . '/..' . $image_path_to_delete;
            log_message('Attempting to delete physical file: ' . $absolute_path);
            if (unlink($absolute_path)) {
                log_message('Physical file deleted successfully: ' . $absolute_path);
            } else {
                log_message('Failed to delete physical file: ' . $absolute_path);
            }
        } else if ($image_path_to_delete) {
            log_message('Physical file not found at: ' . (__DIR__ . '/..' . $image_path_to_delete) . '. Skipping physical deletion.');
        } else {
            log_message('No image path found to delete physical file.');
        }

        echo json_encode(['success' => true, 'message' => 'تم حذف الصورة بنجاح.']);
    } else {
        $error_info = $stmt->errorInfo();
        log_message('Database update failed. Error: ' . ($error_info[2] ?? 'Unknown error'));
        echo json_encode(['success' => false, 'error' => 'فشل تحديث قاعدة البيانات.', 'details' => ($error_info[2] ?? 'Unknown error')]);
    }
} catch (PDOException $e) {
    log_message('Database update error during delete: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'خطأ في قاعدة البيانات أثناء الحذف.', 'details' => $e->getMessage()]);
}

log_message('Delete script finished.');
?> 