<?php
require_once 'includes/header.php';
require_once 'includes/db.php';
require_once 'includes/email_config.php';

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Define language-specific content
$content = [
    'en' => [
        'title' => 'Forgot Password',
        'description' => 'Enter your email address and we\'ll send you a link to reset your password.',
        'email_label' => 'Email Address',
        'submit_button' => 'Send Reset Link',
        'back_to_login' => 'Back to Login',
        'email_sent' => 'If the email exists in our system, you will receive password reset instructions.',
        'email_error' => 'There was an error sending the reset link. Please try again.',
        'invalid_email' => 'Please enter a valid email address.',
        'email_placeholder' => 'Enter your email address'
    ],
    'hi' => [
        'title' => 'पासवर्ड भूल गए',
        'description' => 'अपना ईमेल पता दर्ज करें और हम आपको पासवर्ड रीसेट करने का लिंक भेजेंगे।',
        'email_label' => 'ईमेल पता',
        'submit_button' => 'रीसेट लिंक भेजें',
        'back_to_login' => 'लॉगिन पर वापस जाएं',
        'email_sent' => 'यदि ईमेल हमारे सिस्टम में मौजूद है, तो आपको पासवर्ड रीसेट निर्देश प्राप्त होंगे।',
        'email_error' => 'रीसेट लिंक भेजने में एक त्रुटि हुई। कृपया पुनः प्रयास करें।',
        'invalid_email' => 'कृपया एक वैध ईमेल पता दर्ज करें।',
        'email_placeholder' => 'अपना ईमेल पता दर्ज करें'
    ]
];

$t = $content[$lang];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate a unique token
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // Store the token in the database
                $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
                
                if ($stmt->execute([$email, $token, $expiry])) {
                    // Send reset email
                    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $token;
                    
                    $emailSubject = ($lang == 'en') ? 'Password Reset Request' : 'पासवर्ड रीसेट अनुरोध';
                    
                    // Create HTML email body
                    $emailBody = ($lang == 'en') ? 
                        "<html><body>
                            <h2>Password Reset Request</h2>
                            <p>Hello {$user['name']},</p>
                            <p>You have requested to reset your password. Click the button below to reset it:</p>
                            <p style='text-align: center;'>
                                <a href='$resetLink' 
                                   style='background-color: #4CAF50; color: white; padding: 12px 30px; 
                                          text-decoration: none; border-radius: 5px; display: inline-block;'>
                                    Reset Password
                                </a>
                            </p>
                            <p>Or copy and paste this link in your browser:</p>
                            <p>$resetLink</p>
                            <p>This link will expire in 24 hours.</p>
                            <p>If you didn't request this password reset, please ignore this email.</p>
                        </body></html>" :
                        "<html><body>
                            <h2>पासवर्ड रीसेट अनुरोध</h2>
                            <p>नमस्ते {$user['name']},</p>
                            <p>आपने अपना पासवर्ड रीसेट करने का अनुरोध किया है। इसे रीसेट करने के लिए नीचे दिए गए बटन पर क्लिक करें:</p>
                            <p style='text-align: center;'>
                                <a href='$resetLink' 
                                   style='background-color: #4CAF50; color: white; padding: 12px 30px; 
                                          text-decoration: none; border-radius: 5px; display: inline-block;'>
                                    पासवर्ड रीसेट करें
                                </a>
                            </p>
                            <p>या इस लिंक को अपने ब्राउज़र में कॉपी और पेस्ट करें:</p>
                            <p>$resetLink</p>
                            <p>यह लिंक 24 घंटों में समाप्त हो जाएगा।</p>
                            <p>यदि आपने यह पासवर्ड रीसेट अनुरोध नहीं किया है, तो कृपया इस ईमेल को अनदेखा करें।</p>
                        </body></html>";
                    
                    if (sendEmailSMTP($email, $emailSubject, $emailBody)) {
                        $message = $t['email_sent'];
                        $messageType = 'success';
                    } else {
                        $message = $t['email_error'];
                        $messageType = 'danger';
                    }
                } else {
                    $message = $t['email_error'];
                    $messageType = 'danger';
                }
            } else {
                // For security reasons, show the same message even if email doesn't exist
                $message = $t['email_sent'];
                $messageType = 'success';
            }
        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            $message = $t['email_error'];
            $messageType = 'danger';
        }
    } else {
        $message = $t['invalid_email'];
        $messageType = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <title data-lang-en="Forgot Password - AgroInnovate" data-lang-hi="पासवर्ड भूल गए - एग्रोइनोवेट">
        <?php echo ($lang == 'en') ? 'Forgot Password - AgroInnovate' : 'पासवर्ड भूल गए - एग्रोइनोवेट'; ?>
    </title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4" data-lang-en="<?php echo $t['title']; ?>" data-lang-hi="<?php echo $t['title']; ?>">
                            <?php echo $t['title']; ?>
                        </h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-center mb-4" data-lang-en="<?php echo $content['en']['description']; ?>" data-lang-hi="<?php echo $content['hi']['description']; ?>">
                            <?php echo $t['description']; ?>
                        </p>

                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label" data-lang-en="<?php echo $content['en']['email_label']; ?>" data-lang-hi="<?php echo $content['hi']['email_label']; ?>">
                                    <?php echo $t['email_label']; ?>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                    placeholder="<?php echo $t['email_placeholder']; ?>"
                                    data-lang-en-placeholder="<?php echo $content['en']['email_placeholder']; ?>"
                                    data-lang-hi-placeholder="<?php echo $content['hi']['email_placeholder']; ?>">
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" data-lang-en="<?php echo $content['en']['submit_button']; ?>" data-lang-hi="<?php echo $content['hi']['submit_button']; ?>">
                                    <?php echo $t['submit_button']; ?>
                                </button>
                                <a href="login.php" class="btn btn-link" data-lang-en="<?php echo $content['en']['back_to_login']; ?>" data-lang-hi="<?php echo $content['hi']['back_to_login']; ?>">
                                    <?php echo $t['back_to_login']; ?>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 