<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get language parameter
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

// Set language session variable if valid
if ($lang === 'en' || $lang === 'hi') {
    $_SESSION['language'] = $lang;
}

// Return success response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'language' => $_SESSION['language']]);
?>