<?php
// Start the session
session_start();

// Set default language if not set
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = 'en';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroInnovate – Empowering Indian Agriculture</title>
    <meta name="description" content="AgroInnovate provides real-time weather updates, market information, and educational resources for Indian farmers.">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">
    <link rel="stylesheet" href="/css/styles.css">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/">
                        <img src="/assets/logo.svg" alt="AgroInnovate Logo" height="40">
                        <span>AgroInnovate</span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="/" data-en="Home" data-hi="होम">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Home' : 'होम'; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'weather.php') ? 'active' : ''; ?>" href="/weather.php" data-en="Weather" data-hi="मौसम">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Weather' : 'मौसम'; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'market.php') ? 'active' : ''; ?>" href="/market.php" data-en="Market" data-hi="बाज़ार">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Market' : 'बाज़ार'; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'education.php') ? 'active' : ''; ?>" href="/education.php" data-en="Education" data-hi="शिक्षा">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Education' : 'शिक्षा'; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'community.php') ? 'active' : ''; ?>" href="/community.php" data-en="Community" data-hi="समुदाय">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Community' : 'समुदाय'; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="/about.php" data-en="About Us" data-hi="हमारे बारे में">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'About Us' : 'हमारे बारे में'; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="/contact.php" data-en="Contact" data-hi="संपर्क">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Contact' : 'संपर्क'; ?>
                                </a>
                            </li>
                            <li class="nav-item language-switch">
                                <button id="language-toggle" class="btn btn-sm">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'हिंदी' : 'English'; ?>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main>
