<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        // Find the verification record
        $stmt = $pdo->prepare("SELECT ev.*, u.email 
                            FROM email_verification ev 
                            JOIN users u ON ev.user_id = u.id 
                            WHERE ev.token = ? 
                            AND ev.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $stmt->execute([$token]);
        $verification = $stmt->fetch();
        
        if ($verification) {
            // Update user's verification status
            $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
            if ($stmt->execute([$verification['user_id']])) {
                // Delete the verification token
                $stmt = $pdo->prepare("DELETE FROM email_verification WHERE user_id = ?");
                $stmt->execute([$verification['user_id']]);
                
                $title = ($_SESSION['language'] == 'en') ? 
                    "Email Verification Successful!" : 
                    "ईमेल सत्यापन सफल!";
                $message = ($_SESSION['language'] == 'en') ? 
                    "Your email has been successfully verified. You can now log in to your account." : 
                    "आपका ईमेल सफलतापूर्वक सत्यापित हो गया है। अब आप अपने खाते में लॉग इन कर सकते हैं।";
            } else {
                $title = ($_SESSION['language'] == 'en') ? 
                    "Verification Failed" : 
                    "सत्यापन विफल";
                $message = ($_SESSION['language'] == 'en') ? 
                    "There was an error verifying your email. Please try again later." : 
                    "आपका ईमेल सत्यापित करने में एक त्रुटि हुई। कृपया बाद में पुनः प्रयास करें।";
            }
        } else {
            $title = ($_SESSION['language'] == 'en') ? 
                "Verification Link Expired" : 
                "सत्यापन लिंक समाप्त हो गया";
            $message = ($_SESSION['language'] == 'en') ? 
                "The verification link has expired or is invalid. Please request a new verification email." : 
                "सत्यापन लिंक समाप्त हो गया है या अमान्य है। कृपया एक नया सत्यापन ईमेल का अनुरोध करें।";
        }
    } catch(PDOException $e) {
        $title = ($_SESSION['language'] == 'en') ? 
            "Error" : 
            "त्रुटि";
        $message = ($_SESSION['language'] == 'en') ? 
            "An error occurred. Please try again later." : 
            "एक त्रुटि हुई। कृपया बाद में पुनः प्रयास करें।";
        error_log("Verification error: " . $e->getMessage());
    }
} else {
    $title = ($_SESSION['language'] == 'en') ? 
        "Invalid Request" : 
        "अमान्य अनुरोध";
    $message = ($_SESSION['language'] == 'en') ? 
        "No verification token provided." : 
        "कोई सत्यापन टोकन प्रदान नहीं किया गया।";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-en="Email Verification - AgroInnovate" data-hi="ईमेल सत्यापन - एग्रोइनोवेट">
        <?php echo ($_SESSION['language'] == 'en') ? 'Email Verification - AgroInnovate' : 'ईमेल सत्यापन - एग्रोइनोवेट'; ?>
    </title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="verification-message">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <div class="actions">
            <a href="index.php" class="btn" data-en="Return to Homepage" data-hi="होमपेज पर वापस जाएं">
                <?php echo ($_SESSION['language'] == 'en') ? 'Return to Homepage' : 'होमपेज पर वापस जाएं'; ?>
            </a>
        </div>
    </div>
</body>
</html> 