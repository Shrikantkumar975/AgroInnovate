<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isset($_GET['email'])) {
    $email = sanitizeInput($_GET['email']);
    
    try {
        // Get user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            if ($user['is_verified'] == 1) {
                $_SESSION['message'] = ($_SESSION['language'] == 'en') ? 
                    "Your email is already verified. You can login now." : 
                    "आपका ईमेल पहले ही सत्यापित हो चुका है। अब आप लॉगिन कर सकते हैं।";
                $_SESSION['message_type'] = "info";
                header('Location: login.php');
                exit;
            }
            
            // Generate new verification token
            $verification_token = bin2hex(random_bytes(32));
            
            // Delete any existing tokens for this user
            $stmt = $pdo->prepare("DELETE FROM email_verification WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            
            // Insert new verification token
            $stmt = $pdo->prepare("INSERT INTO email_verification (user_id, token) VALUES (?, ?)");
            $stmt->execute([$user['id'], $verification_token]);
            
            // Send verification email
            $verification_link = "http://" . $_SERVER['HTTP_HOST'] . "/verify.php?token=" . $verification_token;
            
            $title_text = ($_SESSION['language'] == 'en') ? 'Email Verification' : 'ईमेल सत्यापन';
            $dear_text = ($_SESSION['language'] == 'en') ? 'Dear' : 'प्रिय';
            $request_text = ($_SESSION['language'] == 'en') ? 
                'You requested a new verification link. Please verify your email address by clicking the button below:' : 
                'आपने एक नया सत्यापन लिंक अनुरोध किया है। कृपया नीचे दिए गए बटन पर क्लिक करके अपना ईमेल पता सत्यापित करें:';
            $button_text = ($_SESSION['language'] == 'en') ? 'Verify Email Address' : 'ईमेल पता सत्यापित करें';
            $copy_text = ($_SESSION['language'] == 'en') ? 'Or copy and paste this link in your browser:' : 'या इस लिंक को अपने ब्राउज़र में कॉपी और पेस्ट करें:';
            $expire_text = ($_SESSION['language'] == 'en') ? 'This link will expire in 24 hours.' : 'यह लिंक 24 घंटों में समाप्त हो जाएगा।';
            $ignore_text = ($_SESSION['language'] == 'en') ? 
                "If you didn't request this verification email, please ignore it." : 
                "यदि आपने यह सत्यापन ईमेल नहीं मांगा है, तो कृपया इसे अनदेखा करें।";
            
            $message = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #2E7D32;'>$title_text</h2>
                        <p>$dear_text {$user['name']},</p>
                        <p>$request_text</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='$verification_link' 
                               style='background-color: #2E7D32; color: white; padding: 12px 30px; 
                                      text-decoration: none; border-radius: 5px;'>
                                $button_text
                            </a>
                        </div>
                        <p>$copy_text</p>
                        <p style='word-break: break-all;'>$verification_link</p>
                        <p>$expire_text</p>
                        <hr style='margin: 30px 0;'>
                        <p style='color: #666; font-size: 12px;'>
                            $ignore_text
                        </p>
                    </div>
                </body>
                </html>
            ";
            
            $subject = ($_SESSION['language'] == 'en') ? 
                "AgroInnovate - Email Verification" : 
                "एग्रोइनोवेट - ईमेल सत्यापन";
            
            if (sendEmail($email, $subject, $message)) {
                $_SESSION['message'] = ($_SESSION['language'] == 'en') ? 
                    "A new verification email has been sent. Please check your inbox." : 
                    "एक नया सत्यापन ईमेल भेज दिया गया है। कृपया अपना इनबॉक्स चेक करें।";
                $_SESSION['message_type'] = "success";
            } else {
                throw new Exception("Failed to send verification email");
            }
        } else {
            $_SESSION['message'] = ($_SESSION['language'] == 'en') ? 
                "Email address not found." : 
                "ईमेल पता नहीं मिला।";
            $_SESSION['message_type'] = "error";
        }
    } catch (Exception $e) {
        $_SESSION['message'] = ($_SESSION['language'] == 'en') ? 
            "An error occurred. Please try again later." : 
            "एक त्रुटि हुई। कृपया बाद में पुनः प्रयास करें।";
        $_SESSION['message_type'] = "error";
        error_log("Resend verification error: " . $e->getMessage());
    }
    
    header('Location: login.php');
    exit;
} else {
    header('Location: login.php');
    exit;
} 