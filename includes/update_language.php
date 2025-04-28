<?php
session_start();

if (isset($_GET['language'])) {
    $language = $_GET['language'];
    
    // Only allow valid languages
    if (in_array($language, ['en', 'hi', 'pa'])) {
        $_SESSION['lang'] = $language;
    }
}

// Redirect back to previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?> 