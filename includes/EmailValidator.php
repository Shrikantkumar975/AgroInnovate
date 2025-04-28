<?php
class EmailValidator {
    private $email;
    
    public function __construct($email) {
        $this->email = $email;
    }
    
    /**
     * Performs basic syntax validation
     */
    public function isValidSyntax() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Checks if the domain has valid MX records
     */
    public function hasMXRecord() {
        $domain = substr(strrchr($this->email, "@"), 1);
        return checkdnsrr($domain, 'MX');
    }
    
    /**
     * Checks if email is disposable
     */
    public function isDisposable() {
        $disposableDomains = [
            'tempmail.com', 'throwawaymail.com', 'mailinator.com',
            'temp-mail.org', 'guerrillamail.com', '10minutemail.com'
            // Add more disposable email domains as needed
        ];
        
        $domain = substr(strrchr($this->email, "@"), 1);
        return in_array(strtolower($domain), $disposableDomains);
    }
    
    /**
     * Generates verification token
     */
    public function generateVerificationToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Sends verification email
     */
    public function sendVerificationEmail($token) {
        require_once 'email_config.php';
        
        try {
            $mail = configureEmail();
            $mail->addAddress($this->email);
            $mail->Subject = "Verify Your Email Address - AgroInnovate";
            
            // HTML Email Body
            $mail->isHTML(true);
            $mail->Body = "
                <html>
                <body style='font-family: Arial, sans-serif;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #2E7D32;'>Welcome to AgroInnovate!</h2>
                        <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='https://{$_SERVER['HTTP_HOST']}/verify.php?token={$token}' 
                               style='background-color: #2E7D32; color: white; padding: 12px 30px; 
                                      text-decoration: none; border-radius: 5px;'>
                                Verify Email Address
                            </a>
                        </div>
                        <p>Or copy and paste this link in your browser:</p>
                        <p style='word-break: break-all;'>
                            https://{$_SERVER['HTTP_HOST']}/verify.php?token={$token}
                        </p>
                        <p>This link will expire in 24 hours.</p>
                        <hr style='margin: 30px 0;'>
                        <p style='color: #666; font-size: 12px;'>
                            If you didn't create an account with AgroInnovate, please ignore this email.
                        </p>
                    </div>
                </body>
                </html>
            ";
            
            // Plain text alternative
            $mail->AltBody = "Welcome to AgroInnovate!\n\n" .
                            "Please verify your email address by clicking this link:\n" .
                            "https://{$_SERVER['HTTP_HOST']}/verify.php?token={$token}\n\n" .
                            "This link will expire in 24 hours.\n\n" .
                            "If you didn't create an account with AgroInnovate, please ignore this email.";
            
            return $mail->send();
        } catch (Exception $e) {
            error_log("Error sending verification email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Performs all validations
     */
    public function validate() {
        $result = [
            'valid' => true,
            'errors' => []
        ];
        
        // Check basic syntax
        if (!$this->isValidSyntax()) {
            $result['valid'] = false;
            $result['errors'][] = 'Invalid email format';
        }
        
        // Check MX records
        if (!$this->hasMXRecord()) {
            $result['valid'] = false;
            $result['errors'][] = 'Invalid email domain';
        }
        
        // Check if disposable
        if ($this->isDisposable()) {
            $result['valid'] = false;
            $result['errors'][] = 'Disposable email addresses are not allowed';
        }
        
        return $result;
    }
}
?> 