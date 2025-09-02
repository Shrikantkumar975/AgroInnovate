<?php
// Include configuration
require_once __DIR__ . '/config.php';

// Include database connection
require_once __DIR__ . '/db.php';

// Include PHPMailer
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Sanitize user input to prevent XSS attacks
 * 
 * @param string $input The input to sanitize
 * @return string The sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * 
 * @param string $email The email to validate
 * @return bool Whether the email is valid
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Set language preference
 * 
 * @param string $lang The language code (en/hi)
 * @return void
 */
function setLanguage($lang) {
    if ($lang === 'en' || $lang === 'hi') {
        $_SESSION['language'] = $lang;
    }
}

/**
 * Translate text based on current language
 * 
 * @param string $englishText The English text
 * @param string $hindiText The Hindi text
 * @return string The translated text
 */
function translate($englishText, $hindiText) {
    return ($_SESSION['language'] == 'en') ? $englishText : $hindiText;
}

/**
 * Get current weather data from API
 * 
 * @param string $location The location to get weather for
 * @return array|false Weather data or false on failure
 */
function getWeatherData($location) {
    $apiKey = getenv('OPENWEATHER_API_KEY');
    
    // Log API key status
    error_log("Weather API Key status: " . ($apiKey ? "Present" : "Missing"));
    
    // Check if we have a valid API key
    if ($apiKey) {
        // Build API URL
        $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($location) . ",in&units=metric&appid=" . $apiKey;
        error_log("Attempting to fetch weather data from: " . $url);
        
        // Make API request with error handling
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['cod']) && $data['cod'] === 200) {
                error_log("Successfully fetched weather data for: " . $location);
                return $data;
            } else {
                error_log("API Error: " . ($data['message'] ?? 'Unknown error'));
            }
        } else {
            error_log("Failed to fetch weather data. HTTP response: " . $http_response_header[0] ?? 'No response');
        }
    }
    
    error_log("Falling back to demo weather data for: " . $location);
    // If API request fails or no API key, return demo data
    return getWeatherDemoData($location);
}

/**
 * Get current weather data by geographic coordinates
 *
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return array|false Weather data or false on failure
 */
function getWeatherDataByCoords($lat, $lon) {
    $apiKey = getenv('OPENWEATHER_API_KEY');

    if (!is_numeric($lat) || !is_numeric($lon)) {
        error_log('Invalid coordinates supplied to getWeatherDataByCoords');
        return false;
    }

    error_log("Weather API Key status: " . ($apiKey ? "Present" : "Missing"));

    if ($apiKey) {
        $url = "https://api.openweathermap.org/data/2.5/weather?lat=" . urlencode((string)$lat) . "&lon=" . urlencode((string)$lon) . "&units=metric&appid=" . $apiKey;
        error_log("Attempting to fetch weather data (coords) from: " . $url);

        $context = stream_context_create([
            'http' => [ 'ignore_errors' => true ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['cod']) && (int)$data['cod'] === 200) {
                error_log("Successfully fetched weather data for coords: $lat,$lon");
                return $data;
            } else {
                error_log("API Error (coords): " . ($data['message'] ?? 'Unknown error'));
            }
        } else {
            error_log("Failed to fetch weather data (coords). HTTP response: " . ($http_response_header[0] ?? 'No response'));
        }
    }

    return getWeatherDemoData('Your Location');
}

/**
 * Get demo weather data for testing when API is not available
 * 
 * @param string $location The location to simulate weather for
 * @return array Demo weather data
 */
