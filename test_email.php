<?php
require_once 'includes/email_config.php';
require_once 'includes/smtp.php';

// Test email configuration
$to = "test@example.com"; // Replace with your test email
$subject = "Test Email from AgroInnovate";
$message = "This is a test email to verify the email configuration is working correctly.";

// Try to send the email
try {
    $mail = configureEmail();
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->send();
    echo "Test email sent successfully!";
} catch (Exception $e) {
    echo "Error sending test email: " . $mail->ErrorInfo;
}
?> 