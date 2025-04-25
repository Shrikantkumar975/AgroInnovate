<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/language.php';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroInnovate</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <!-- Simple Language Selector -->
    <div class="language-selector-floating">
        <div class="dropdown">
            <button class="btn btn-light rounded-circle shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-globe2"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="includes/update_language.php?language=en">English</a></li>
                <li><a class="dropdown-item" href="includes/update_language.php?language=hi">हिन्दी</a></li>
                <li><a class="dropdown-item" href="includes/update_language.php?language=pa">ਪੰਜਾਬੀ</a></li>
            </ul>
        </div>
    </div>

    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center me-5" href="index.php">
                    <img src="assets/logo-transparent.png" alt="AgroInnovate Logo" class="navbar-logo">
                    <span class="brand-text">AgroInnovate</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'weather.php') ? 'active' : ''; ?>" href="weather.php">Weather</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'market.php') ? 'active' : ''; ?>" href="market.php">Market</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'education.php') ? 'active' : ''; ?>" href="education.php">Education</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'community.php') ? 'active' : ''; ?>" href="community.php">Community</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact</a>
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
                                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>View Profile</a></li>
                                        <li><a class="dropdown-item" href="edit_profile.php"><i class="bi bi-pencil-square me-2"></i>Edit Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                    </ul>
                                </div>
                                <a href="includes/logout.php" class="btn btn-danger" onclick="return confirm('Are you sure you want to logout?');">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-2">
                                <div class="auth-buttons">
                                    <div class="btn-group">
                                        <a href="login.php" class="btn btn-success">Login</a>
                                        <a href="login.php?action=register" class="btn btn-success">Register</a>
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
</body>
</html>
