<?php
require_once 'includes/db_connect.php';

try {
    // Check if the table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'community_posts'");
    $tableExists = $stmt->rowCount() > 0;
    
    echo "Table exists: " . ($tableExists ? "Yes" : "No") . "\n\n";
    
    if ($tableExists) {
        // Get all posts
        $stmt = $pdo->query("SELECT * FROM community_posts");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Number of posts found: " . count($posts) . "\n\n";
        
        if (count($posts) > 0) {
            echo "Posts in database:\n";
            foreach ($posts as $post) {
                echo "ID: " . $post['id'] . "\n";
                echo "Name: " . $post['name'] . "\n";
                echo "Title: " . $post['title'] . "\n";
                echo "Content: " . $post['content'] . "\n";
                echo "Location: " . $post['location'] . "\n";
                echo "Created at: " . $post['created_at'] . "\n";
                echo "Likes: " . $post['likes'] . "\n";
                echo "-------------------\n";
            }
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
} 