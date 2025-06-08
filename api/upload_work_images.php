<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
session_start();
require_once '../db.php';
global $conn;

// Simple logging function
function log_message($message) {
    file_put_contents('upload_log.txt', date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}

log_message('Upload script started.');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    log_message('Invalid method: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Check if user_type is set in session
if (!isset($_SESSION['user_type'])) {
     log_message('user_type not set in session.');
     http_response_code(403);
     echo json_encode(['error' => 'Session user type not set.']);
     exit;
}

log_message('User type in session: ' . $_SESSION['user_type']);

if ($_SESSION['user_type'] !== 'job_seeker') {
    log_message('User type is not job_seeker: ' . $_SESSION['user_type']);
    http_response_code(403);
    echo json_encode(['error' => 'غير مصرح لك برفع الصور']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
     log_message('user_id not set in session.');
     http_response_code(403);
     echo json_encode(['error' => 'Session user ID not set.']);
     exit;
}

$user_id = $_SESSION['user_id'];
log_message('User ID set: ' . $user_id);

// Determine if it's a change operation or a new upload
$is_change = isset($_POST['is_change']) && $_POST['is_change'] === 'true';
$target_image_number = $is_change ? (int)($_POST['image_number'] ?? 0) : 0;

log_message('Operation type: ' . ($is_change ? 'Change' : 'New Upload') . ', Target image number (if change): ' . $target_image_number);

// Log before accessing $_FILES
log_message('Checking $_FILES variable.');

// Check if files were actually uploaded
if (!isset($_FILES['work_images']) || empty($_FILES['work_images']['name'][0])) {
     log_message('No files uploaded or empty file array.');
     echo json_encode(['error' => 'لم يتم اختيار أي صور للرفع.']);
     exit;
}

log_message('Files variable seems okay. Processing uploads.');

$uploaded_files_array = $_FILES['work_images']; // This will be an array for multiple files

$response_messages = [];
$overall_success = false;

$upload_dir = '../uploads/work_images/';
if (!is_dir($upload_dir)) {
    log_message('Upload directory does not exist, attempting to create: ' . $upload_dir);
    if (!mkdir($upload_dir, 0777, true)) {
        log_message('Failed to create upload directory.');
        echo json_encode(['error' => 'فشل إنشاء مجلد رفع الصور.']);
        exit;
    }
     log_message('Upload directory created.');
}

// Fetch current images for new uploads only (once per request)
$current_images_db = [];
if (!$is_change) {
    try {
        log_message('Fetching current images for new upload slot determination.');
        $stmt_current = $conn->prepare("SELECT work_image_1, work_image_2, work_image_3, work_image_4, work_image_5 FROM profile WHERE user_id = ?");
        $stmt_current->execute([$user_id]);
        $current_images_db = $stmt_current->fetch(PDO::FETCH_ASSOC);
        log_message('Current images from DB for slot determination: ' . print_r($current_images_db, true));
    } catch (PDOException $e) {
        log_message('Database error fetching current images for new upload slot determination: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'خطأ في قاعدة البيانات عند تحديد خانة الصورة.', 'details' => $e->getMessage()]);
        exit;
    }
}

log_message('Starting file processing loop. Number of files to process: ' . count($uploaded_files_array['name']));

for ($i = 0; $i < count($uploaded_files_array['name']); $i++) {
    $file_error = $uploaded_files_array['error'][$i];
    $tmp_name = $uploaded_files_array['tmp_name'][$i];
    $file_name = $uploaded_files_array['name'][$i];

    log_message('Processing file ' . ($i + 1) . ' of ' . count($uploaded_files_array['name']) . ': ' . $file_name);

    if ($file_error === UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        log_message('File name: ' . $file_name . ', Extension: ' . $file_ext);

        $allowed = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_ext, $allowed)) {
            $response_messages[] = "نوع الملف غير مسموح به: $file_name";
            log_message('Disallowed file type: ' . $file_name);
            continue; // Skip this file, but continue with others
        }

        $new_name = uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_name;
        $db_path = str_replace('../', '/forsa-pal/', $upload_path); // Path for database storage

        log_message('Attempting to move uploaded file ' . $tmp_name . ' to ' . $upload_path);

        if (move_uploaded_file($tmp_name, $upload_path)) {
            log_message('File moved successfully: ' . $upload_path);

            $slot_to_update = 0;
            if ($is_change) {
                // For a change operation, use the provided target_image_number
                $slot_to_update = $target_image_number;
                log_message('Change operation: target slot is ' . $slot_to_update);

                // Delete old physical file before updating
                try {
                    $stmt_fetch_old = $conn->prepare("SELECT work_image_$slot_to_update FROM profile WHERE user_id = ?");
                    $stmt_fetch_old->execute([$user_id]);
                    $old_image_path = $stmt_fetch_old->fetchColumn();
                    if ($old_image_path && file_exists(__DIR__ . '/..' . str_replace('/forsa-pal/', '/', $old_image_path))) {
                        $abs_old_path = __DIR__ . '/..' . str_replace('/forsa-pal/', '/', $old_image_path);
                        log_message('Attempting to delete old physical file: ' . $abs_old_path);
                        if (unlink($abs_old_path)) {
                            log_message('Old physical file deleted successfully.');
                        } else {
                            log_message('Failed to delete old physical file.');
                        }
                    } else if ($old_image_path) {
                        log_message('Old physical file not found at: ' . (__DIR__ . '/..' . str_replace('/forsa-pal/', '/', $old_image_path)) . '. Skipping deletion.');
                    } else {
                        log_message('No old image path found for slot ' . $slot_to_update . '.');
                    }
                } catch (PDOException $e) {
                    log_message('Error fetching/deleting old image path: ' . $e->getMessage());
                }

            } else {
                // For new uploads, find the first available slot
                log_message('New upload operation: finding first available slot for current file.');
                $current_count = 0;
                foreach($current_images_db as $img) {
                    if (!empty($img)) $current_count++;
                }
                log_message('Current image count in DB: ' . $current_count);

                if ($current_count >= 5) {
                    log_message('Upload limit reached (5 images) for new upload. No empty slot for ' . $file_name);
                    $response_messages[] = "يمكنك رفع 5 صور كحد أقصى. لم يتم رفع $file_name.";
                    unlink($upload_path); // Clean up the newly uploaded file
                    continue; // Skip this file, but continue with others
                }

                for ($j = 1; $j <= 5; $j++) {
                    if (empty($current_images_db["work_image_$j"])) {
                        $slot_to_update = $j;
                        log_message('Found empty slot for new upload: ' . $slot_to_update . ' for file ' . $file_name);
                        break; // Found a slot, exit loop
                    }
                }
                if ($slot_to_update === 0) {
                     log_message('No empty slot found for new upload after checking all slots for file ' . $file_name);
                     $response_messages[] = "لا توجد خانات فارغة لرفع صور جديدة. لم يتم رفع $file_name.";
                     unlink($upload_path); // Clean up the newly uploaded file
                     continue; // Skip this file, but continue with others
                }

                // Mark the slot as used in our in-memory array for subsequent files in this batch
                $current_images_db["work_image_$slot_to_update"] = $db_path; 
            }

            // Update database with the new image path
            if ($slot_to_update > 0) {
                try {
                    log_message('Attempting database update for slot ' . $slot_to_update . ': ' . $db_path);
                    $stmt = $conn->prepare("UPDATE profile SET work_image_$slot_to_update = ? WHERE user_id = ?");
                    if ($stmt->execute([$db_path, $user_id])) {
                        log_message('Database updated successfully for slot ' . $slot_to_update);
                        $overall_success = true;
                        $response_messages[] = 'تم رفع الصورة ' . $file_name . ' بنجاح!';
                    } else {
                        $db_error = $stmt->errorInfo();
                        $response_messages[] = "فشل تحديث قاعدة البيانات للصورة $file_name: " . ($db_error[2] ?? 'Unknown error');
                        log_message('Database update failed for slot ' . $slot_to_update . ', Error: ' . ($db_error[2] ?? 'Unknown error'));
                    }
                } catch (PDOException $e) {
                    $response_messages[] = "خطأ PDO عند تحديث قاعدة البيانات للصورة $file_name: " . $e->getMessage();
                    log_message('PDO Error during database update for slot ' . $slot_to_update . ', Error: ' . $e->getMessage());
                }
            } else {
                 $response_messages[] = "فشل تحديد خانة الصورة للرفع: $file_name.";
                 log_message('Failed to determine slot for file: ' . $file_name);
                 unlink($upload_path); // Clean up the newly uploaded file if slot not determined
                 continue; // Skip this file, but continue with others
            }

        } else {
            $response_messages[] = "فشل في نقل الصورة إلى مجلد الرفع: $file_name";
            log_message('Failed to move uploaded file: ' . $file_name . '. Error code: ' . ($uploaded_files_array['error'][$i] ?? 'Unknown'));
             $php_error = error_get_last();
             if ($php_error) {
                 log_message('move_uploaded_file PHP error: ' . $php_error['message']);
             }
             continue; // Skip this file, but continue with others
        }
    } else {
        $response_messages[] = "خطأ في رفع الملف $file_name: " . $uploaded_files_array['error'][$i];
        log_message('File upload error for ' . $file_name . ': ' . $uploaded_files_array['error'][$i]);
        continue; // Skip this file, but continue with others
    }
}

log_message('Upload process finished. Overall success: ' . ($overall_success ? 'Yes' : 'No') . ', Messages: ' . implode('; ', $response_messages));

// Send final response based on overall success flag
if ($overall_success) {
    echo json_encode([
        'success' => true,
        'message' => implode('. ', $response_messages) // Combine messages
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => implode('. ', $response_messages) // Combine errors
    ]);
}

log_message('Response sent.');
?>