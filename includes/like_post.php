<?php
// Include necessary files
require_once 'db_connect.php';
require_once 'functions.php';
require_once 'session.php';
require_once 'db.php';
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User must be logged in to like posts']);
    exit;
}

// Check if post_id was provided
if (!isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if user has already liked the post
    $check_stmt = $conn->prepare("SELECT id FROM post_likes WHERE user_id = ? AND post_id = ?");
    $check_stmt->bind_param("ii", $user_id, $post_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User has already liked the post - remove the like
        $delete_stmt = $conn->prepare("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $post_id);
        $delete_stmt->execute();

        // Update the likes count in community_posts
        $update_stmt = $conn->prepare("UPDATE community_posts SET likes = GREATEST(likes - 1, 0) WHERE id = ?");
        $update_stmt->bind_param("i", $post_id);
        $update_stmt->execute();

        $action = 'unliked';
    } else {
        // User hasn't liked the post - add the like
        $insert_stmt = $conn->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $post_id);
        $insert_stmt->execute();

        // Update the likes count in community_posts
        $update_stmt = $conn->prepare("UPDATE community_posts SET likes = likes + 1 WHERE id = ?");
        $update_stmt->bind_param("i", $post_id);
        $update_stmt->execute();

        $action = 'liked';
    }

    // Get the updated likes count
    $count_stmt = $conn->prepare("SELECT likes FROM community_posts WHERE id = ?");
    $count_stmt->bind_param("i", $post_id);
    $count_stmt->execute();
    $result = $count_stmt->get_result();
    $row = $result->fetch_assoc();
    $likes_count = $row['likes'];

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => "Post successfully " . $action,
        'action' => $action,
        'likes' => $likes_count
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Error in like_post.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Error updating like status: " . $e->getMessage()
    ]);
} finally {
    // Close all statements
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($delete_stmt)) $delete_stmt->close();
    if (isset($insert_stmt)) $insert_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($count_stmt)) $count_stmt->close();
}
?> 