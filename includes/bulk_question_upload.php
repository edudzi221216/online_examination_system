<?php
/**
 * Bulk Question Upload System
 * Supports CSV uploads for multiple choice and fill-in-the-blank questions
 */

// Set timezone
// Set timezone from config or use default
$timezone = defined('DEFAULT_TIMEZONE') ? DEFAULT_TIMEZONE : 'UTC';
date_default_timezone_set($timezone);

/**
 * Process bulk question upload from CSV file
 * @param string $file_path Path to uploaded CSV file
 * @param string $exam_id Exam ID to associate questions with
 * @param string $uploaded_by User ID who uploaded the file
 * @param string $user_type Type of user (admin/teacher)
 * @param object $conn Database connection object
 * @return array Array with upload results
 */
function processBulkQuestionUpload($file_path, $exam_id, $uploaded_by, $user_type, $conn) {
    $upload_id = 'UP' . get_rand_numbers(8);
    $total_questions = 0;
    $successful_uploads = 0;
    $failed_uploads = 0;
    $error_log = array();
    
    // Validate exam exists
    $exam_sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $exam_result = $conn->query($exam_sql);
    if ($exam_result->num_rows == 0) {
        return array(
            'success' => false,
            'message' => 'Exam not found',
            'upload_id' => null,
            'total_questions' => 0,
            'successful_uploads' => 0,
            'failed_uploads' => 0,
            'error_log' => array('Exam not found')
        );
    }
    
    // Create upload record
    $insert_sql = "INSERT INTO tbl_bulk_uploads (upload_id, uploaded_by, user_type, exam_id, file_name, total_questions, successful_uploads, failed_uploads, status) 
                   VALUES ('$upload_id', '$uploaded_by', '$user_type', '$exam_id', '" . basename($file_path) . "', 0, 0, 0, 'Processing')";
    $conn->query($insert_sql);
    
    // Open CSV file
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $row_number = 0;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_number++;
            
            // Skip header row
            if ($row_number == 1) {
                continue;
            }
            
            $total_questions++;
            
            // Validate required columns
            if (count($data) < 4) {
                $failed_uploads++;
                $error_log[] = "Row $row_number: Insufficient columns. Expected at least 4 columns.";
                continue;
            }
            
            // Extract data from CSV
            $question_type = trim(strtoupper($data[0])); // MC, FB, TF
            $question = trim($data[1]);
            $marks = trim($data[2]);
            $correct_answer = trim($data[3]);
            
            // Validate question type
            if (!in_array($question_type, array('MC', 'FB', 'TF'))) {
                $failed_uploads++;
                $error_log[] = "Row $row_number: Invalid question type '$question_type'. Must be MC, FB, or TF.";
                continue;
            }
            
            // Validate required fields
            if (empty($question) || empty($marks) || empty($correct_answer)) {
                $failed_uploads++;
                $error_log[] = "Row $row_number: Missing required fields (question, marks, or correct answer).";
                continue;
            }
            
            // Validate marks
            if (!is_numeric($marks) || $marks <= 0) {
                $failed_uploads++;
                $error_log[] = "Row $row_number: Invalid marks value '$marks'. Must be a positive number.";
                continue;
            }
            
            // Process based on question type
            $result = processQuestionByType($question_type, $question, $marks, $correct_answer, $data, $exam_id, $conn);
            
            if ($result['success']) {
                $successful_uploads++;
            } else {
                $failed_uploads++;
                $error_log[] = "Row $row_number: " . $result['message'];
            }
        }
        
        fclose($handle);
    } else {
        $error_log[] = "Could not open CSV file.";
    }
    
    // Update upload record
    $status = ($failed_uploads == 0) ? 'Completed' : (($successful_uploads > 0) ? 'Completed' : 'Failed');
    $error_log_text = implode("\n", $error_log);
    
    $update_sql = "UPDATE tbl_bulk_uploads SET 
                   total_questions = $total_questions,
                   successful_uploads = $successful_uploads,
                   failed_uploads = $failed_uploads,
                   status = '$status',
                   error_log = " . ($error_log_text ? "'" . mysqli_real_escape_string($conn, $error_log_text) . "'" : "NULL") . "
                   WHERE upload_id = '$upload_id'";
    $conn->query($update_sql);
    
    return array(
        'success' => $successful_uploads > 0,
        'message' => "Upload completed. $successful_uploads successful, $failed_uploads failed.",
        'upload_id' => $upload_id,
        'total_questions' => $total_questions,
        'successful_uploads' => $successful_uploads,
        'failed_uploads' => $failed_uploads,
        'error_log' => $error_log
    );
}

