<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/EmailValidator.php';
session_start();

// Define language-specific content
$content = [
    'en' => [
        'title' => 'Register',
        'name_label' => 'Name',
        'email_label' => 'Email',
        'password_label' => 'Password',
        'confirm_password_label' => 'Confirm Password',
        'register_button' => 'Register',
        'login_text' => 'Already have an account?',
        'login_link' => 'Login here',
        'success_message' => 'Registration successful! Please check your email to verify your account.',
        'email_exists' => 'Email already registered',
        'password_length' => 'Password must be at least 8 characters long',
        'password_mismatch' => 'Passwords do not match',
        'registration_failed' => 'Registration failed: '
    ],
    'hi' => [
        'title' => 'पंजीकरण',
        'name_label' => 'नाम',
        'email_label' => 'ईमेल',
        'password_label' => 'पासवर्ड',
        'confirm_password_label' => 'पासवर्ड की पुष्टि करें',
        'register_button' => 'पंजीकरण करें',
        'login_text' => 'क्या आपका पहले से एक खाता है?',
        'login_link' => 'यहां लॉगिन करें',
        'success_message' => 'पंजीकरण सफल! कृपया अपना खाता सत्यापित करने के लिए अपना ईमेल जांचें।',
        'email_exists' => 'ईमेल पहले से पंजीकृत है',
        'password_length' => 'पासवर्ड कम से कम 8 अक्षर लंबा होना चाहिए',
        'password_mismatch' => 'पासवर्ड मेल नहीं खाते',
        'registration_failed' => 'पंजीकरण विफल: '
    ]
];

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
$t = $content[$lang];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate email
    $emailValidator = new EmailValidator($email);
    $emailValidation = $emailValidator->validate();
    
    if (!$emailValidation['valid']) {
        $errors = array_merge($errors, $emailValidation['errors']);
    }
    
    // Check if email already exists
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = $t['email_exists'];
    }
    
    // Validate password
    if (strlen($password) < 8) {
        $errors[] = $t['password_length'];
    }
    if ($password !== $confirm_password) {
        $errors[] = $t['password_mismatch'];
    }
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, email_verified) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            $stmt->execute();
            $userId = $conn->insert_id;
            
            // Generate verification token
            $token = $emailValidator->generateVerificationToken();
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            // Store verification token (singular table, no expiry column)
            $stmt = $conn->prepare("INSERT INTO email_verification (user_id, token) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $token);
            $stmt->execute();
            
            // Send verification email
            if ($emailValidator->sendVerificationEmail($token)) {
                $conn->commit();
                $_SESSION['message'] = $t['success_message'];
                $_SESSION['message_type'] = "success";
                header('Location: login.php');
                exit;
            } else {
                throw new Exception($t['registration_failed']);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = $t['registration_failed'] . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = ['name' => $name, 'email' => $email];
        header('Location: register.php');
        exit;
    }
}

// Display registration form
include_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0" data-lang-en="Register" data-lang-hi="पंजीकरण"><?php echo $t['title']; ?></h4>
                </div>
                <div class="card-body">
                    <?php
                    if (isset($_SESSION['errors'])) {
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<div class="alert alert-danger">' . $error . '</div>';
                        }
                        unset($_SESSION['errors']);
                    }
                    ?>
                    
                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label for="name" class="form-label" data-lang-en="Name" data-lang-hi="नाम"><?php echo $t['name_label']; ?></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($_SESSION['form_data']['name']) ? $_SESSION['form_data']['name'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label" data-lang-en="Email" data-lang-hi="ईमेल"><?php echo $t['email_label']; ?></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>" 
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label" data-lang-en="Password" data-lang-hi="पासवर्ड"><?php echo $t['password_label']; ?></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label" data-lang-en="Confirm Password" data-lang-hi="पासवर्ड की पुष्टि करें"><?php echo $t['confirm_password_label']; ?></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-success" data-lang-en="Register" data-lang-hi="पंजीकरण करें"><?php echo $t['register_button']; ?></button>
                    </form>
                    
                    <div class="mt-3">
                        <span data-lang-en="Already have an account?" data-lang-hi="क्या आपका पहले से एक खाता है?"><?php echo $t['login_text']; ?></span>
                        <a href="login.php" data-lang-en="Login here" data-lang-hi="यहां लॉगिन करें"><?php echo $t['login_link']; ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
unset($_SESSION['form_data']);
include_once 'includes/footer.php';
?> 