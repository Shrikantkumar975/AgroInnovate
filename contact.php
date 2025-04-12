
<?php
require_once 'includes/session.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - AgroInnovate</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main>
        <section class="contact-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="contact-info">
                            <h2 data-en="Contact Us" data-hi="संपर्क करें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Contact Us' : 'संपर्क करें'; ?>
                            </h2>
                            <p data-en="Have questions or suggestions? Get in touch with us." data-hi="सवाल या सुझाव हैं? हमसे संपर्क करें।">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Have questions or suggestions? Get in touch with us.' : 'सवाल या सुझाव हैं? हमसे संपर्क करें।'; ?>
                            </p>
                            
                            <div class="contact-item">
                                <i data-feather="mail"></i>
                                <div class="contact-text">
                                    <h4>Email</h4>
                                    <p>contact@agroinnovate.com</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <i data-feather="phone"></i>
                                <div class="contact-text">
                                    <h4 data-en="Phone" data-hi="फ़ोन">
                                        <?php echo ($_SESSION['language'] == 'en') ? 'Phone' : 'फ़ोन'; ?>
                                    </h4>
                                    <p>+91 1234567890</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <form id="contact-form" class="contact-form" method="post" action="">
                            <h3 data-en="Send us a Message" data-hi="हमें संदेश भेजें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Send us a Message' : 'हमें संदेश भेजें'; ?>
                            </h3>
                            
                            <div class="form-group">
                                <label for="name" data-en="Your Name" data-hi="आपका नाम">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Your Name' : 'आपका नाम'; ?>
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" data-en="Email Address" data-hi="ईमेल पता">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Email Address' : 'ईमेल पता'; ?>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message" data-en="Message" data-hi="संदेश">
                                    <?php echo ($_SESSION['language'] == 'en') ? 'Message' : 'संदेश'; ?>
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" data-en="Send Message" data-hi="संदेश भेजें">
                                <?php echo ($_SESSION['language'] == 'en') ? 'Send Message' : 'संदेश भेजें'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    <script src="/js/main.js"></script>
    <script src="/js/language.js"></script>
    <script>
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
</body>
</html>