/**
 * Process question based on its type
 * @param string $question_type Question type (MC, FB, TF)
 * @param string $question Question text
 * @param int $marks Marks for the question
 * @param string $correct_answer Correct answer
 * @param array $data Full CSV row data
 * @param string $exam_id Exam ID
 * @param object $conn Database connection object
 * @return array Result array
 */
function processQuestionByType($question_type, $question, $marks, $correct_answer, $data, $exam_id, $conn) {
    $question_id = 'QS-' . get_rand_numbers(6);
    $exam_id = mysqli_real_escape_string($conn, $exam_id);
    $question = mysqli_real_escape_string($conn, $question);
    $marks = (int)$marks;
    
    // Check for duplicate question
    $check_sql = "SELECT * FROM tbl_questions WHERE exam_id = '$exam_id' AND question = '$question'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        return array('success' => false, 'message' => 'Question already exists for this exam.');
    }
    
    switch ($question_type) {
        case 'MC':
            return processMultipleChoiceQuestion($question_id, $exam_id, $question, $marks, $correct_answer, $data, $conn);
            
        case 'FB':
            return processFillInTheBlankQuestion($question_id, $exam_id, $question, $marks, $correct_answer, $conn);
            
        case 'TF':
            return processTrueFalseQuestion($question_id, $exam_id, $question, $marks, $correct_answer, $conn);
            
        default:
            return array('success' => false, 'message' => 'Unsupported question type.');
    }
}

/**
 * Process multiple choice question
 * @param string $question_id Question ID
 * @param string $exam_id Exam ID
 * @param string $question Question text
 * @param int $marks Marks
 * @param string $correct_answer Correct answer
 * @param array $data CSV data
 * @param object $conn Database connection object
 * @return array Result array
 */
function processMultipleChoiceQuestion($question_id, $exam_id, $question, $marks, $correct_answer, $data, $conn) {
    // Extract options (columns 4-7 for options A, B, C, D)
    $options = array();
    for ($i = 4; $i <= 7; $i++) {
        $options[] = isset($data[$i]) ? mysqli_real_escape_string($conn, trim($data[$i])) : '';
    }
    
    // Validate options
    if (count(array_filter($options)) < 2) {
        return array('success' => false, 'message' => 'Multiple choice questions must have at least 2 options.');
    }
    
    // Validate correct answer
    $valid_answers = array('A', 'B', 'C', 'D', 'option1', 'option2', 'option3', 'option4');
    if (!in_array(strtoupper($correct_answer), $valid_answers)) {
        return array('success' => false, 'message' => 'Invalid correct answer format. Use A, B, C, D or option1, option2, option3, option4.');
    }
    
    // Convert answer format if needed
    $answer_mapping = array(
        'A' => 'option1', 'B' => 'option2', 'C' => 'option3', 'D' => 'option4',
        'option1' => 'option1', 'option2' => 'option2', 'option3' => 'option3', 'option4' => 'option4'
    );
    $correct_answer = $answer_mapping[strtoupper($correct_answer)];
    
    $sql = "INSERT INTO tbl_questions (question_id, exam_id, type, question_type, question, Qmarks, option1, option2, option3, option4, answer) 
            VALUES ('$question_id', '$exam_id', 'MC', 'MC', '$question', $marks, '{$options[0]}', '{$options[1]}', '{$options[2]}', '{$options[3]}', '$correct_answer')";
    
    if ($conn->query($sql) === TRUE) {
        return array('success' => true, 'message' => 'Multiple choice question added successfully.');
    } else {
        return array('success' => false, 'message' => 'Database error: ' . $conn->error);
    }
}

