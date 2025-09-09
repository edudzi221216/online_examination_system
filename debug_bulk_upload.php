<?php
/**
 * Debug page for bulk upload functionality
 * This page helps identify issues with the bulk upload system
 */

// Include database configuration
require_once 'database/config.php';
require_once 'includes/bulk_question_upload.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "User not logged in. Session data: ";
    print_r($_SESSION);
    exit();
}

echo "<h1>Bulk Upload Debug Page</h1>";

// Display session information
echo "<h2>Session Information</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test database connection
echo "<h2>Database Connection Test</h2>";
if ($conn) {
    echo "Database connection: SUCCESS<br>";
    
    // Test exam query
    $sql = "SELECT exam_id, exam_name, class, subject FROM tbl_examinations LIMIT 5";
    $result = $conn->query($sql);
    if ($result) {
        echo "Exam query: SUCCESS<br>";
        echo "Found " . $result->num_rows . " exams<br>";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['exam_name'] . " (" . $row['exam_id'] . ")<br>";
        }
    } else {
        echo "Exam query: FAILED - " . $conn->error . "<br>";
    }
    
    // Test bulk uploads table
    $sql = "SELECT * FROM tbl_bulk_uploads LIMIT 5";
    $result = $conn->query($sql);
    if ($result) {
        echo "Bulk uploads query: SUCCESS<br>";
        echo "Found " . $result->num_rows . " upload records<br>";
    } else {
        echo "Bulk uploads query: FAILED - " . $conn->error . "<br>";
    }
} else {
    echo "Database connection: FAILED<br>";
}

// Test file upload directory
echo "<h2>File Upload Directory Test</h2>";
$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    echo "Upload directory does not exist. Creating...<br>";
    if (mkdir($upload_dir, 0755, true)) {
        echo "Upload directory created successfully<br>";
    } else {
        echo "Failed to create upload directory<br>";
    }
} else {
    echo "Upload directory exists and is writable<br>";
}

// Test CSV template generation
echo "<h2>CSV Template Test</h2>";
if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id'];
    echo "Generating template for exam: $exam_id<br>";
    
    $template = generateCSVTemplate($exam_id, $conn);
    if ($template) {
        echo "Template generated successfully<br>";
        echo "<pre>" . htmlspecialchars($template) . "</pre>";
    } else {
        echo "Failed to generate template<br>";
    }
} else {
    echo "No exam_id provided. Add ?exam_id=EXAM_ID to URL to test template generation<br>";
}

// Test form submission
echo "<h2>Form Submission Test</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Form submitted!<br>";
    echo "POST data:<br>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "FILES data:<br>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
} else {
    echo "No form submission detected<br>";
}

// Create test form
echo "<h2>Test Upload Form</h2>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<label>Select Exam:</label><br>";
echo "<select name='exam_id' required>";
echo "<option value=''>-- Select Exam --</option>";

$sql = "SELECT exam_id, exam_name, class, subject FROM tbl_examinations";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['exam_id'] . "'>" . $row['exam_name'] . " (" . $row['class'] . " - " . $row['subject'] . ")</option>";
    }
}
echo "</select><br><br>";

echo "<label>Upload CSV File:</label><br>";
echo "<input type='file' name='csv_file' accept='.csv' required><br><br>";

echo "<button type='submit'>Test Upload</button>";
echo "</form>";

// Test bulk upload processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    echo "<h2>Processing Test Upload</h2>";
    
    $exam_id = $_POST['exam_id'];
    $uploaded_by = $_SESSION['myid'];
    $user_type = $_SESSION['role'];
    
    echo "Exam ID: $exam_id<br>";
    echo "Uploaded by: $uploaded_by<br>";
    echo "User type: $user_type<br>";
    
    // Move uploaded file
    $upload_dir = 'uploads/';
    $filename = 'debug_upload_' . date('Y-m-d_H-i-s') . '.csv';
    $file_path = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $file_path)) {
        echo "File uploaded successfully to: $file_path<br>";
        
        // Process the upload
        $result = processBulkQuestionUpload($file_path, $exam_id, $uploaded_by, $user_type, $conn);
        
        echo "Processing result:<br>";
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        
        // Clean up
        unlink($file_path);
    } else {
        echo "Failed to move uploaded file<br>";
    }
}

$conn->close();
?> 