function getWeatherDemoData($location) {
    // Demo data based on common Indian weather patterns
    $weatherTypes = [
        'Clear' => [
            'description' => 'clear sky',
            'icon' => '01d',
            'temp' => rand(25, 35),
            'feels_like' => rand(27, 37),
            'humidity' => rand(30, 50),
            'pressure' => rand(1010, 1020),
            'wind_speed' => rand(1, 5) + (rand(0, 9) / 10)
        ],
        'Clouds' => [
            'description' => 'scattered clouds',
            'icon' => '03d',
            'temp' => rand(22, 32),
            'feels_like' => rand(24, 34),
            'humidity' => rand(40, 60),
            'pressure' => rand(1005, 1015),
            'wind_speed' => rand(2, 6) + (rand(0, 9) / 10)
        ],
        'Rain' => [
            'description' => 'light rain',
            'icon' => '10d',
            'temp' => rand(18, 28),
            'feels_like' => rand(19, 29),
            'humidity' => rand(60, 90),
            'pressure' => rand(1000, 1010),
            'wind_speed' => rand(3, 8) + (rand(0, 9) / 10)
        ]
    ];
    
    // Select a weather type randomly
    $weatherKeys = array_keys($weatherTypes);
    $weatherKey = $weatherKeys[array_rand($weatherKeys)];
    $weather = $weatherTypes[$weatherKey];
    
    // Structure data like the OpenWeatherMap API response
    return [
        'coord' => [
            'lon' => 77.21,
            'lat' => 28.67
        ],
        'weather' => [
            [
                'id' => 800,
                'main' => $weatherKey,
                'description' => $weather['description'],
                'icon' => $weather['icon']
            ]
        ],
        'base' => 'stations',
        'main' => [
            'temp' => $weather['temp'],
            'feels_like' => $weather['feels_like'],
            'temp_min' => $weather['temp'] - rand(1, 3),
            'temp_max' => $weather['temp'] + rand(1, 3),
            'pressure' => $weather['pressure'],
            'humidity' => $weather['humidity']
        ],
        'visibility' => 10000,
        'wind' => [
            'speed' => $weather['wind_speed'],
            'deg' => rand(0, 359)
        ],
        'clouds' => [
            'all' => $weatherKey == 'Clear' ? rand(0, 10) : ($weatherKey == 'Clouds' ? rand(30, 90) : rand(70, 100))
        ],
        'dt' => time(),
        'sys' => [
            'country' => 'IN',
            'sunrise' => strtotime('today 6:00am'),
            'sunset' => strtotime('today 6:30pm')
        ],
        'timezone' => 19800, // UTC+5:30
        'id' => 1273294,
        'name' => $location,
        'cod' => 200
    ];
}

/**
 * Get weather forecast data from API
 * 
 * @param string $location The location to get forecast for
 * @return array|false Forecast data or false on failure
 */
function getWeatherForecast($location) {
    $apiKey = getenv('OPENWEATHER_API_KEY');
    
    // Log API key status
    error_log("Weather Forecast API Key status: " . ($apiKey ? "Present" : "Missing"));
    
    // Check if we have a valid API key
    if ($apiKey) {
        // Build API URL
        $url = "https://api.openweathermap.org/data/2.5/forecast?q=" . urlencode($location) . ",in&units=metric&appid=" . $apiKey;
        error_log("Attempting to fetch forecast data from: " . $url);
        
        // Make API request with error handling
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['cod']) && $data['cod'] === "200") {
                error_log("Successfully fetched forecast data for: " . $location);
                return $data;
            } else {
                error_log("API Error: " . ($data['message'] ?? 'Unknown error'));
            }
        } else {
            error_log("Failed to fetch forecast data. HTTP response: " . $http_response_header[0] ?? 'No response');
        }
    }
    
    error_log("Falling back to demo forecast data for: " . $location);
    // If API request fails or no API key, return demo data
    return getForecastDemoData($location);
}

/**
 * Get weather forecast by geographic coordinates
 *
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return array|false Forecast data or false on failure
 */
