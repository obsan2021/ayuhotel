<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$logFile = '../logs/error.log';

if (file_exists($logFile)) {
    // Backup existing logs before clearing
    $backupFile = '../logs/error-' . date('Y-m-d-H-i-s') . '.log';
    copy($logFile, $backupFile);
    
    // Clear the log file
    file_put_contents($logFile, date('[Y-m-d H:i:s]') . " INFO: Log file cleared by admin\n");
    
    echo json_encode(['success' => true, 'message' => 'Logs cleared successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Log file not found']);
}
?>
