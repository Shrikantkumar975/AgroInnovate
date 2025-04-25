<?php
require_once 'includes/email_config.php';

// Test email parameters
$to = 'akashjasrotia6a@gmail.com';
$subject = 'Test Email from AgroInnovate';
$message = 'This is a test email to verify the SMTP configuration is working correctly.';

try {
    $result = sendEmail($to, $subject, $message);
    if ($result === true) {
        echo "Test email sent successfully!";
    } else {
        echo "Failed to send test email. Error: " . $result;
    }
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
} 