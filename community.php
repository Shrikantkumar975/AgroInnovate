<?php
session_start();
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'includes/db_connect.php';

// Redirect if not logged in with message
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to access the community section.";
    $_SESSION['message_type'] = "danger";
    header('Location: login.php');
    exit;
}

// Get user data
$user = fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $location = trim($_POST['location']);

    // Handle image upload
    $image_path = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']); // Get MIME type based on file content

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = 'uploads/community/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            } else {
                $_SESSION['message'] = "Error uploading image.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.";
            $_SESSION['message_type'] = "warning";
        }
    }

    // Insert post into database
    if (!isset($_SESSION['message'])) {
        $stmt = $pdo->prepare("INSERT INTO community_posts (user_id, name, title, content, location, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $user['name'], $title, $content, $location, $image_path]);
        // Redirect to prevent form resubmission
        header('Location: community.php');
        exit;
    }
}

// Fetch existing posts with like status for current user
$stmt = $pdo->prepare("
    SELECT
        cp.*,
        u.name as username,
        CASE WHEN pl.id IS NOT NULL THEN 1 ELSE 0 END as user_liked,
        (SELECT COUNT(*) FROM post_likes WHERE post_id = cp.id) as likes
    FROM community_posts cp
    LEFT JOIN users u ON cp.user_id = u.id
    LEFT JOIN post_likes pl ON cp.id = pl.post_id AND pl.user_id = ?
    ORDER BY cp.created_at DESC
");
$stmt->execute([$_SESSION['user_id'] ?? null]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Now include the header which will start HTML output
include_once 'includes/header.php';
?>

<div class="container py-5" data-aos="fade-up">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">Community Feed</h3>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="community.php" method="POST" enctype="multipart/form-data" class="mb-4" id="newPostForm">
                        <?php 
                        include 'includes/message.php'; 
                        ?>
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image (optional)</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text text-muted">Only JPEG, PNG, GIF, and WebP images are allowed.</div>
                        </div>
                        <button type="submit" class="btn btn-success">Post</button>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-info">
                        Please <a href="login.php" class="alert-link">login</a> to create a post.
                    </div>
                    <?php endif; ?>

                    <div class="posts-container" style="max-height: 600px; overflow-y: auto;">
                        <?php foreach ($posts as $post): ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                        <small class="text-muted">(@user<?php echo htmlspecialchars($post['user_id']); ?>)</small>
                                        <small class="text-muted ms-2"><?php echo htmlspecialchars($post['location']); ?></small>
                                    </div>
                                    <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($post['created_at'])); ?></small>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                    <?php if ($post['image_path']): ?>
                                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="w-50 img-fluid rounded" alt="Post image">
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <div class="likes-section">
                                        <button class="btn btn-sm <?php echo $post['user_liked'] ? 'btn-success' : 'btn-outline-success'; ?> like-button" data-post-id="<?php echo $post['id']; ?>">
                                            <i class="bi <?php echo $post['user_liked'] ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                            <span class="likes-count"><?php echo $post['likes'] ?? 0; ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Community Stats</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Members:</span>
                        <span class="fw-bold"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Posts Today:</span>
                        <span class="fw-bold"></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Active Discussions:</span>
                        <span class="fw-bold"></span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Popular Topics</h5>
                    <div class="popular-topics">
                        <span class="badge bg-success me-2 mb-2">#Farming</span>
                        <span class="badge bg-success me-2 mb-2">#Sustainability</span>
                        <span class="badge bg-success me-2 mb-2">#Innovation</span>
                        <span class="badge bg-success me-2 mb-2">#AgTech</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Community Guidelines</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Be respectful and kind</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Share knowledge</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>No spam or self-promotion</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Stay on topic</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.posts-container::-webkit-scrollbar {
    width: 8px;
}

.posts-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.posts-container::-webkit-scrollbar-thumb {
    background: #4CAF50;
    border-radius: 4px;
}

.posts-container::-webkit-scrollbar-thumb:hover {
    background: #45a049;
}

.like-button {
    transition: all 0.2s ease;
}

.like-button:hover {
    transform: scale(1.05);
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like button functionality
    const likeButtons = document.querySelectorAll('.like-button');
    likeButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
                window.location.href = 'login.php';
                return;
            }

            const postId = this.dataset.postId;
            const likesCount = this.querySelector('.likes-count');
            const heartIcon = this.querySelector('.bi');

            try {
                const response = await fetch('includes/like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        post_id: postId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update likes count
                    likesCount.textContent = data.likes;

                    // Toggle button appearance based on action
                    if (data.action === 'liked') {
                        this.classList.remove('btn-outline-success');
                        this.classList.add('btn-success');
                        heartIcon.classList.remove('bi-heart');
                        heartIcon.classList.add('bi-heart-fill');
                    } else {
                        this.classList.add('btn-outline-success');
                        this.classList.remove('btn-success');
                        heartIcon.classList.add('bi-heart');
                        heartIcon.classList.remove('bi-heart-fill');
                    }
                } else {
                    // Show error message if provided
                    if (data.message) {
                        alert(data.message);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                // Show a user-friendly error message
                alert('There was an error updating the like status. Please try again.');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>