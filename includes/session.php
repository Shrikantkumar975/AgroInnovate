
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language if not set
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}
?>
