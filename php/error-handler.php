<?php
// error-handler.php - Complete Error Management System for Ayu Hotel

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to visitors
ini_set('log_errors', 1); // Log errors instead
ini_set('error_log', dirname(__DIR__) . '/logs/error.log'); // Path to error log

// Custom error handler function
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    $logMessage = date('[Y-m-d H:i:s]') . " ERROR: [$errno] $errstr in $errfile on line $errline\n";
    error_log($logMessage, 3, dirname(__DIR__) . '/logs/error.log');
    
    // Don't show to visitors in production
    if (($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1')) {
        echo "<div style='background:#ffcccc; padding:10px; margin:10px; border:1px solid red;'>
                <strong>Error:</strong> $errstr<br>
                <strong>File:</strong> $errfile<br>
                <strong>Line:</strong> $errline
              </div>";
    }
    
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    $logMessage = date('[Y-m-d H:i:s]') . " EXCEPTION: " . $exception->getMessage() . 
                  " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($logMessage, 3, dirname(__DIR__) . '/logs/error.log');
    
    // Show user-friendly message
    if (!headers_sent()) {
        header('HTTP/1.1 500 Internal Server Error');
    }
    echo "<div style='text-align:center; padding:50px;'>
            <h2>Something went wrong</h2>
            <p>We've been notified and are working to fix the issue.</p>
            <a href='/'>Return to Homepage</a>
          </div>";
}

// Custom shutdown function to catch fatal errors
function shutdownHandler() {
    $error = error_get_last();
    if ($error !== null && ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR))) {
        $logMessage = date('[Y-m-d H:i:s]') . " FATAL: " . $error['message'] . 
                      " in " . $error['file'] . " on line " . $error['line'] . "\n";
        error_log($logMessage, 3, dirname(__DIR__) . '/logs/error.log');
    }
}

// Function to log custom messages
function logMessage($level, $message, $data = null) {
    $logEntry = date('[Y-m-d H:i:s]') . " [$level] $message";
    if ($data !== null) {
        $logEntry .= " - Data: " . json_encode($data);
    }
    $logEntry .= "\n";
    error_log($logEntry, 3, dirname(__DIR__) . '/logs/error.log');
}

// Function to log database queries
function logQuery($query, $params = null) {
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        $logMessage = date('[Y-m-d H:i:s]') . " QUERY: $query";
        if ($params) {
            $logMessage .= " | Params: " . json_encode($params);
        }
        error_log($logMessage . "\n", 3, dirname(__DIR__) . '/logs/error.log');
    }
}

// Function to log user actions
function logUserAction($userId, $action, $details = null) {
    $logMessage = date('[Y-m-d H:i:s]') . " USER_ACTION: UserID:$userId - $action";
    if ($details) {
        $logMessage .= " - $details";
    }
    error_log($logMessage . "\n", 3, dirname(__DIR__) . '/logs/error.log');
}

// Function to log API calls
function logAPI($endpoint, $request, $response, $status) {
    $logMessage = date('[Y-m-d H:i:s]') . " API: $endpoint | Status: $status | ";
    $logMessage .= "Request: " . json_encode($request) . " | ";
    $logMessage .= "Response: " . json_encode($response);
    error_log($logMessage . "\n", 3, dirname(__DIR__) . '/logs/error.log');
}

// Function to get error log summary
function getErrorSummary() {
    $logFile = dirname(__DIR__) . '/logs/error.log';
    if (!file_exists($logFile)) {
        return ['total' => 0, 'errors' => [], 'last_24h' => 0];
    }
    
    $lines = file($logFile);
    $totalErrors = 0;
    $errorTypes = [];
    $last24h = 0;
    $last24hTime = time() - (24 * 3600);
    
    foreach ($lines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'EXCEPTION') !== false) {
            $totalErrors++;
            
            // Extract error type
            if (preg_match('/\[(ERROR|EXCEPTION|FATAL)\]/', $line, $matches)) {
                $type = $matches[1];
                $errorTypes[$type] = ($errorTypes[$type] ?? 0) + 1;
            }
            
            // Check if in last 24 hours
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $dateMatch)) {
                $logTime = strtotime($dateMatch[1]);
                if ($logTime > $last24hTime) {
                    $last24h++;
                }
            }
        }
    }
    
    return [
        'total' => $totalErrors,
        'by_type' => $errorTypes,
        'last_24h' => $last24h
    ];
}

// Set the handlers
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");
register_shutdown_function("shutdownHandler");

// Log that error handling is active
logMessage('INFO', 'Error logging system initialized for Ayu International Hotel');

// Define debug mode (set to false in production)
define('DEBUG_MODE', true); // Set to false on live server
?>