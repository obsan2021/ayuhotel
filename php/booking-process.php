<?php
// Booking Processing for Ayu Hotel

require_once 'config.php';
require_once 'db-connection.php';
require_once 'send-email.php';

header('Content-Type: application/json');

// Log booking attempt
logMessage('INFO', 'New booking attempt received', $_POST);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Sanitize and validate input
$checkIn = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
$checkOut = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
$guests = filter_input(INPUT_POST, 'guests', FILTER_VALIDATE_INT);
$roomType = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
$firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
$lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$specialRequests = filter_input(INPUT_POST, 'special_requests', FILTER_SANITIZE_STRING);
$promoCode = filter_input(INPUT_POST, 'promo_code', FILTER_SANITIZE_STRING);

// Validation
$errors = [];

if (empty($checkIn) || !strtotime($checkIn)) {
    $errors[] = 'Valid check-in date is required';
}

if (empty($checkOut) || !strtotime($checkOut)) {
    $errors[] = 'Valid check-out date is required';
}

if (strtotime($checkOut) <= strtotime($checkIn)) {
    $errors[] = 'Check-out date must be after check-in date';
}

if (empty($guests) || $guests < 1) {
    $errors[] = 'Number of guests is required';
}

if (empty($roomType)) {
    $errors[] = 'Room type is required';
}

if (empty($firstName) || strlen($firstName) < 2) {
    $errors[] = 'First name is required';
}

if (empty($lastName) || strlen($lastName) < 2) {
    $errors[] = 'Last name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email address is required';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
}

if (!empty($errors)) {
    logMessage('WARNING', 'Booking validation failed', ['errors' => $errors, 'data' => $_POST]);
    echo json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Calculate nights and total price
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $nights = $checkInDate->diff($checkOutDate)->days;
    
    // Get room price
    $room = $db->fetchOne("SELECT price FROM rooms WHERE type = ?", [$roomType]);
    $roomPrice = $room ? $room['price'] : 0;
    $totalPrice = $nights * $roomPrice;
    
    // Apply promo code if provided
    $discount = 0;
    if (!empty($promoCode)) {
        $promo = $db->fetchOne("SELECT discount FROM offers WHERE code = ? AND valid_until >= CURDATE()", [$promoCode]);
        if ($promo) {
            $discount = ($totalPrice * $promo['discount']) / 100;
            $totalPrice -= $discount;
        }
    }
    
    // Generate booking reference
    $bookingRef = 'AYU' . strtoupper(uniqid());
    
    // Insert booking
    $data = [
        'booking_ref' => $bookingRef,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'nights' => $nights,
        'guests' => $guests,
        'room_type' => $roomType,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'special_requests' => $specialRequests,
        'promo_code' => $promoCode,
        'discount' => $discount,
        'total_price' => $totalPrice,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $result = $db->insert('bookings', $data);
    
    if ($result) {
        $bookingId = $db->lastInsertId();
        
        logMessage('SUCCESS', 'Booking created successfully', [
            'booking_id' => $bookingId,
            'booking_ref' => $bookingRef,
            'name' => $firstName . ' ' . $lastName,
            'email' => $email,
            'total' => $totalPrice
        ]);
        
        // Send confirmation email
        $emailSent = sendBookingConfirmation($bookingRef, $firstName, $email, $checkIn, $checkOut, $nights, $roomType, $totalPrice);
        
        echo json_encode([
            'success' => true,
            'message' => 'Booking successful! Check your email for confirmation.',
            'booking_ref' => $bookingRef,
            'booking_id' => $bookingId
        ]);
    } else {
        logMessage('ERROR', 'Database insert failed for booking', ['data' => $data]);
        echo json_encode(['success' => false, 'message' => 'Failed to process booking. Please try again.']);
    }
    
} catch (Exception $e) {
    logMessage('ERROR', 'Booking process failed', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
?>
