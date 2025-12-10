<?php
// Load .env configuration (ensures getenv vars are available)
require_once __DIR__ . '/config.php';

// Email Configuration (read from environment; set in .env)
define('SMTP_HOST', getenv('SMTP_HOST') !== false ? getenv('SMTP_HOST') : 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') !== false ? (int)getenv('SMTP_PORT') : 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') !== false ? getenv('SMTP_USERNAME') : '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') !== false ? getenv('SMTP_PASSWORD') : '');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') !== false ? getenv('SMTP_FROM_NAME') : 'AgroInnovate');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') !== false ? getenv('SMTP_FROM_EMAIL') : '');

// Require Composer's autoloader
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo "CRITICAL: Autoload file not found at $autoloadPath\n";
} else {
    echo "Autoload found at $autoloadPath\n";
}
require_once $autoloadPath;

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
        $mail->SMTPDebug = 0; // Set to 2 for detailed debug output
        $mail->Debugoutput = function($str, $level) {
            error_log("SMTP Debug: $str");
        };
        
        // Set default from address
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        return $mail;
    } catch (Exception $e) {
        echo "Error configuring email: " . $e->getMessage() . "\n";
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