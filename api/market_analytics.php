<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'data' => null, 'error' => null];

try {
    switch ($action) {
        case 'search':
            $query = $_GET['query'] ?? '';
            if (empty($query)) {
                throw new Exception('Search query is required');
            }

            $stmt = $pdo->prepare("
                SELECT crop, price, unit, market, state, trend, change_percentage
                FROM market_prices
                WHERE crop LIKE :query
                ORDER BY crop ASC
            ");
            $stmt->execute([':query' => "%$query%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['data'] = $results;
            $response['success'] = true;
            break;

        case 'statistics':
            // Get average, min, max prices for all crops
            $stmt = $pdo->query("
                SELECT 
                    crop,
                    AVG(price) as average_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price,
                    COUNT(DISTINCT market) as market_count,
                    COUNT(DISTINCT state) as state_count
                FROM market_price_history
                WHERE recorded_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY crop
                ORDER BY average_price DESC
            ");
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['data'] = $stats;
            $response['success'] = true;
            break;

        case 'market_comparison':
            $crop = $_GET['crop'] ?? '';
            if (empty($crop)) {
                throw new Exception('Crop parameter is required');
            }

            // Get prices across different markets
            $stmt = $pdo->prepare("
                SELECT 
                    market,
                    state,
                    price,
                    updated_at,
                    trend,
                    change_percentage
                FROM market_prices
                WHERE crop = :crop
                ORDER BY price DESC
            ");
            $stmt->execute([':crop' => $crop]);
            $comparison = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['data'] = $comparison;
            $response['success'] = true;
            break;

        case 'price_trends':
            $crop = $_GET['crop'] ?? '';
            $interval = $_GET['interval'] ?? '30'; // days
            
            if (empty($crop)) {
                throw new Exception('Crop parameter is required');
            }

            // Get daily average prices for trend analysis
            $stmt = $pdo->prepare("
                SELECT 
                    DATE(recorded_date) as date,
                    AVG(price) as average_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price
                FROM market_price_history
                WHERE crop = :crop
                AND recorded_date >= DATE_SUB(CURDATE(), INTERVAL :interval DAY)
                GROUP BY DATE(recorded_date)
                ORDER BY recorded_date ASC
            ");
            $stmt->execute([
                ':crop' => $crop,
                ':interval' => $interval
            ]);
            $trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response['data'] = $trends;
            $response['success'] = true;
            break;

        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response); 