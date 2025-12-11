<?php
// setup_full_db.php
// A single script to create ALL required tables for the AgroInnovate application.

require_once 'includes/config.php';
require_once 'includes/db.php';

try {
    echo "Starting database setup...\n";
    echo "Connected to database: " . $db_name . "\n";

    // 1. Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL UNIQUE,
        `password` varchar(255) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `is_verified` tinyint(1) NOT NULL DEFAULT 0,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: users\n";

    // 2. Email Verification Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `email_verification` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `token` varchar(64) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: email_verification\n";

    // 3. Password Resets Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `email` VARCHAR(255) NOT NULL,
        `token` VARCHAR(255) NOT NULL,
        `expiry` DATETIME NOT NULL,
        `used` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `token_index` (`token`),
        INDEX `email_index` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: password_resets\n";

    // 4. Contact Submissions Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_submissions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `created_at` datetime NOT NULL,
        `is_read` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: contact_submissions\n";

    // 5. Community Posts Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `community_posts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `name` varchar(100) NOT NULL,
        `title` varchar(255) NOT NULL,
        `content` text NOT NULL,
        `location` varchar(100) NOT NULL,
        `image_path` varchar(255) DEFAULT NULL,
        `likes` int(11) NOT NULL DEFAULT 0,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: community_posts\n";

    // 6. Post Likes Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `post_likes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `post_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_like` (`post_id`, `user_id`),
        FOREIGN KEY (`post_id`) REFERENCES `community_posts`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: post_likes\n";

    // 7. Market Prices Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `market_prices` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `crop` varchar(100) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        `unit` varchar(20) NOT NULL DEFAULT 'quintal',
        `market` varchar(100) NOT NULL,
        `state` varchar(100) NOT NULL,
        `trend` enum('up','down','stable') NOT NULL,
        `change_percentage` decimal(5,2) NOT NULL,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `crop_index` (`crop`),
        KEY `market_index` (`market`),
        KEY `state_index` (`state`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: market_prices\n";

    // 8. Market Price History Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `market_price_history` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `crop` varchar(100) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        `unit` varchar(20) NOT NULL DEFAULT 'quintal',
        `market` varchar(100) NOT NULL,
        `state` varchar(100) NOT NULL,
        `recorded_date` date NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `crop_date_index` (`crop`, `recorded_date`),
        KEY `market_date_index` (`market`, `recorded_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Checked/Created table: market_price_history\n";

    echo "\nDatabase setup completed successfully!\n";

} catch (PDOException $e) {
    die("\nError during database setup: " . $e->getMessage() . "\n");
}
