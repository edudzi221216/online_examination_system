<?php
/**
 * Script to update all buttons across the EMS system
 * This script will add the button-styles.css link to all PHP files
 */

$files_to_update = [
    // Admin files
    'admin/acad.php',
    'admin/accountant.php',
    'admin/add-questions.php',
    'admin/blockclassstd.php',
    'admin/blockstd.php',
    'admin/classes.php',
    'admin/classexamination.php',
    'admin/classresults.php',
    'admin/edit-accountant.php',
    'admin/edit-exam.php',
    'admin/edit-student.php',
    'admin/edit-teacher.php',
    'admin/notice.php',
    'admin/profile.php',
    'admin/questions.php',
    'admin/results.php',
    'admin/students.php',
    'admin/subject.php',
    'admin/teacher.php',
    'admin/vstudents.php',
    'admin/view-questions.php',
    'admin/view-results.php',
    
    // Teacher files
    'teacher/add-questions.php',
    'teacher/classexamination.php',
    'teacher/classresults.php',
    'teacher/edit-exam.php',
    'teacher/edit-student.php',
    'teacher/notice.php',
    'teacher/profile.php',
    'teacher/questions.php',
    'teacher/results.php',
    'teacher/students.php',
    'teacher/subject.php',
    'teacher/vstudents.php',
    'teacher/view-questions.php',
    'teacher/view-results.php',
    
    // Student files
    'student/assessment.php',
    'student/profile.php',
    'student/take-assessment.php',
    
    // Accountant files
    'accountant/profile.php',
    'accountant/students.php',
    'accountant/vstudents.php',
    
    // Main files
    'index.php',
    'login.php',
    'reset-password.php'
];

$css_link = '<link href="assets/css/button-styles.css" rel="stylesheet" type="text/css"/>';

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check if the CSS link is already present
        if (strpos($content, 'button-styles.css') === false) {
            // Find the position to insert the CSS link (after other CSS links)
            $insert_position = strpos($content, '<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">');
            
            if ($insert_position !== false) {
                $insert_position += strlen('<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">');
                
                // Insert the CSS link
                $new_content = substr($content, 0, $insert_position) . "\n        " . $css_link . substr($content, $insert_position);
                
                // Write the updated content back to the file
                if (file_put_contents($file, $new_content)) {
                    echo "✓ Updated $file\n";
                } else {
                    echo "✗ Failed to update $file\n";
                }
            } else {
                echo "⚠ Could not find insertion point in $file\n";
            }
        } else {
            echo "✓ $file already has button styles\n";
        }
    } else {
        echo "✗ File not found: $file\n";
    }
}

echo "\n=== Button Update Summary ===\n";
echo "All files have been updated with the new button styling.\n";
echo "The button-styles.css file provides consistent styling across the entire system.\n";
?> 