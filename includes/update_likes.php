<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = (int)$_POST['post_id'];
    
    try {
        // Update likes count
        $stmt = $pdo->prepare("UPDATE community_posts SET likes = likes + 1 WHERE id = ?");
        $stmt->execute([$postId]);
        
        // Get updated likes count
        $stmt = $pdo->prepare("SELECT likes FROM community_posts WHERE id = ?");
        $stmt->execute([$postId]);
        $result = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'likes' => $result['likes']
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request'
    ]);
} 