/**
 * Process fill-in-the-blank question
 * @param string $question_id Question ID
 * @param string $exam_id Exam ID
 * @param string $question Question text
 * @param int $marks Marks
 * @param string $correct_answer Correct answer
 * @param object $conn Database connection object
 * @return array Result array
 */
function processFillInTheBlankQuestion($question_id, $exam_id, $question, $marks, $correct_answer, $conn) {
    $correct_answer = mysqli_real_escape_string($conn, $correct_answer);
    
    $sql = "INSERT INTO tbl_questions (question_id, exam_id, type, question_type, question, Qmarks, answer) 
            VALUES ('$question_id', '$exam_id', 'FB', 'FB', '$question', $marks, '$correct_answer')";
    
    if ($conn->query($sql) === TRUE) {
        return array('success' => true, 'message' => 'Fill-in-the-blank question added successfully.');
    } else {
        return array('success' => false, 'message' => 'Database error: ' . $conn->error);
    }
}

/**
 * Process true/false question
 * @param string $question_id Question ID
 * @param string $exam_id Exam ID
 * @param string $question Question text
 * @param int $marks Marks
 * @param string $correct_answer Correct answer
 * @param object $conn Database connection object
 * @return array Result array
 */
function processTrueFalseQuestion($question_id, $exam_id, $question, $marks, $correct_answer, $conn) {
    // Validate correct answer
    $correct_answer = strtoupper(trim($correct_answer));
    if (!in_array($correct_answer, array('TRUE', 'FALSE', 'T', 'F'))) {
        return array('success' => false, 'message' => 'Invalid true/false answer. Use TRUE, FALSE, T, or F.');
    }
    
    // Convert to standard format
    $answer_mapping = array(
        'TRUE' => 'True', 'FALSE' => 'False', 'T' => 'True', 'F' => 'False'
    );
    $correct_answer = $answer_mapping[$correct_answer];
    
    $sql = "INSERT INTO tbl_questions (question_id, exam_id, type, question_type, question, Qmarks, option1, option2, answer) 
            VALUES ('$question_id', '$exam_id', 'TF', 'TF', '$question', $marks, 'True', 'False', '$correct_answer')";
    
    if ($conn->query($sql) === TRUE) {
        return array('success' => true, 'message' => 'True/False question added successfully.');
    } else {
        return array('success' => false, 'message' => 'Database error: ' . $conn->error);
    }
}

/**
 * Generate CSV template for bulk upload
 * @param string $exam_id Exam ID
 * @param object $conn Database connection object
 * @return string CSV content
 */
function generateCSVTemplate($exam_id, $conn) {
    $exam_id = mysqli_real_escape_string($conn, $exam_id);
    
    // Get exam details
    $sql = "SELECT * FROM tbl_examinations WHERE exam_id = '$exam_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        return false;
    }
    
    $exam = $result->fetch_assoc();
    
    $csv_content = "Question Type,Question,Marks,Correct Answer,Option A,Option B,Option C,Option D\n";
    $csv_content .= "MC,What is the capital of France?,20,A,Paris,London,Berlin,Madrid\n";
    $csv_content .= "MC,Which programming language is this system built with?,15,B,PHP,Java,Python,C++\n";
    $csv_content .= "FB,Complete the sentence: The sun _____ in the east.,10,rises\n";
    $csv_content .= "TF,The Earth is round.,10,TRUE\n";
    $csv_content .= "TF,Water boils at 100 degrees Celsius at sea level.,10,TRUE\n";
    
    return $csv_content;
}

