<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$logFile = '../logs/error.log';

if (file_exists($logFile)) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="ayu-hotel-error-logs-' . date('Y-m-d') . '.log"');
    header('Content-Length: ' . filesize($logFile));
    readfile($logFile);
} else {
    echo "No log file found.";
}
?>
