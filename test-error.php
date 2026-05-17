<?php
require_once 'php/error-handler.php';

// Test different log levels
logMessage('INFO', 'Testing info logging');
logMessage('WARNING', 'This is a test warning message');
logMessage('ERROR', 'This is a test error message');

// Test database error simulation
try {
    throw new Exception('Test exception for error handling');
} catch (Exception $e) {
    logMessage('EXCEPTION', $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
}

echo "Error logging test completed. Check logs/error.log file.";
?>
