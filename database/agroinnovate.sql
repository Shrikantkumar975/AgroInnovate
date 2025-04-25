-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `agroinnovate`;
USE `agroinnovate`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes
CREATE INDEX idx_email ON users(email);

-- Add any initial data if needed
-- INSERT INTO users (name, email, password, phone) VALUES
-- ('Admin User', 'admin@example.com', '$2y$10$yourhashedpassword', '1234567890'); 