/**
 * Get upload history for a user
 * @param string $user_id User ID
 * @param string $user_type User type
 * @param object $conn Database connection object
 * @return array Upload history
 */
function getUploadHistory($user_id, $user_type, $conn) {
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $user_type = mysqli_real_escape_string($conn, $user_type);
    
    $sql = "SELECT bu.*, e.exam_name, e.subject, e.class 
            FROM tbl_bulk_uploads bu 
            JOIN tbl_examinations e ON bu.exam_id = e.exam_id 
            WHERE bu.uploaded_by = '$user_id' AND bu.user_type = '$user_type' 
            ORDER BY bu.upload_date DESC";
    
    $result = $conn->query($sql);
    $uploads = array();
    
    while ($row = $result->fetch_assoc()) {
        $uploads[] = $row;
    }
    
    return $uploads;
}

/**
 * Validate CSV file format
 * @param string $file_path Path to CSV file
 * @return array Validation result
 */
function validateCSVFormat($file_path) {
    $errors = array();
    
    if (!file_exists($file_path)) {
        return array('valid' => false, 'errors' => array('File does not exist.'));
    }
    
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $row_number = 0;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_number++;
            
            // Check header row
            if ($row_number == 1) {
                $expected_headers = array('Question Type', 'Question', 'Marks', 'Correct Answer');
                if (count($data) < 4) {
                    $errors[] = "Header row: Insufficient columns. Expected at least 4 columns.";
                }
                continue;
            }
            
            // Validate data rows
            if (count($data) < 4) {
                $errors[] = "Row $row_number: Insufficient columns. Expected at least 4 columns.";
                continue;
            }
            
            $question_type = trim(strtoupper($data[0]));
            $question = trim($data[1]);
            $marks = trim($data[2]);
            $correct_answer = trim($data[3]);
            
            // Validate question type
            if (!in_array($question_type, array('MC', 'FB', 'TF'))) {
                $errors[] = "Row $row_number: Invalid question type '$question_type'. Must be MC, FB, or TF.";
            }
            
            // Validate required fields
            if (empty($question)) {
                $errors[] = "Row $row_number: Question text is required.";
            }
            
            if (empty($marks) || !is_numeric($marks) || $marks <= 0) {
                $errors[] = "Row $row_number: Marks must be a positive number.";
            }
            
            if (empty($correct_answer)) {
                $errors[] = "Row $row_number: Correct answer is required.";
            }
            
            // Validate multiple choice questions
            if ($question_type == 'MC') {
                if (count($data) < 8) {
                    $errors[] = "Row $row_number: Multiple choice questions must have at least 4 options (columns 5-8).";
                }
                
                $valid_answers = array('A', 'B', 'C', 'D', 'option1', 'option2', 'option3', 'option4');
                if (!in_array(strtoupper($correct_answer), $valid_answers)) {
                    $errors[] = "Row $row_number: Invalid correct answer format for multiple choice. Use A, B, C, D or option1, option2, option3, option4.";
                }
            }
            
            // Validate true/false questions
            if ($question_type == 'TF') {
                $valid_answers = array('TRUE', 'FALSE', 'T', 'F');
                if (!in_array(strtoupper($correct_answer), $valid_answers)) {
                    $errors[] = "Row $row_number: Invalid true/false answer. Use TRUE, FALSE, T, or F.";
                }
            }
        }
        
        fclose($handle);
    } else {
        $errors[] = "Could not open CSV file.";
    }
    
    return array('valid' => empty($errors), 'errors' => $errors);
}

/**
 * Generate random numbers for ID creation
 * @param int $length Length of random number
 * @return string Random number string
 */
function get_rand_numbers($length) {
    $numbers = '';
    for ($i = 0; $i < $length; $i++) {
        $numbers .= rand(0, 9);
    }
    return $numbers;
}
?> 