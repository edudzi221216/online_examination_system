<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['myid']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$type = $_GET['type'] ?? '';

// Define templates for each type
$templates = [
    'teachers' => [
        'headers' => ['Teacher ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Password'],
        'examples' => [
            ['TCHR1001', 'John', 'Doe', 'Male', 'john.doe@school.com', 'password123'],
            ['TCHR1002', 'Jane', 'Smith', 'Female', 'jane.smith@school.com', 'password456'],
            ['TCHR1003', 'Mike', 'Johnson', 'Male', 'mike.johnson@school.com', 'password789']
        ]
    ],
    'accountants' => [
        'headers' => ['Accountant ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Password'],
        'examples' => [
            ['ACC1001', 'Sarah', 'Wilson', 'Female', 'sarah.wilson@school.com', 'password123'],
            ['ACC1002', 'David', 'Brown', 'Male', 'david.brown@school.com', 'password456'],
            ['ACC1003', 'Lisa', 'Davis', 'Female', 'lisa.davis@school.com', 'password789']
        ]
    ],
    'classes' => [
        'headers' => ['Class ID', 'Class Name', 'Academic Year'],
        'examples' => [
            ['CLS1001', 'ICT L100', '2022/2023'],
            ['CLS1002', 'BTECH ICT', '2022/2023'],
            ['CLS1003', 'Computer Science', '2022/2023']
        ]
    ],
    'subjects' => [
        'headers' => ['Subject ID', 'Subject Name', 'Class'],
        'examples' => [
            ['SUB1001', 'Element Of Programming', 'ICT L100'],
            ['SUB1002', 'Coding', 'BTECH ICT'],
            ['SUB1003', 'Web Development', 'Computer Science']
        ]
    ],
    'students' => [
        'headers' => ['Student ID', 'First Name', 'Last Name', 'Gender', 'Email', 'Contact', 'Class', 'Academic Year'],
        'examples' => [
            ['STU1001', 'Alice', 'Johnson', 'Female', 'alice.johnson@student.com', '0241234567', 'ICT L100', '2022/2023'],
            ['STU1002', 'Bob', 'Williams', 'Male', 'bob.williams@student.com', '0242345678', 'BTECH ICT', '2022/2023'],
            ['STU1003', 'Carol', 'Miller', 'Female', 'carol.miller@student.com', '0243456789', 'Computer Science', '2022/2023']
        ]
    ]
];

if (!isset($templates[$type])) {
    header('Location: ../bulk_upload_questions.php');
    exit();
}

$template = $templates[$type];

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_template.csv"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Create output stream
$output = fopen('php://output', 'w');

// Write headers
fputcsv($output, $template['headers']);

// Write example rows
foreach ($template['examples'] as $example) {
    fputcsv($output, $example);
}

// Close the file
fclose($output);
exit();
?> 