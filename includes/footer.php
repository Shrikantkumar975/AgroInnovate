<?php
// Define footer-specific language content
$footer_content = [
    'en' => [
        'about_heading' => 'About AgroInnovate',
        'about_text' => 'AgroInnovate is dedicated to empowering Indian farmers with technology, information, and community support.',
        'quick_links' => 'Quick Links',
        'home' => 'Home',
        'weather' => 'Weather',
        'market' => 'Market',
        'education' => 'Education',
        'resources' => 'Resources',
        'community' => 'Community',
        'about' => 'About Us',
        'contact' => 'Contact',
        'connect' => 'Connect With Us',
        'copyright' => '&copy; 2025 AgroInnovate. All rights reserved.',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service'
    ],
    'hi' => [
        'about_heading' => 'एग्रोइनोवेट के बारे में',
        'about_text' => 'एग्रोइनोवेट भारतीय किसानों को प्रौद्योगिकी, जानकारी और सामुदायिक समर्थन के साथ सशक्त बनाने के लिए समर्पित है।',
        'quick_links' => 'त्वरित लिंक',
        'home' => 'होम',
        'weather' => 'मौसम',
        'market' => 'बाज़ार',
        'education' => 'शिक्षा',
        'resources' => 'संसाधन',
        'community' => 'समुदाय',
        'about' => 'हमारे बारे में',
        'contact' => 'संपर्क',
        'connect' => 'हमसे जुड़ें',
        'copyright' => '&copy; 2025 एग्रोइनोवेट. सर्वाधिकार सुरक्षित।',
        'privacy_policy' => 'गोपनीयता नीति',
        'terms_of_service' => 'सेवा की शर्तें'
    ]
];

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
$t = $footer_content[$lang];
?>
    </main>
    <footer class="site-footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-heading" data-lang-en="About AgroInnovate" data-lang-hi="एग्रोइनोवेट के बारे में">
                        <?php echo $t['about_heading']; ?>
                    </h5>
                    <p data-lang-en="AgroInnovate is dedicated to empowering Indian farmers with technology, information, and community support." data-lang-hi="एग्रोइनोवेट भारतीय किसानों को प्रौद्योगिकी, जानकारी और सामुदायिक समर्थन के साथ सशक्त बनाने के लिए समर्पित है।">
                        <?php echo $t['about_text']; ?>
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-heading" data-lang-en="Quick Links" data-lang-hi="त्वरित लिंक">
                        <?php echo $t['quick_links']; ?>
                    </h5>
                    <ul class="list-unstyled">
                        <li><a href="/" data-lang-en="Home" data-lang-hi="होम">
                            <?php echo $t['home']; ?>
                        </a></li>
                        <li><a href="/weather.php" data-lang-en="Weather" data-lang-hi="मौसम">
                            <?php echo $t['weather']; ?>
                        </a></li>
                        <li><a href="/market.php" data-lang-en="Market" data-lang-hi="बाज़ार">
                            <?php echo $t['market']; ?>
                        </a></li>
                        <li><a href="/education.php" data-lang-en="Education" data-lang-hi="शिक्षा">
                            <?php echo $t['education']; ?>
                        </a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-heading" data-lang-en="Resources" data-lang-hi="संसाधन">
                        <?php echo $t['resources']; ?>
                    </h5>
                    <ul class="list-unstyled">
                        <li><a href="/community.php" data-lang-en="Community" data-lang-hi="समुदाय">
                            <?php echo $t['community']; ?>
                        </a></li>
                        <li><a href="/about.php" data-lang-en="About Us" data-lang-hi="हमारे बारे में">
                            <?php echo $t['about']; ?>
                        </a></li>
                        <li><a href="/contact.php" data-lang-en="Contact" data-lang-hi="संपर्क">
                            <?php echo $t['contact']; ?>
                        </a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5 class="footer-heading" data-lang-en="Connect With Us" data-lang-hi="हमसे जुड़ें">
                        <?php echo $t['connect']; ?>
                    </h5>
                    <div class="social-icons">
                        <a href="https://www.facebook.com" target="_blank" aria-label="Facebook"><i data-feather="facebook"></i></a>
                        <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i data-feather="twitter"></i></a>
                        <a href="https://www.instagram.com" target="_blank" aria-label="Instagram"><i data-feather="instagram"></i></a>
                        <a href="https://www.youtube.com" target="_blank" aria-label="YouTube"><i data-feather="youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="copyright" data-lang-en="&copy; 2025 AgroInnovate. All rights reserved." data-lang-hi="&copy; 2025 एग्रोइनोवेट. सर्वाधिकार सुरक्षित।">
                        <?php echo $t['copyright']; ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="footer-link me-3" data-lang-en="Privacy Policy" data-lang-hi="गोपनीयता नीति">
                        <?php echo $t['privacy_policy']; ?>
                    </a>
                    <a href="#" class="footer-link" data-lang-en="Terms of Service" data-lang-hi="सेवा की शर्तें">
                        <?php echo $t['terms_of_service']; ?>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/language.js"></script>
    
    <script>
        // Initialize Feather icons
        feather.replace();
    </script>
</body>
</html>
