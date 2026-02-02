<?php
session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'newpassword123'); // Replace with your actual password
define('DB_NAME', 'real_estate_db');

// Site Configuration
define('SITE_NAME', 'Real Estate Hub');
define('SITE_URL', 'http://localhost:8765');
define('SITE_EMAIL', 'info@realestateHub.com');
define('SITE_PHONE', '+254 (0) 712 345 678');

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
