<?php
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user data
$user = fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

include_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Dashboard Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Dashboard Menu</h5>
                    <div class="list-group">
                        <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                        <a href="#posts" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-file-text me-2"></i> My Posts
                        </a>
                        <a href="#settings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Section -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Edit Profile</h3>
                            <form action="includes/update_profile.php" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                                <button type="submit" class="btn btn-success">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Posts Section -->
                <div class="tab-pane fade" id="posts">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title mb-4">My Posts</h3>
                            <!-- Add posts content here -->
                            <div class="posts-container">
                                <!-- Posts will be loaded dynamically -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="tab-pane fade" id="settings">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title mb-4">Settings</h3>
                            <!-- Add settings content here -->
                            <form action="includes/update_settings.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label d-block">Notification Preferences</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="emailNotifications" name="notifications[]" value="email">
                                        <label class="form-check-label" for="emailNotifications">Email Notifications</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="smsNotifications" name="notifications[]" value="sms">
                                        <label class="form-check-label" for="smsNotifications">SMS Notifications</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Privacy Settings</label>
                                    <select class="form-select" name="privacy">
                                        <option value="public">Public Profile</option>
                                        <option value="private">Private Profile</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>