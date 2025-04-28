<?php
// Include necessary files
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Get pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Calculate offset
$offset = ($page - 1) * $limit;

try {
    $posts = getPosts($page, $limit);
    echo json_encode(['success' => true, 'data' => $posts]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 