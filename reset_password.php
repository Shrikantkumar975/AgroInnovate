<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Define language-specific content
$content = [
    'en' => [
        'title' => 'Reset Password',
        'new_password' => 'New Password',
        'confirm_password' => 'Confirm Password',
        'reset_button' => 'Reset Password',
        'back_to_login' => 'Back to Login',
        'password_mismatch' => 'Passwords do not match.',
        'invalid_token' => 'Invalid or expired reset token.',
        'password_updated' => 'Password has been updated successfully. You can now login with your new password.',
        'error_updating' => 'Error updating password. Please try again.',
        'password_requirements' => 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.',
        'new_password_placeholder' => 'Enter your new password',
        'confirm_password_placeholder' => 'Confirm your new password'
    ],
    'hi' => [
        'title' => 'पासवर्ड रीसेट करें',
        'new_password' => 'नया पासवर्ड',
        'confirm_password' => 'पासवर्ड की पुष्टि करें',
        'reset_button' => 'पासवर्ड रीसेट करें',
        'back_to_login' => 'लॉगिन पर वापस जाएं',
        'password_mismatch' => 'पासवर्ड मेल नहीं खाते।',
        'invalid_token' => 'अमान्य या समाप्त रीसेट टोकन।',
        'password_updated' => 'पासवर्ड सफलतापूर्वक अपडेट कर दिया गया है। अब आप अपने नए पासवर्ड से लॉगिन कर सकते हैं।',
        'error_updating' => 'पासवर्ड अपडेट करने में त्रुटि। कृपया पुनः प्रयास करें।',
        'password_requirements' => 'पासवर्ड कम से कम 8 अक्षर लंबा होना चाहिए और इसमें कम से कम एक अपरकेस अक्षर, एक लोअरकेस अक्षर और एक संख्या शामिल होनी चाहिए।',
        'new_password_placeholder' => 'अपना नया पासवर्ड दर्ज करें',
        'confirm_password_placeholder' => 'अपने नए पासवर्ड की पुष्टि करें'
    ]
];

$t = $content[$lang];
$message = '';
$messageType = '';
$validToken = false;
$tokenEmail = '';

// Verify token
if (isset($_GET['token'])) {
    try {
        $token = $_GET['token'];
        $stmt = $pdo->prepare("SELECT email, expiry FROM password_resets WHERE token = ? AND used = 0");
        $stmt->execute([$token]);
        $result = $stmt->fetch();
        
        if ($result && strtotime($result['expiry']) > time()) {
            $validToken = true;
            $tokenEmail = $result['email'];
        }
    } catch (PDOException $e) {
        error_log("Error verifying token: " . $e->getMessage());
        $message = $t['invalid_token'];
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate password
    if ($password !== $confirmPassword) {
        $message = $t['password_mismatch'];
        $messageType = 'danger';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $message = $t['password_requirements'];
        $messageType = 'danger';
    } else {
        try {
            // Hash the new password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Update the password and mark token as used
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($stmt->execute([$hashedPassword, $tokenEmail])) {
                // Mark token as used
                $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $stmt->execute([$_GET['token']]);
                
                $message = $t['password_updated'];
                $messageType = 'success';
                // Set a flag to trigger JavaScript redirect
                $redirectToLogin = true;
            } else {
                $message = $t['error_updating'];
                $messageType = 'danger';
            }
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            $message = $t['error_updating'];
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <title data-lang-en="Reset Password - AgroInnovate" data-lang-hi="पासवर्ड रीसेट करें - एग्रोइनोवेट">
        <?php echo ($lang == 'en') ? 'Reset Password - AgroInnovate' : 'पासवर्ड रीसेट करें - एग्रोइनोवेट'; ?>
    </title>
    <?php if (isset($redirectToLogin) && $redirectToLogin): ?>
    <script>
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 3000);
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4" data-lang-en="<?php echo $content['en']['title']; ?>" data-lang-hi="<?php echo $content['hi']['title']; ?>">
                            <?php echo $t['title']; ?>
                        </h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($validToken): ?>
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?token=' . htmlspecialchars($_GET['token']); ?>">
                                <div class="mb-3">
                                    <label for="password" class="form-label" data-lang-en="<?php echo $content['en']['new_password']; ?>" data-lang-hi="<?php echo $content['hi']['new_password']; ?>">
                                        <?php echo $t['new_password']; ?>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        placeholder="<?php echo $t['new_password_placeholder']; ?>"
                                        data-lang-en-placeholder="<?php echo $content['en']['new_password_placeholder']; ?>"
                                        data-lang-hi-placeholder="<?php echo $content['hi']['new_password_placeholder']; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label" data-lang-en="<?php echo $content['en']['confirm_password']; ?>" data-lang-hi="<?php echo $content['hi']['confirm_password']; ?>">
                                        <?php echo $t['confirm_password']; ?>
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                        placeholder="<?php echo $t['confirm_password_placeholder']; ?>"
                                        data-lang-en-placeholder="<?php echo $content['en']['confirm_password_placeholder']; ?>"
                                        data-lang-hi-placeholder="<?php echo $content['hi']['confirm_password_placeholder']; ?>">
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" data-lang-en="<?php echo $content['en']['reset_button']; ?>" data-lang-hi="<?php echo $content['hi']['reset_button']; ?>">
                                        <?php echo $t['reset_button']; ?>
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger" role="alert" data-lang-en="<?php echo $content['en']['invalid_token']; ?>" data-lang-hi="<?php echo $content['hi']['invalid_token']; ?>">
                                <?php echo $t['invalid_token']; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-link" data-lang-en="<?php echo $content['en']['back_to_login']; ?>" data-lang-hi="<?php echo $content['hi']['back_to_login']; ?>">
                                <?php echo $t['back_to_login']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 