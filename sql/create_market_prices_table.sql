-- Create market_prices table
CREATE TABLE IF NOT EXISTS `market_prices` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create market_price_history table
CREATE TABLE IF NOT EXISTS `market_price_history` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 