<?php
session_start();
include '../../database/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['myid']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Function to generate unique ID
function generateUniqueId($prefix, $conn, $table, $idColumn) {
    do {
        $id = $prefix . rand(1000, 9999);
        $sql = "SELECT COUNT(*) as count FROM $table WHERE $idColumn = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
    } while ($count > 0);
    
    return $id;
}

// Function to hash password
function hashPassword($password) {
    return md5($password);
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate gender
function isValidGender($gender) {
    $validGenders = ['Male', 'Female', 'male', 'female', 'M', 'F', 'm', 'f'];
    return in_array($gender, $validGenders);
}

// Function to process CSV file
function processCSV($file, $uploadType, $conn) {
    $results = [
        'total' => 0,
        'successful' => 0,
        'failed' => 0,
        'errors' => []
    ];
    
    if (($handle = fopen($file['tmp_name'], "r")) !== FALSE) {
        $row = 1;
        $headers = fgetcsv($handle);
        
        // Validate headers based on upload type
        $expectedHeaders = getExpectedHeaders($uploadType);
        if (count(array_diff($expectedHeaders, $headers)) > 0) {
            $results['errors'][] = "Invalid CSV format. Expected headers: " . implode(', ', $expectedHeaders);
            return $results;
        }
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $row++;
            $results['total']++;
            
            try {
                $success = processRow($data, $uploadType, $conn, $row);
                if ($success) {
                    $results['successful']++;
                } else {
                    $results['failed']++;
                }
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row $row: " . $e->getMessage();
            }
        }
        fclose($handle);
    }
    
    return $results;
}

// Function to get expected headers for each upload type
function getExpectedHeaders($uploadType) {
    switch ($uploadType) {
        case 'teachers':
            return ['Teacher ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Password'];
        case 'accountants':
            return ['Accountant ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Password'];
        case 'classes':
            return ['Class ID', 'Class Name', 'Academic Year'];
        case 'subjects':
            return ['Subject ID', 'Subject Name', 'Class'];
        case 'students':
            return ['Student ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Contact', 'Class', 'Academic Year'];
        case "questions":
            return ['Question Type', 'Question', 'Marks', 'Correct Answer', 'Option A', 'Option B', 'Option C', 'Option D'];
        default:
            return [];
    }
}

// Function to process individual row
function processRow($data, $uploadType, $conn, $row) {
    switch ($uploadType) {
        case 'teachers':
            return processTeacherRow($data, $conn, $row);
        case 'accountants':
            return processAccountantRow($data, $conn, $row);
        case 'classes':
            return processClassRow($data, $conn, $row);
        case 'subjects':
            return processSubjectRow($data, $conn, $row);
        case 'students':
            return processStudentRow($data, $conn, $row);
        default:
            throw new Exception("Unknown upload type: $uploadType");
    }
}

// Process teacher row
function processTeacherRow($data, $conn, $row) {
    if (count($data) < 6) {
        throw new Exception("Insufficient data. Expected 6 columns, got " . count($data));
    }
    
    $teacherId = trim($data[0]);
    $firstName = trim($data[1]);
    $lastName = trim($data[2]);
    $gender = trim($data[3]);
    $email = trim($data[4]);
    $password = trim($data[5]);

    if(empty($teacherId)){
        $teacherId = generateUniqueId('TCHR', $conn, 'tbl_teacher', 'teacher_id');
    }
    
    // Validation
    if (empty($teacherId) || empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        throw new Exception("All fields are required");
    }
    
    if (!isValidEmail($email)) {
        throw new Exception("Invalid email format: $email");
    }
    
    if (!isValidGender($gender)) {
        throw new Exception("Invalid gender: $gender");
    }
    
    // Check if teacher already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_teacher WHERE teacher_id = ? OR email = ?");
    $stmt->bind_param("ss", $teacherId, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception("Teacher with ID '$teacherId' or email '$email' already exists");
    }
    
    // Insert teacher
    $hashedPassword = hashPassword($password);
    $stmt = $conn->prepare("INSERT INTO tbl_teacher (teacher_id, first_name, last_name, gender, email, login, role) VALUES (?, ?, ?, ?, ?, ?, 'teacher')");
    $stmt->bind_param("ssssss", $teacherId, $firstName, $lastName, $gender, $email, $hashedPassword);
    
    $response = $stmt->execute();

    if($response){
        $mail = setup_teacher_email($teacherId, $email);
        $mail->send();
    }

    return $response;
}

