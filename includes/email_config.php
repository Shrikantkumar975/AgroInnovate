<?php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'akashjasrotia6a@gmail.com');
define('SMTP_PASSWORD', 'fwfk ebuw xqdu iqah');
define('SMTP_FROM_NAME', 'AgroInnovate');
define('SMTP_FROM_EMAIL', 'akashjasrotia6a@gmail.com');

// Require Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message body
 * @param string $from Sender email address (optional)
 * @return bool Whether the email was sent successfully
 */
function sendEmail($to, $subject, $message, $from = null) {
    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom($from ?: SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        if ($from) {
            $mail->addReplyTo($from);
        }

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send email to $to. Error: " . $mail->ErrorInfo);
        return false;
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
 * Send email using SMTP class
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @return bool Success status
 */
function sendEmailSMTP($to, $subject, $message) {
    $mail = new SMTP();
    $mail->isSMTP();
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->subject = $subject;
    $mail->body = $message;
    
    return $mail->send();
}

// PHPMailer configuration function
function configureMailer($mail) {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = 'tls';
    $mail->Port = SMTP_PORT;
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    
    // Set UTF-8 encoding
    $mail->CharSet = 'UTF-8';
    
    return $mail;
} 