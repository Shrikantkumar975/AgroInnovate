<?php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'akaxmovie@gmail.com');
define('SMTP_PASSWORD', 'pgbznlemckbrdofy');
define('SMTP_FROM_NAME', 'AgroInnovate');
define('SMTP_FROM_EMAIL', 'akaxmovie@gmail.com');

// Require Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Configure a PHPMailer instance with default settings
 * 
 * @return PHPMailer Configured PHPMailer instance
 */
function configureEmail() {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Enable debugging
        $mail->SMTPDebug = 2; // Set to 2 for detailed debug output
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };
        
        // Set default from address
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        return $mail;
    } catch (Exception $e) {
        error_log("Error configuring email: " . $e->getMessage());
        return null;
    }
}

/**
 * Send email using PHP's built-in mail function
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @return bool Success status
 */
function sendEmailSimple($to, $subject, $message) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Send email using SMTP with PHPMailer
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @return bool Success status
 */
function sendEmailSMTP($to, $subject, $message) {
    try {
        $mail = configureEmail();
        if (!$mail) {
            throw new Exception("Failed to configure email");
        }

        // Recipients
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        // Send the email
        $result = $mail->send();
        error_log("Email sent successfully to: " . $to);
        return $result;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
} 