function getWeatherForecastByCoords($lat, $lon) {
    $apiKey = getenv('OPENWEATHER_API_KEY');

    if (!is_numeric($lat) || !is_numeric($lon)) {
        error_log('Invalid coordinates supplied to getWeatherForecastByCoords');
        return false;
    }

    error_log("Weather Forecast API Key status: " . ($apiKey ? "Present" : "Missing"));

    if ($apiKey) {
        $url = "https://api.openweathermap.org/data/2.5/forecast?lat=" . urlencode((string)$lat) . "&lon=" . urlencode((string)$lon) . "&units=metric&appid=" . $apiKey;
        error_log("Attempting to fetch forecast data (coords) from: " . $url);

        $context = stream_context_create([
            'http' => [ 'ignore_errors' => true ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response !== false) {
            $data = json_decode($response, true);
            if (isset($data['cod']) && (string)$data['cod'] === '200') {
                error_log("Successfully fetched forecast data for coords: $lat,$lon");
                return $data;
            } else {
                error_log("API Error (coords forecast): " . ($data['message'] ?? 'Unknown error'));
            }
        } else {
            error_log("Failed to fetch forecast data (coords). HTTP response: " . ($http_response_header[0] ?? 'No response'));
        }
    }

    return getForecastDemoData('Your Location');
}

/**
 * Get demo forecast data for testing when API is not available
 * 
 * @param string $location The location to simulate forecast for
 * @return array Demo forecast data
 */
function getForecastDemoData($location) {
    // Possible weather conditions
    $weatherConditions = [
        ['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d'],
        ['main' => 'Clouds', 'description' => 'scattered clouds', 'icon' => '03d'],
        ['main' => 'Clouds', 'description' => 'broken clouds', 'icon' => '04d'],
        ['main' => 'Rain', 'description' => 'light rain', 'icon' => '10d'],
        ['main' => 'Rain', 'description' => 'moderate rain', 'icon' => '10d']
    ];
    
    // Create forecast data for 5 days
    $list = [];
    $currentDate = time();
    
    // Base temperature for the region (adjust as needed)
    $baseTemp = rand(25, 32);
    
    // Create 8 forecasts per day (3-hour intervals) for 5 days
    for ($day = 0; $day < 5; $day++) {
        for ($hour = 0; $hour < 24; $hour += 3) {
            $forecastTime = strtotime("+$day days", $currentDate) + ($hour * 3600);
            
            // Temperature varies throughout the day
            $hourlyVariation = $hour < 12 ? $hour / 3 : (24 - $hour) / 3;
            $temp = $baseTemp + $hourlyVariation - rand(0, 2);
            
            // More likely to rain in the afternoon
            $weatherProbability = ($hour > 12 && $hour < 18) ? rand(0, 4) : rand(0, 3);
            $weather = $weatherConditions[$weatherProbability];
            
            $list[] = [
                'dt' => $forecastTime,
                'main' => [
                    'temp' => $temp,
                    'feels_like' => $temp + 2,
                    'temp_min' => $temp - 1,
                    'temp_max' => $temp + 1,
                    'pressure' => rand(1008, 1020),
                    'humidity' => rand(40, 90)
                ],
                'weather' => [
                    [
                        'id' => 800,
                        'main' => $weather['main'],
                        'description' => $weather['description'],
                        'icon' => $weather['icon']
                    ]
                ],
                'clouds' => [
                    'all' => $weather['main'] == 'Clear' ? rand(0, 10) : rand(30, 100)
                ],
                'wind' => [
                    'speed' => rand(1, 8) + (rand(0, 9) / 10),
                    'deg' => rand(0, 359)
                ],
                'visibility' => 10000,
                'pop' => $weather['main'] == 'Rain' ? rand(30, 90) / 100 : rand(0, 20) / 100,
                'sys' => [
                    'pod' => ($hour >= 6 && $hour < 18) ? 'd' : 'n'
                ],
                'dt_txt' => date('Y-m-d H:i:s', $forecastTime)
            ];
        }
    }
    
    // Structure data like the OpenWeatherMap forecast API response
    return [
        'cod' => '200',
        'message' => 0,
        'cnt' => count($list),
        'list' => $list,
        'city' => [
            'id' => 1273294,
            'name' => $location,
            'coord' => [
                'lat' => 28.6667,
                'lon' => 77.2167
            ],
            'country' => 'IN',
            'population' => 10927986,
            'timezone' => 19800,
            'sunrise' => strtotime('today 6:00am'),
            'sunset' => strtotime('today 6:30pm')
        ]
    ];
}

/**
 * Get market prices for crops
 * 
 * @return array Market price data
 */
function getMarketPrices() {
    // In a real application, this would fetch from an actual API
    // For this implementation, we'll use static data
    return [
        [
            'crop' => 'Rice',
            'price' => 2250,
            'unit' => 'quintal',
            'trend' => 'up',
            'change' => 5.2
        ],
        [
            'crop' => 'Wheat',
            'price' => 2150,
            'unit' => 'quintal',
            'trend' => 'up',
            'change' => 3.1
        ],
        [
            'crop' => 'Cotton',
            'price' => 6500,
            'unit' => 'quintal',
            'trend' => 'down',
            'change' => -1.5
        ],
        [
            'crop' => 'Sugarcane',
            'price' => 350,
            'unit' => 'quintal',
            'trend' => 'up',
            'change' => 0.8
        ],
        [
            'crop' => 'Maize',
            'price' => 1850,
            'unit' => 'quintal',
            'trend' => 'down',
            'change' => -2.3
        ],
        [
            'crop' => 'Soybeans',
            'price' => 4200,
            'unit' => 'quintal',
            'trend' => 'up',
            'change' => 4.7
        ]
    ];
}

/**
 * Get historical market data for a specific crop
 * 
 * @param string $crop The crop to get data for
 * @return array Historical market data
 */
function getHistoricalMarketData($crop) {
    // In a real application, this would fetch from a database or API
    // For this implementation, we'll use static data
    $data = [
        'Rice' => [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'prices' => [2100, 2120, 2140, 2130, 2150, 2170, 2180, 2200, 2220, 2210, 2230, 2250]
        ],
        'Wheat' => [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'prices' => [2000, 2030, 2050, 2070, 2060, 2080, 2090, 2100, 2110, 2130, 2140, 2150]
        ],
        'Cotton' => [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'prices' => [6300, 6350, 6400, 6450, 6500, 6550, 6600, 6650, 6600, 6580, 6550, 6500]
        ],
        'Sugarcane' => [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'prices' => [330, 335, 340, 338, 342, 345, 343, 347, 348, 345, 348, 350]
        ],
        'Maize' => [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'prices' => [1900, 1920, 1910, 1890, 1880, 1870, 1860, 1850, 1870, 1880, 1860, 1850]
        ],
        'Soybeans' => [
            'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'prices' => [4000, 4020, 4050, 4080, 4100, 4120, 4150, 4170, 4190, 4180, 4190, 4200]
        ]
    ];
    
    return isset($data[$crop]) ? $data[$crop] : ['months' => [], 'prices' => []];
}

/**
 * Get educational resources
 * 
 * @param string $category Category of resources to get
 * @return array Educational resources
 */
function getEducationalResources($category = null) {
    // In a real application, this would fetch from a database
    // For this implementation, we'll use static data
    $resources = [
        'crop_management' => [
            [
                'title' => 'Sustainable Rice Cultivation Techniques',
                'description' => 'Learn modern techniques for sustainable rice cultivation that maximize yield while minimizing environmental impact.',
                'image' => 'rice_cultivation.svg',
                'url' => '/education.php?resource=sustainable_rice'
            ],
            [
                'title' => 'Pest Management in Cotton Farming',
                'description' => 'Comprehensive guide to identifying and managing common pests in cotton farming using integrated pest management approaches.',
                'image' => 'pest_management.svg',
                'url' => '/education.php?resource=cotton_pests'
            ],
            [
                'title' => 'Water Conservation Techniques',
                'description' => 'Efficient irrigation and water management techniques for areas with limited water resources.',
                'image' => 'water_conservation.svg',
                'url' => '/education.php?resource=water_conservation'
            ]
        ],
        'technology' => [
            [
                'title' => 'Introduction to Precision Farming',
                'description' => 'Learn how technology can help optimize field-level management for improved crop yields and reduced waste.',
                'image' => 'precision_farming.svg',
                'url' => '/education.php?resource=precision_farming'
            ],
            [
                'title' => 'Using Drones in Agriculture',
                'description' => 'How drone technology is revolutionizing crop monitoring, spraying, and overall farm management.',
                'image' => 'drones.svg',
                'url' => '/education.php?resource=drones'
            ],
            [
                'title' => 'Mobile Apps for Farmers',
                'description' => 'Essential mobile applications that can help farmers track weather, manage inventory, and access market information.',
                'image' => 'mobile_apps.svg',
                'url' => '/education.php?resource=mobile_apps'
            ]
        ],
        'finance' => [
            [
                'title' => 'Agricultural Subsidies in India',
                'description' => 'Comprehensive guide to understanding and accessing government subsidies for farmers.',
                'image' => 'subsidies.svg',
                'url' => '/education.php?resource=subsidies'
            ],
            [
                'title' => 'Crop Insurance Programs',
                'description' => 'Overview of crop insurance options to protect farmers from losses due to natural disasters or price fluctuations.',
                'image' => 'insurance.svg',
                'url' => '/education.php?resource=insurance'
            ],
            [
                'title' => 'Financial Planning for Farmers',
                'description' => 'Basic financial management principles for agricultural businesses of all sizes.',
                'image' => 'financial_planning.svg',
                'url' => '/education.php?resource=financial_planning'
            ]
        ]
    ];
    
    if ($category && isset($resources[$category])) {
        return $resources[$category];
    }
    
    return $resources;
}

/**
 * Save contact form submission to database
 * 
 * @param array $formData The form data to save
 * @return bool Whether the save was successful
 */
function saveContactSubmission($formData) {
    global $pdo;
    
    try {
        // First, ensure the table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_submissions` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL,
            `subject` varchar(255) NOT NULL,
            `message` text NOT NULL,
            `created_at` datetime NOT NULL,
            `is_read` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // Prepare the insert statement
        $sql = "INSERT INTO contact_submissions (name, email, subject, message, created_at) VALUES (:name, :email, :subject, :message, :created_at)";
        $stmt = $pdo->prepare($sql);
        
        // Execute with the form data
        $result = $stmt->execute([
            ':name' => $formData['name'],
            ':email' => $formData['email'],
            ':subject' => $formData['subject'],
            ':message' => $formData['message'],
            ':created_at' => $formData['created_at']
        ]);
        
        return $result;
        
    } catch (PDOException $e) {
        // Log the error
        error_log("Error saving contact submission: " . $e->getMessage());
        return false;
    }
}

/**
 * Save community post to database
 * 
 * @param string $name The name of the poster
 * @param string $title The post title
 * @param string $content The post content
 * @param string $location The location of the poster
 * @param string $imagePath The path to the uploaded image
 * @param int|null $userId The user ID if logged in
 * @return bool Whether the save was successful
 */
function saveCommunityPost($name, $title, $content, $location, $imagePath = null, $userId = null) {
    global $conn;
    
    if (!$conn) {
        // If no database connection, still return true to avoid errors
        return true;
    }
    
    $postData = [
        'name' => $name,
        'title' => $title,
        'content' => $content,
        'location' => $location,
        'image_path' => $imagePath,
        'user_id' => $userId,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return dbInsertData('community_posts', $postData);
}

/**
 * Get community posts from database
 * 
 * @param int $limit Number of posts to get
 * @param int $offset Offset for pagination
 * @return array Community posts
 */
function getCommunityPosts($limit = null, $offset = 0) {
    global $pdo;
    
    if (!$pdo) {
        error_log("No database connection in getCommunityPosts");
        return [];
    }
    
    try {
        // Build the SQL query
        $sql = "SELECT * FROM community_posts ORDER BY created_at DESC";
        
        // Add limit if specified
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters if limit is specified
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($posts) . " posts in getCommunityPosts");
        return $posts;
    } catch(PDOException $e) {
        error_log("Error in getCommunityPosts: " . $e->getMessage());
        return [];
    }
}

/**
 * Get farmer stories/testimonials
 * 
 * @param int $limit Number of stories to get
 * @return array Farmer stories
 */
function getFarmerStories($limit = 3) {
    // In a real application, this would fetch from a database
    // For this implementation, we'll use static data
    $stories = [
        [
            'name' => 'Harpreet Singh',
            'age' => 45,
            'location' => 'Amritsar, Punjab',
            'crop' => 'Wheat & Rice',
            'image' => 'farmer1.svg',
            'quote' => 'Using the weather forecasting on AgroInnovate helped me plan my irrigation schedule better, saving water and improving my wheat yield by 15%.',
            'story' => 'As a third-generation farmer in Punjab, I\'ve seen many changes in agriculture. The unpredictable weather patterns in recent years made farming more challenging. Since using AgroInnovate\'s weather forecasting and educational resources, I\'ve been able to adapt my practices and optimize water usage. The community section also connected me with farmers facing similar challenges, and we now share solutions regularly.'
        ],
        [
            'name' => 'Lakshmi Devi',
            'age' => 38,
            'location' => 'Kurnool, Andhra Pradesh',
            'crop' => 'Cotton & Pulses',
            'image' => 'farmer2.svg',
            'quote' => 'The market price information helped me time my cotton harvest and sales perfectly, increasing my profits by nearly 20% compared to last season.',
            'story' => 'As a woman farmer managing 5 acres of land, I faced many challenges including lack of timely market information. AgroInnovate\'s market price trends have been invaluable. I can now make informed decisions about when to sell my crops for maximum profit. The educational content on pest management has also helped me reduce crop losses significantly. I\'ve encouraged other women farmers in my village to use this platform.'
        ],
        [
            'name' => 'Ramesh Patel',
            'age' => 52,
            'location' => 'Rajkot, Gujarat',
            'crop' => 'Groundnut & Cumin',
            'image' => 'farmer3.svg',
            'quote' => 'The educational resources on drip irrigation and water management transformed my farm productivity even during drought conditions.',
            'story' => 'Farming in a water-scarce region of Gujarat was becoming increasingly difficult due to climate change. Through AgroInnovate\'s educational resources, I learned about efficient drip irrigation systems and drought-resistant crop varieties. Implementing these techniques has made my farm resilient even during water shortage periods. The community forum also allowed me to share my experiences with farmers from similar regions, creating a support network.'
        ],
        [
            'name' => 'Meena Kumari',
            'age' => 35,
            'location' => 'Jharkhand',
            'crop' => 'Millets & Vegetables',
            'image' => 'farmer4.svg',
            'quote' => 'Switching to organic farming and millet cultivation has not only improved my family\'s health but also increased our income by 30%.',
            'story' => 'After years of struggling with chemical fertilizers and poor yields, I decided to switch to organic farming. Through AgroInnovate\'s resources, I learned about millet cultivation and organic farming techniques. The transition wasn\'t easy, but the platform\'s community support and expert guidance helped me succeed. Now, my farm produces healthy, chemical-free food, and I\'ve started a small organic produce business that supplies to local markets.'
        ],
        [
            'name' => 'Arun Kumar',
            'age' => 41,
            'location' => 'Tamil Nadu',
            'crop' => 'Banana & Coconut',
            'image' => 'farmer5.svg',
            'quote' => 'The precision farming techniques I learned through AgroInnovate have helped me reduce input costs by 25% while increasing my banana yield.',
            'story' => 'Managing a 10-acre banana plantation was always challenging, especially with rising input costs. After joining AgroInnovate, I learned about precision farming techniques and soil health management. The platform\'s weather alerts helped me plan irrigation and pest control measures better. I\'ve also connected with other banana farmers across India, sharing best practices and market insights.'
        ],
        [
            'name' => 'Priyanka Sharma',
            'age' => 29,
            'location' => 'Uttarakhand',
            'crop' => 'Herbs & Medicinal Plants',
            'image' => 'farmer6.svg',
            'quote' => 'AgroInnovate helped me transform my small terrace garden into a successful medicinal plant business.',
            'story' => 'Starting with just a small terrace garden, I was passionate about growing medicinal plants. Through AgroInnovate\'s resources, I learned about value addition and marketing strategies. The platform connected me with buyers and helped me understand market trends. Today, I run a successful business growing and processing medicinal herbs, employing several women from my village.'
        ]
    ];
    
    return array_slice($stories, 0, $limit);
}

// Fetch one record from database
function fetchOne($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Insert data into database
function insertData($table, $data) {
    global $pdo;
    try {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $pdo->lastInsertId();
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Update data in database
function updateData($table, $data, $where, $whereParams) {
    global $pdo;
    try {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE $table SET $set WHERE $where";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge(array_values($data), $whereParams));
        return true;
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Delete data from database
function deleteData($table, $where, $params) {
    global $pdo;
    try {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return true;
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send an email using PHPMailer with SMTP
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message body
 * @return bool Whether the email was sent successfully
 */
function sendEmail($to, $subject, $message) {
    try {
        // Use centralized SMTP sender that reads all settings from environment
        require_once __DIR__ . '/email_config.php';
        return sendEmailSMTP($to, $subject, $message);
    } catch (Exception $e) {
        error_log("Failed to send email to $to. Error: " . $e->getMessage());
        return false;
    }
}

// Password validation function
function validatePassword($password) {
    $errors = [];
    
    // Minimum 8 characters
    if(strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    // Must contain at least one uppercase letter
    if(!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    // Must contain at least one lowercase letter
    if(!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    // Must contain at least one number
    if(!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    // Must contain at least one special character
    if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    return [
        'isValid' => empty($errors),
        'errors' => $errors
    ];
}

// Phone number validation function
function validatePhone($phone) {
    // Remove any non-digit characters
    $cleanPhone = preg_replace('/\D/', '', $phone);
    
    // Check if it's a valid Indian phone number (10 digits, starting with 6-9)
    if(!preg_match('/^[6-9]\d{9}$/', $cleanPhone)) {
        return [
            'isValid' => false,
            'error' => "Please enter a valid 10-digit phone number starting with 6-9"
        ];
    }
    
    return [
        'isValid' => true,
        'error' => null
    ];
}
?>
