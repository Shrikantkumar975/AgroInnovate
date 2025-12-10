<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/language.php';

// Define language-specific content
$header_content = [
    'en' => [
        'home' => 'Home',
        'weather' => 'Weather',
        'market' => 'Market',
        'education' => 'Education',
        'community' => 'Community',
        'about' => 'About Us',
        'contact' => 'Contact',
        'view_profile' => 'View Profile',
        'edit_profile' => 'Edit Profile',
        'settings' => 'Settings',
        'logout' => 'Logout',
        'logout_confirm' => 'Are you sure you want to logout?',
        'login' => 'Login',
        'register' => 'Register',
        'language_english' => 'English',
        'language_hindi' => 'हिन्दी',
        'language_punjabi' => 'ਪੰਜਾਬੀ'
    ],
    'hi' => [
        'home' => 'होम',
        'weather' => 'मौसम',
        'market' => 'बाज़ार',
        'education' => 'शिक्षा',
        'community' => 'समुदाय',
        'about' => 'हमारे बारे में',
        'contact' => 'संपर्क करें',
        'view_profile' => 'प्रोफ़ाइल देखें',
        'edit_profile' => 'प्रोफ़ाइल संपादित करें',
        'settings' => 'सेटिंग्स',
        'logout' => 'लॉग आउट',
        'logout_confirm' => 'क्या आप लॉग आउट करना चाहते हैं?',
        'login' => 'लॉग इन',
        'register' => 'पंजीकरण',
        'language_english' => 'English',
        'language_hindi' => 'हिन्दी',
        'language_punjabi' => 'ਪੰਜਾਬੀ'
    ]
];

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
$t = $header_content[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-lang-en="AgroInnovate" data-lang-hi="एग्रोइनोवेट">
        <?php echo ($lang == 'en') ? 'AgroInnovate' : 'एग्रोइनोवेट'; ?>
    </title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <!-- Language Selector -->
    <div class="language-selector-floating">
        <div class="dropdown">
            <button class="btn btn-light rounded-circle shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-globe2"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="includes/update_language.php?language=en" data-lang-en="English" data-lang-hi="English"><?php echo $t['language_english']; ?></a></li>
                <li><a class="dropdown-item" href="includes/update_language.php?language=hi" data-lang-en="हिन्दी" data-lang-hi="हिन्दी"><?php echo $t['language_hindi']; ?></a></li>
                <li><a class="dropdown-item" href="includes/update_language.php?language=pa" data-lang-en="ਪੰਜਾਬੀ" data-lang-hi="ਪੰਜਾਬੀ"><?php echo $t['language_punjabi']; ?></a></li>
            </ul>
        </div>
    </div>

    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center me-5" href="index.php">
                    <img src="assets/logo-transparent.png" alt="AgroInnovate Logo" class="navbar-logo">
                    <span class="brand-text" data-lang-en="AgroInnovate" data-lang-hi="एग्रोइनोवेट">
                        <?php echo ($lang == 'en') ? 'AgroInnovate' : 'एग्रोइनोवेट'; ?>
                    </span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php" data-lang-en="Home" data-lang-hi="होम"><?php echo $t['home']; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'weather.php') ? 'active' : ''; ?>" href="weather.php" data-lang-en="Weather" data-lang-hi="मौसम"><?php echo $t['weather']; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'market.php') ? 'active' : ''; ?>" href="market.php" data-lang-en="Market" data-lang-hi="बाज़ार"><?php echo $t['market']; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'education.php') ? 'active' : ''; ?>" href="education.php" data-lang-en="Education" data-lang-hi="शिक्षा"><?php echo $t['education']; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'community.php') ? 'active' : ''; ?>" href="community.php" data-lang-en="Community" data-lang-hi="समुदाय"><?php echo $t['community']; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php" data-lang-en="About Us" data-lang-hi="हमारे बारे में"><?php echo $t['about']; ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php" data-lang-en="Contact" data-lang-hi="संपर्क करें"><?php echo $t['contact']; ?></a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item ms-2 d-flex align-items-center">
                                <div class="dropdown me-3">
                                    <button class="btn btn-link p-0 profile-avatar" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="avatar-circle">
                                            <?php 
                                            // Get first letter of user's name or email
                                            $initial = isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'U';
                                            echo $initial;
                                            ?>
                                        </div>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="profile.php" data-lang-en="View Profile" data-lang-hi="प्रोफ़ाइल देखें"><i class="bi bi-person me-2"></i><?php echo $t['view_profile']; ?></a></li>
                                        <li><a class="dropdown-item" href="edit_profile.php" data-lang-en="Edit Profile" data-lang-hi="प्रोफ़ाइल संपादित करें"><i class="bi bi-pencil-square me-2"></i><?php echo $t['edit_profile']; ?></a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="settings.php" data-lang-en="Settings" data-lang-hi="सेटिंग्स"><i class="bi bi-gear me-2"></i><?php echo $t['settings']; ?></a></li>
                                    </ul>
                                </div>
                                <a href="includes/logout.php" class="btn btn-danger" onclick="return confirm('<?php echo $t['logout_confirm']; ?>');" data-lang-en="Logout" data-lang-hi="लॉग आउट">
                                    <i class="bi bi-box-arrow-right me-1"></i><?php echo $t['logout']; ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-2">
                                <div class="auth-buttons">
                                    <div class="btn-group">
                                        <a href="login.php" class="btn btn-success" data-lang-en="Login" data-lang-hi="लॉग इन"><?php echo $t['login']; ?></a>
                                        <a href="login.php?action=register" class="btn btn-success" data-lang-en="Register" data-lang-hi="पंजीकरण"><?php echo $t['register']; ?></a>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
    <script>
        // Test if Bootstrap is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Bootstrap version:', bootstrap.Dropdown.VERSION);
            
            // Initialize dropdown manually
            const dropdownButton = document.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdownButton) {
                const dropdown = new bootstrap.Dropdown(dropdownButton);
                
                // Add click handler to test
                dropdownButton.addEventListener('click', function(e) {
                    console.log('Dropdown button clicked');
                });
            }
        });
    </script>
    <style>
        .language-selector-floating {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
        }

        .language-selector-floating .btn {
            width: 48px;
            height: 48px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .language-selector-floating .bi-globe2 {
            font-size: 1.5rem;
            color: #4CAF50;
        }

        .language-selector-floating .dropdown-menu {
            min-width: 150px;
        }

        /* Hide the dropdown toggle indicator */
        .language-selector-floating .dropdown-toggle::after {
            display: none;
        }

        @media (max-width: 768px) {
            .language-selector-floating {
                bottom: 15px;
                right: 15px;
            }
        }

        /* Profile Avatar Styles */
        .profile-avatar {
            text-decoration: none;
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .profile-avatar:hover .avatar-circle {
            background-color: #3d8b40;
        }

        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            padding: 8px 16px;
            color: #333;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item i {
            color: #4CAF50;
        }
    </style>
    <script src="js/language.js"></script>
</body>
</html>
