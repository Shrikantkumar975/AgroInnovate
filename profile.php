<?php
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$successMessage = '';
$errorMessage = '';

// Get user data
$userData = fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $errors = array();
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = 'Name is required.';
    } elseif (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        // Check if email exists for another user
        $existingUser = fetchOne("SELECT * FROM users WHERE email = ? AND id != ?", [$email, $_SESSION['user_id']]);
        
        if ($existingUser) {
            $errors[] = 'Email already in use by another account.';
        }
    }
    
    // Handle password update if requested
    if (!empty($newPassword) || !empty($currentPassword) || !empty($confirmPassword)) {
        // Verify all password fields are provided
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required to change password.';
        } elseif (empty($newPassword)) {
            $errors[] = 'New password is required.';
        } elseif (empty($confirmPassword)) {
            $errors[] = 'Please confirm your new password.';
        } else {
            // Verify current password
            if (!password_verify($currentPassword, $userData['password'])) {
                $errors[] = 'Current password is incorrect.';
            }
            // Verify new password matches confirmation
            elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirmation do not match.';
            }
            else {
                // Validate password strength
                $passwordErrors = [];
                
                if (strlen($newPassword) < 8) {
                    $passwordErrors[] = 'at least 8 characters long';
                }
                if (!preg_match('/[A-Z]/', $newPassword)) {
                    $passwordErrors[] = 'one uppercase letter';
                }
                if (!preg_match('/[a-z]/', $newPassword)) {
                    $passwordErrors[] = 'one lowercase letter';
                }
                if (!preg_match('/[0-9]/', $newPassword)) {
                    $passwordErrors[] = 'one number';
                }
                if (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
                    $passwordErrors[] = 'one special character';
                }
                if (in_array(strtolower($newPassword), ['123456', 'password', 'qwerty', 'admin', 'letmein', 'welcome', 'monkey', 'dragon', '12345678', '123456789', '1234567', '1234567890'])) {
                    $passwordErrors[] = 'not be a common password';
                }
                
                if (!empty($passwordErrors)) {
                    $errors[] = 'Password must contain ' . implode(', ', $passwordErrors) . '.';
                } else {
                    // All validations passed, update password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                }
            }
        }
    }
    
    // If there are no errors, proceed with the update
    if (empty($errors)) {
        if (!empty($hashedPassword)) {
            $sql = "UPDATE users SET name=?, email=?, password=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $name, $email, $hashedPassword, $_SESSION['user_id']);
        } else {
            $sql = "UPDATE users SET name=?, email=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $_SESSION['user_id']);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_msg'] = "Profile updated successfully!";
            header('location: profile.php');
            exit();
        } else {
            $errors[] = "Error updating profile: " . mysqli_error($conn);
        }
    }
    
    if (!empty($errors)) {
        $errorMessage = implode('<br>', $errors);
    }
}

// ... (PHP logic stays, we only replace the HTML part below)
?>
<?php include 'includes/header.php'; ?>

<main>
    <div class="container profile-container" data-aos="fade-up">
        <div class="profile-header">
            <div class="profile-header-avatar">
                <?php echo strtoupper(substr($userData['name'], 0, 1)); ?>
            </div>
            <div class="profile-name">
                <h2><?php echo htmlspecialchars($userData['name']); ?></h2>
                <div class="profile-email"><?php echo htmlspecialchars($userData['email']); ?></div>
            </div>
        </div>
        
        <div class="profile-card">
            <h3>Edit Profile</h3>
            
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?></div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            
            <form method="post" action="" id="profileForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                </div>
                
                <h4 class="mt-4 mb-3">Change Password (optional)</h4>
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password">
                    <small class="text-muted">Required to change password</small>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                    <div class="password-requirements">
                        Password must contain:
                        <ul>
                            <li>At least 8 characters</li>
                            <li>At least one uppercase letter</li>
                            <li>At least one lowercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const currentPassword = document.getElementById('current_password');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');

    form.addEventListener('submit', function(e) {
        // Only validate if any password field is filled
        if (currentPassword.value || newPassword.value || confirmPassword.value) {
            const errors = [];

            // Check if current password is provided
            if (!currentPassword.value) {
                errors.push('Current password is required to change password');
            }

            // Check if new password is provided
            if (!newPassword.value) {
                errors.push('New password is required');
            }

            // Check if confirmation matches
            if (newPassword.value !== confirmPassword.value) {
                errors.push('New password and confirmation do not match');
            }

            // Validate password strength
            if (newPassword.value) {
                if (newPassword.value.length < 8) {
                    errors.push('Password must be at least 8 characters long');
                }
                if (!/[A-Z]/.test(newPassword.value)) {
                    errors.push('Password must contain at least one uppercase letter');
                }
                if (!/[a-z]/.test(newPassword.value)) {
                    errors.push('Password must contain at least one lowercase letter');
                }
                if (!/[0-9]/.test(newPassword.value)) {
                    errors.push('Password must contain at least one number');
                }
                if (!/[^A-Za-z0-9]/.test(newPassword.value)) {
                    errors.push('Password must contain at least one special character');
                }
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        }
    });
});
</script> 