// Process accountant row
function processAccountantRow($data, $conn, $row) {
    if (count($data) < 6) {
        throw new Exception("Insufficient data. Expected 6 columns, got " . count($data));
    }
    
    $accountantId = trim($data[0]);
    $firstName = trim($data[1]);
    $lastName = trim($data[2]);
    $gender = trim($data[3]);
    $email = trim($data[4]);
    $password = trim($data[5]);

    if(empty($accountantId)){
        $accountantId = generateUniqueId('ACC', $conn, 'tbl_teacher', 'teacher_id');
    }
    
    // Validation
    if (empty($accountantId) || empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        throw new Exception("All fields are required");
    }
    
    if (!isValidEmail($email)) {
        throw new Exception("Invalid email format: $email");
    }
    
    if (!isValidGender($gender)) {
        throw new Exception("Invalid gender: $gender");
    }
    
    // Check if accountant already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_teacher WHERE teacher_id = ? OR email = ?");
    $stmt->bind_param("ss", $accountantId, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception("Accountant with ID '$accountantId' or email '$email' already exists");
    }
    
    // Insert accountant
    $hashedPassword = hashPassword($password);
    $stmt = $conn->prepare("INSERT INTO tbl_teacher (teacher_id, first_name, last_name, gender, email, login, role) VALUES (?, ?, ?, ?, ?, ?, 'accountant')");
    $stmt->bind_param("ssssss", $accountantId, $firstName, $lastName, $gender, $email, $hashedPassword);
    
    $response = $stmt->execute();
    
    if($response){
        $mail = setup_accountant_email($accountantId, $email);
        $mail->send();
    }
    return $response;
}

