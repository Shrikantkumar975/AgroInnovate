// Password validation function
function validatePassword(password) {
    const requirements = {
        minLength: password.length >= 8,
        hasUpperCase: /[A-Z]/.test(password),
        hasLowerCase: /[a-z]/.test(password),
        hasNumber: /[0-9]/.test(password),
        hasSpecial: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };

    const messages = [];
    if (!requirements.minLength) messages.push("At least 8 characters");
    if (!requirements.hasUpperCase) messages.push("One uppercase letter");
    if (!requirements.hasLowerCase) messages.push("One lowercase letter");
    if (!requirements.hasNumber) messages.push("One number");
    if (!requirements.hasSpecial) messages.push("One special character");

    return {
        isValid: Object.values(requirements).every(req => req),
        messages: messages
    };
}

// Phone number validation function
function validatePhone(phone) {
    // Remove any non-digit characters
    const cleanPhone = phone.replace(/\D/g, '');
    
    // Check if it's a valid Indian phone number (10 digits, starting with 6-9)
    const phoneRegex = /^[6-9]\d{9}$/;
    
    return {
        isValid: phoneRegex.test(cleanPhone),
        message: phoneRegex.test(cleanPhone) ? 
            "Valid phone number" : 
            "Please enter a valid 10-digit phone number starting with 6-9"
    };
}

// Update password strength indicator
function updatePasswordStrength(password, feedbackElement) {
    const validation = validatePassword(password);
    const strengthBar = document.getElementById('password-strength');
    
    if (password.length === 0) {
        feedbackElement.innerHTML = '';
        strengthBar.style.width = '0%';
        strengthBar.className = 'password-strength-bar';
        return;
    }

    const requirements = validation.messages;
    let strength = 100 - (requirements.length * 20);
    
    strengthBar.style.width = strength + '%';
    
    if (strength <= 20) {
        strengthBar.className = 'password-strength-bar very-weak';
    } else if (strength <= 40) {
        strengthBar.className = 'password-strength-bar weak';
    } else if (strength <= 60) {
        strengthBar.className = 'password-strength-bar medium';
    } else if (strength <= 80) {
        strengthBar.className = 'password-strength-bar strong';
    } else {
        strengthBar.className = 'password-strength-bar very-strong';
    }

    feedbackElement.innerHTML = requirements.length > 0 ? 
        'Missing requirements: ' + requirements.join(', ') :
        'Strong password!';
}

// Real-time validation for registration form
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registration-form');
    if (registerForm) {
        const passwordInput = document.getElementById('register_password');
        const phoneInput = document.getElementById('register_phone');
        const passwordFeedback = document.getElementById('password-feedback');
        const phoneFeedback = document.getElementById('phone-feedback');

        // Add password strength indicator
        if (passwordInput && !document.getElementById('password-strength-container')) {
            const strengthContainer = document.createElement('div');
            strengthContainer.id = 'password-strength-container';
            strengthContainer.innerHTML = `
                <div class="password-strength">
                    <div class="password-strength-bar" id="password-strength"></div>
                </div>
            `;
            passwordInput.parentNode.insertBefore(strengthContainer, passwordInput.nextSibling);
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                updatePasswordStrength(this.value, passwordFeedback);
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                const result = validatePhone(this.value);
                phoneFeedback.textContent = result.message;
                phoneFeedback.className = result.isValid ? 'text-success' : 'text-danger';
            });
        }

        registerForm.addEventListener('submit', function(e) {
            const passwordValidation = validatePassword(passwordInput.value);
            const phoneValidation = validatePhone(phoneInput.value);

            if (!passwordValidation.isValid || !phoneValidation.isValid) {
                e.preventDefault();
                if (!passwordValidation.isValid) {
                    passwordFeedback.textContent = 'Please fix password requirements: ' + 
                        passwordValidation.messages.join(', ');
                    passwordFeedback.className = 'text-danger';
                }
                if (!phoneValidation.isValid) {
                    phoneFeedback.textContent = phoneValidation.message;
                    phoneFeedback.className = 'text-danger';
                }
            }
        });
    }
}); 