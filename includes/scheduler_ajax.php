<?php
/**
 * AJAX Handler for JavaScript Exam Scheduler
 * This file handles AJAX requests from the JavaScript scheduler
 */

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

// Include database configuration
require_once '../database/config.php';

// Include enhanced exam scheduler functions
require_once 'enhanced_exam_scheduler.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Get action from request
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'update_exam_status':
            handleUpdateExamStatus($conn);
            break;
            
        case 'get_exam_status':
            handleGetExamStatus($conn);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

/**
 * Handle exam status update request
 */
function handleUpdateExamStatus($conn) {
    $changes = [];
    $notifications = [];
    
    // Update exam statuses
    $exam_changes = updateExamStatus($conn);
    if ($exam_changes) {
        $changes = array_merge($changes, $exam_changes);
    }
    
    // Update result publication status
    $result_changes = updateResultPublicationStatus($conn);
    if ($result_changes) {
        $changes = array_merge($changes, $result_changes);
    }
    
    // Generate notifications for important changes
    foreach ($changes as $change) {
        if ($change['type'] === 'exam_status' && $change['new_status'] === 'Active') {
            $notifications[] = [
                'type' => 'success',
                'title' => 'Exam Started',
                'message' => "Exam '{$change['exam_name']}' is now available for students."
            ];
        } elseif ($change['type'] === 'exam_status' && $change['new_status'] === 'Inactive') {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Exam Ended',
                'message' => "Exam '{$change['exam_name']}' has ended and is no longer available."
            ];
        } elseif ($change['type'] === 'result_status' && $change['new_status'] === 'Published') {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Results Published',
                'message' => "Results for '{$change['exam_name']}' are now available to students."
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Exam status updated successfully',
        'changes' => $changes,
        'notifications' => $notifications,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Handle get exam status request
 */
function handleGetExamStatus($conn) {
    $exam_id = $_POST['exam_id'] ?? '';
    
    if (empty($exam_id)) {
        echo json_encode(['success' => false, 'message' => 'Exam ID is required']);
        return;
    }
    
    $exam_id = mysqli_real_escape_string($conn, $exam_id);
    
    // Get exam details
    $sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'Exam not found']);
        return;
    }
    
    $exam = $result->fetch_assoc();
    
    // Check exam availability
    $availability = checkExamAvailability($exam_id, $conn);
    
    // Check result publication status
    $result_status = checkResultPublication($exam_id, $conn);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'exam_id' => $exam['exam_id'],
            'exam_name' => $exam['exam_name'],
            'class' => $exam['class'],
            'subject' => $exam['subject'],
            'status' => $exam['status'],
            'availability' => $availability,
            'result_publication' => $result_status,
            'start_date' => $exam['date'],
            'end_date' => $exam['end_exam_date'],
            'start_time' => $exam['start_time'],
            'end_time' => $exam['end_time'],
            'result_publish_status' => $exam['result_publish_status']
        ]
    ]);
}

/**
 * Update exam status and return changes
 */
function updateExamStatus($conn) {
    $changes = [];
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Get exams that need status updates
    $sql = "SELECT * FROM tbl_examinations WHERE status IN ('Active', 'Inactive')";
    $result = $conn->query($sql);
    
    while ($exam = $result->fetch_assoc()) {
        $exam_date = $exam['date'];
        $end_exam_date = $exam['end_exam_date'];
        $start_time = $exam['start_time'];
        $end_time = $exam['end_time'];
        
        // Convert date format if needed
        if (strpos($exam_date, '/') !== false) {
            $date_parts = explode('/', $exam_date);
            if (count($date_parts) == 3) {
                $exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        if ($end_exam_date && strpos($end_exam_date, '/') !== false) {
            $date_parts = explode('/', $end_exam_date);
            if (count($date_parts) == 3) {
                $end_exam_date = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
            }
        }
        
        $should_be_active = false;
        
        // Check if exam should be active
        if ($current_date >= $exam_date && (!$end_exam_date || $current_date <= $end_exam_date)) {
            if ($current_date == $exam_date) {
                // On start date, check time
                if ($current_time >= $start_time && $current_time <= $end_time) {
                    $should_be_active = true;
                }
            } else {
                // Between start and end dates, always active
                $should_be_active = true;
            }
        }
        
        // Update status if needed
        $current_status = $exam['status'];
        $new_status = $should_be_active ? 'Active' : 'Inactive';
        
        if ($current_status !== $new_status) {
            $update_sql = "UPDATE tbl_examinations SET status = '$new_status' WHERE exam_id = '{$exam['exam_id']}'";
            if ($conn->query($update_sql)) {
                $changes[] = [
                    'type' => 'exam_status',
                    'exam_id' => $exam['exam_id'],
                    'exam_name' => $exam['exam_name'],
                    'old_status' => $current_status,
                    'new_status' => $new_status,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }
    }
    
    return $changes;
}

/**
 * Update result publication status and return changes
 */
function updateResultPublicationStatus($conn) {
    $changes = [];
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Get exams with scheduled result publication
    $sql = "SELECT * FROM tbl_examinations WHERE result_publish_status = 'Scheduled'";
    $result = $conn->query($sql);
    
    while ($exam = $result->fetch_assoc()) {
        $start_date = $exam['result_publish_start_date'];
        $start_time = $exam['result_publish_start_time'];
        $end_date = $exam['result_publish_end_date'];
        $end_time = $exam['result_publish_end_time'];
        
        if (!$start_date || !$end_date) {
            continue;
        }
        
        $should_be_published = false;
        
        // Check if results should be published
        if ($current_date >= $start_date && $current_date <= $end_date) {
            if ($current_date == $start_date && $start_time) {
                // On start date, check start time
                if ($current_time >= $start_time) {
                    $should_be_published = true;
                }
            } elseif ($current_date == $end_date && $end_time) {
                // On end date, check end time
                if ($current_time <= $end_time) {
                    $should_be_published = true;
                }
            } else {
                // Between start and end dates, always published
                $should_be_published = true;
            }
        }
        
        // Update status if needed
        $current_status = $exam['result_publish_status'];
        $new_status = $should_be_published ? 'Published' : 'Not Published';
        
        if ($current_status !== $new_status) {
            $update_sql = "UPDATE tbl_examinations SET result_publish_status = '$new_status' WHERE exam_id = '{$exam['exam_id']}'";
            if ($conn->query($update_sql)) {
                $changes[] = [
                    'type' => 'result_status',
                    'exam_id' => $exam['exam_id'],
                    'exam_name' => $exam['exam_name'],
                    'old_status' => $current_status,
                    'new_status' => $new_status,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
        }
    }
    
    return $changes;
}
?> 