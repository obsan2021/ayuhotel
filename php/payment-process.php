<?php
// Payment Processing for Ayu Hotel

require_once 'config.php';
require_once 'db-connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get booking ID and payment details
$bookingId = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
$cardNumber = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
$expiry = filter_input(INPUT_POST, 'expiry', FILTER_SANITIZE_STRING);
$cvv = filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_STRING);
$cardName = filter_input(INPUT_POST, 'card_name', FILTER_SANITIZE_STRING);

// Validation
$errors = [];

if (empty($bookingId)) {
    $errors[] = 'Booking ID is required';
}

if (empty($cardNumber) || !luhnCheck($cardNumber)) {
    $errors[] = 'Invalid card number';
}

if (empty($expiry) || !validateExpiry($expiry)) {
    $errors[] = 'Invalid expiry date';
}

if (empty($cvv) || strlen($cvv) < 3) {
    $errors[] = 'Invalid CVV';
}

if (empty($cardName) || strlen($cardName) < 2) {
    $errors[] = 'Cardholder name is required';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get booking details
    $booking = $db->fetchOne("SELECT * FROM bookings WHERE id = ? AND status = 'pending'", [$bookingId]);
    
    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking or already processed']);
        exit;
    }
    
    // Process payment (integrate with payment gateway)
    $paymentResult = processPayment($booking['total_price'], $cardNumber, $expiry, $cvv, $cardName);
    
    if ($paymentResult['success']) {
        // Update booking status
        $db->update('bookings', 
            ['status' => 'confirmed', 'payment_status' => 'paid', 'payment_id' => $paymentResult['transaction_id']],
            'id = ?',
            [$bookingId]
        );
        
        // Record payment
        $db->insert('payments', [
            'booking_id' => $bookingId,
            'amount' => $booking['total_price'],
            'payment_method' => 'card',
            'transaction_id' => $paymentResult['transaction_id'],
            'status' => 'success',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Payment successful! Your booking is confirmed.',
            'transaction_id' => $paymentResult['transaction_id']
        ]);
    } else {
        // Update booking with failed payment
        $db->update('bookings',
            ['payment_status' => 'failed'],
            'id = ?',
            [$bookingId]
        );
        
        echo json_encode(['success' => false, 'message' => $paymentResult['message']]);
    }
    
} catch (Exception $e) {
    error_log("Payment Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}

// Helper functions
function luhnCheck($cardNumber) {
    $cardNumber = preg_replace('/\D/', '', $cardNumber);
    $sum = 0;
    $isEven = false;
    
    for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
        $digit = (int)$cardNumber[$i];
        
        if ($isEven) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        
        $sum += $digit;
        $isEven = !$isEven;
    }
    
    return $sum % 10 === 0;
}

function validateExpiry($expiry) {
    $parts = explode('/', $expiry);
    if (count($parts) !== 2) return false;
    
    $month = (int)$parts[0];
    $year = (int)('20' . $parts[1]);
    
    if ($month < 1 || $month > 12) return false;
    
    $expiryDate = new DateTime("$year-$month-01");
    $now = new DateTime();
    
    return $expiryDate > $now;
}

function processPayment($amount, $cardNumber, $expiry, $cvv, $cardName) {
    // Integrate with actual payment gateway (Stripe, PayPal, etc.)
    // This is a placeholder implementation
    
    // Simulate payment processing
    sleep(1);
    
    // For demo purposes, always succeed
    return [
        'success' => true,
        'transaction_id' => 'TXN' . strtoupper(uniqid()),
        'message' => 'Payment processed successfully'
    ];
}
?>
