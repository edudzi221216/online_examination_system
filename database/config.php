<?php
error_reporting(1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_db";

// Timezone configuration - change this to your local timezone
define('DEFAULT_TIMEZONE', 'UTC'); // Change to your timezone, e.g., 'America/New_York', 'Europe/London', 'Asia/Tokyo'
date_default_timezone_set(DEFAULT_TIMEZONE);


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
die("<h2>Database Connection Failure : " . $conn->connect_error . "</h2><hr>");
} 

// only include for server side scripts
if(!empty($_SERVER["DOCUMENT_ROOT"])){
    include_once "helpers.php";
}
?>