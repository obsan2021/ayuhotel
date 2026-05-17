<?php
require_once 'error-handler.php';

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ayuhotel_db');

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
define('ADMIN_EMAIL', 'manager@ayuinternationalhotel.com');

// Connect to Database with error logging
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        logMessage('ERROR', 'Database connection failed', [
            'error' => $conn->connect_error,
            'host' => DB_HOST,
            'database' => DB_NAME
        ]);
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    logMessage('INFO', 'Database connected successfully');
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    logMessage('FATAL', 'Database connection error', ['message' => $e->getMessage()]);
    die("System error. Please try again later.");
}

// Set timezone
date_default_timezone_set('Africa/Addis_Ababa');

// Log successful configuration load
logMessage('INFO', 'Configuration loaded successfully');
?>