// Process class row
function processClassRow($data, $conn, $row) {
    if (count($data) < 3) {
        throw new Exception("Insufficient data. Expected 3 columns, got " . count($data));
    }
    
    $classId = trim($data[0]);
    $className = trim($data[1]);
    $academicYear = trim($data[2]);
    
    // Validation
    if (empty($classId) || empty($className) || empty($academicYear)) {
        throw new Exception("All fields are required");
    }
    
    // Check if class already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_classes WHERE class_id = ?");
    $stmt->bind_param("s", $classId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception("Class with ID '$classId' already exists");
    }
    
    // Insert class
    $stmt = $conn->prepare("INSERT INTO tbl_classes (class_id, name, ay) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $classId, $className, $academicYear);
    
    return $stmt->execute();
}

// Process subject row
function processSubjectRow($data, $conn, $row) {
    if (count($data) < 3) {
        throw new Exception("Insufficient data. Expected 3 columns, got " . count($data));
    }
    
    $subjectId = trim($data[0]);
    $subjectName = trim($data[1]);
    $class = trim($data[2]);
    
    // Validation
    if (empty($subjectId) || empty($subjectName) || empty($class)) {
        throw new Exception("All fields are required");
    }
    
    // Check if subject already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_subjects WHERE subject_id = ?");
    $stmt->bind_param("s", $subjectId);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception("Subject with ID '$subjectId' already exists");
    }
    
    // Insert subject
    $stmt = $conn->prepare("INSERT INTO tbl_subjects (subject_id, name, class) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $subjectId, $subjectName, $class);
    
    return $stmt->execute();
}

// Process student row
function processStudentRow($data, $conn, $row) {
    if (count($data) < 8) {
        throw new Exception("Insufficient data. Expected 8 columns, got " . count($data));
    }
    
    $studentId = trim($data[0]);
    $firstName = trim($data[1]);
    $lastName = trim($data[2]);
    $gender = trim($data[3]);
    $email = trim($data[4]);
    $contact = trim($data[5]);
    $class = trim($data[6]);
    $academicYear = trim($data[7]);

    if(empty($studentId)){
        $studentId = generateUniqueId('OES', $conn, 'tbl_users', 'user_id');
    }
    
    // Validation
    if (empty($studentId) || empty($firstName) || empty($lastName) || empty($email) || empty($class) || empty($academicYear)) {
        throw new Exception("Required fields cannot be empty");
    }
    
    if (!isValidEmail($email)) {
        throw new Exception("Invalid email format: $email");
    }
    
    if (!isValidGender($gender)) {
        throw new Exception("Invalid gender: $gender");
    }
    
    // Check if student already exists
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_users WHERE user_id = ? OR email = ?");
    $stmt->bind_param("ss", $studentId, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    if ($count > 0) {
        throw new Exception("Student with ID '$studentId' or email '$email' already exists");
    }
    
    // Generate default password (student ID)
    $defaultPassword = hashPassword($studentId);
    
    // Insert student
    $stmt = $conn->prepare("INSERT INTO tbl_users (user_id, first_name, last_name, gender, email, contact, ay, class, login, role, acc_stat, fees) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'student', 'Active', 'Paid')");
    $stmt->bind_param("sssssssss", $studentId, $firstName, $lastName, $gender, $email, $contact, $academicYear, $class, $defaultPassword);
    
    $response = $stmt->execute();

    if($response){
        $mail = setup_student_email($studentId, $email);
        $mail->send();
    }

    return $response;
}

// Main processing logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadType = $_POST['upload_type'] ?? ($_POST['uploadType'] ?? '');
    $csvFile = $_FILES['csv_file'] ?? null;
    
    if (!$csvFile || $csvFile['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['upload_error'] = "Please select a valid CSV file.";
        header('Location: ../bulk_upload_system.php');
    exit();
}

// Validate file type
$fileType = pathinfo($csvFile['name'], PATHINFO_EXTENSION);
if (strtolower($fileType) !== 'csv') {
    $_SESSION['upload_error'] = "Only CSV files are allowed.";
    header('Location: ../bulk_upload_system.php');
    exit();
}

// Validate file size (5MB max)
if ($csvFile['size'] > 5 * 1024 * 1024) {
    $_SESSION['upload_error'] = "File size must be less than 5MB.";
    header('Location: ../bulk_upload_system.php');
    exit();
}

    if($uploadType == "questions"){
        require_once $root_path . '/includes/bulk_question_upload.php';
        $validation_result = validateCSVFormat($csvFile["tmp_name"]);
        
        if($validation_result["valid"]){
            $exam_id = $_POST['exam_id'];
            // Process the bulk upload
            $result = processBulkQuestionUpload($csvFile["tmp_name"], $exam_id, $_SESSION["myid"], $_SESSION["role"], $conn);

            // Redirect to view questions if it's a question upload
            if ($uploadType === 'questions' && $result['successful_uploads'] > 0) {
                $conn->close();
                header('Location: ../view-questions.php?eid=' . urlencode($exam_id));
                exit();
            }
        }else{
            $error_message = "CSV format validation failed:\n" . implode("\n", $validation_result['errors']);
            echo "<script>
                alert('$error_message');
                window.location.href='".$_SERVER["HTTP_REFERER"]."';
                </script>";
        }
    }else{
        try {
            $results = processCSV($csvFile, $uploadType, $conn);
            
            if ($results['successful'] > 0) {
                $_SESSION['upload_success'] = "Successfully uploaded {$results['successful']} out of {$results['total']} records.";
                if (count($results['errors']) > 0) {
                    $_SESSION['upload_warning'] = "Some records failed to upload. Check the error log for details.";
                }
            } else {
                $_SESSION['upload_error'] = "No records were uploaded successfully.";
            }
            
            // Store errors in session for display
            if (count($results['errors']) > 0) {
                $_SESSION['upload_errors'] = $results['errors'];
            }
            
        } catch (Exception $e) {
            $_SESSION['upload_error'] = "Error processing file: " . $e->getMessage();
        }
    }
    
    $conn->close();
    header('Location: '.$_SERVER["HTTP_REFERER"]);
    exit();
} else {
    header('Location: '.$_SERVER["HTTP_REFERER"]);
    exit();
}
?> 