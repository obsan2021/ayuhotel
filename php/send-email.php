<?php
// Email Sending Functions for Ayu Hotel

require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer (adjust path as needed)
require_once '../vendor/autoload.php';

function sendContactEmail($name, $email, $phone, $subject, $message) {
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress(SITE_EMAIL);
        $mail->addReplyTo($email, $name);
        
        $mail->Subject = "New Contact Form Submission: $subject";
        $mail->Body = "
            <h2>New Contact Form Submission</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Subject:</strong> $subject</p>
            <hr>
            <p><strong>Message:</strong></p>
            <p>" . nl2br($message) . "</p>
        ";
        $mail->AltBody = strip_tags($mail->Body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendBookingConfirmation($bookingRef, $firstName, $email, $checkIn, $checkOut, $nights, $roomType, $totalPrice) {
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($email, $firstName);
        
        $mail->Subject = "Booking Confirmation - $bookingRef";
        $mail->Body = "
            <h2>Booking Confirmation</h2>
            <p>Dear $firstName,</p>
            <p>Thank you for choosing Ayu Hotel. Your booking has been confirmed.</p>
            
            <h3>Booking Details</h3>
            <p><strong>Booking Reference:</strong> $bookingRef</p>
            <p><strong>Check-in:</strong> $checkIn</p>
            <p><strong>Check-out:</strong> $checkOut</p>
            <p><strong>Duration:</strong> $nights night(s)</p>
            <p><strong>Room Type:</strong> $roomType</p>
            <p><strong>Total Amount:</strong> $$totalPrice</p>
            
            <h3>Important Information</h3>
            <ul>
                <li>Please present your booking reference at check-in</li>
                <li>Check-in time: 2:00 PM</li>
                <li>Check-out time: 11:00 AM</li>
                <li>Valid ID required at check-in</li>
            </ul>
            
            <p>If you have any questions, please contact us at " . SITE_EMAIL . " or " . SITE_PHONE . "</p>
            
            <p>We look forward to welcoming you to Ayu Hotel!</p>
            
            <p>Best regards,<br>Ayu Hotel Team</p>
        ";
        $mail->AltBody = strip_tags($mail->Body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

function sendPasswordReset($email, $resetLink) {
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($email);
        
        $mail->Subject = "Password Reset Request - Ayu Hotel";
        $mail->Body = "
            <h2>Password Reset Request</h2>
            <p>You have requested to reset your password for Ayu Hotel admin panel.</p>
            
            <p>Click the link below to reset your password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            
            <p>This link will expire in 1 hour.</p>
            
            <p>If you did not request this password reset, please ignore this email.</p>
            
            <p>Best regards,<br>Ayu Hotel Team</p>
        ";
        $mail->AltBody = strip_tags($mail->Body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
