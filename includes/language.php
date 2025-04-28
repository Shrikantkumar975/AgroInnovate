<?php
/**
 * Language functions for AgroInnovate
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language if not set
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

/**
 * Get the current language
 * 
 * @return string The current language code
 */
function getCurrentLanguage() {
    return $_SESSION['lang'];
}

/**
 * Set the current language
 * 
 * @param string $lang The language code to set
 * @return bool Whether the language was set successfully
 */
function setCurrentLanguage($lang) {
    if (in_array($lang, ['en', 'hi', 'pa'])) {
        $_SESSION['lang'] = $lang;
        
        // Also set a cookie for persistence across sessions
        setcookie('preferred_language', $lang, time() + (86400 * 30), '/'); // 30 days
        
        return true;
    }
    return false;
}

/**
 * Load language strings for the current language
 * 
 * @return array The language strings
 */
function loadLanguage() {
    $language = getCurrentLanguage();
    $langFile = __DIR__ . '/languages/' . $language . '.php';
    
    if (file_exists($langFile)) {
        require_once $langFile;
        return isset($lang) ? $lang : array();
    }
    
    // Fallback to English if language file doesn't exist
    require_once __DIR__ . '/languages/en.php';
    return $lang;
}

/**
 * Translate a string key to the current language
 * 
 * @param string $key The translation key
 * @param string|null $default The default value if key is not found
 * @return string The translated string
 */
function __($key, $default = null) {
    static $translations = null;
    
    if ($translations === null) {
        $translations = loadLanguage();
    }
    
    return $translations[$key] ?? $default ?? $key;
}
?> 