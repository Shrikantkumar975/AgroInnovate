<?php
// Ensure clean JSON output
ini_set('display_errors', '0');
if (ob_get_level() > 0) { ob_end_clean(); }
ob_start();

// Include necessary files
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json; charset=UTF-8');

// Determine request mode: coordinates or location name
$lat = isset($_GET['lat']) ? $_GET['lat'] : null;
$lon = isset($_GET['lon']) ? $_GET['lon'] : null;
$location = isset($_GET['location']) ? sanitizeInput($_GET['location']) : null;

if ($lat !== null && $lon !== null) {
    $weatherData = getWeatherDataByCoords($lat, $lon);
    $resolvedLocation = isset($weatherData['name']) ? $weatherData['name'] : 'Your Location';
} else {
    $location = $location ?: 'Delhi';
    $weatherData = getWeatherData($location);
    $resolvedLocation = $location;
}

// Check if weather data retrieval was successful
if ($weatherData === false) {
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to retrieve weather data. Please try again later.'
    ]);
    exit;
}

// Format the response
$response = [
    'success' => true,
    'location' => $resolvedLocation,
    'country' => 'India',
    'current' => [
        'temp' => isset($weatherData['main']['temp']) ? round($weatherData['main']['temp']) : null,
        'feels_like' => isset($weatherData['main']['feels_like']) ? round($weatherData['main']['feels_like']) : null,
        'humidity' => isset($weatherData['main']['humidity']) ? $weatherData['main']['humidity'] : null,
        'pressure' => isset($weatherData['main']['pressure']) ? $weatherData['main']['pressure'] : null,
        'wind_speed' => isset($weatherData['wind']['speed']) ? $weatherData['wind']['speed'] : null,
        'wind_direction' => isset($weatherData['wind']['deg']) ? $weatherData['wind']['deg'] : null,
        'weather_main' => isset($weatherData['weather'][0]['main']) ? $weatherData['weather'][0]['main'] : null,
        'weather_description' => isset($weatherData['weather'][0]['description']) ? ucfirst($weatherData['weather'][0]['description']) : null,
        'weather_icon' => isset($weatherData['weather'][0]['icon']) ? $weatherData['weather'][0]['icon'] : null,
        'timestamp' => isset($weatherData['dt']) ? $weatherData['dt'] : time()
    ]
];

// Get forecast data matching the mode
if ($lat !== null && $lon !== null) {
    $forecastData = getWeatherForecastByCoords($lat, $lon);
} else {
    $forecastData = getWeatherForecast($resolvedLocation);
}

if ($forecastData !== false && isset($forecastData['list'])) {
    $forecast = [];
    $processedDays = [];
    
    foreach ($forecastData['list'] as $item) {
        // Get date from timestamp
        $date = date('Y-m-d', $item['dt']);
        
        // Skip if we already have this day (to get only one forecast per day)
        if (in_array($date, $processedDays)) {
            continue;
        }
        
        // Add to forecast array
        $forecast[] = [
            'date' => $date,
            'day' => date('l', $item['dt']),
            'temp_max' => round($item['main']['temp_max']),
            'temp_min' => round($item['main']['temp_min']),
            'weather_main' => $item['weather'][0]['main'],
            'weather_description' => ucfirst($item['weather'][0]['description']),
            'weather_icon' => $item['weather'][0]['icon']
        ];
        
        // Add to processed days
        $processedDays[] = $date;
        
        // Break after 5 days
        if (count($forecast) >= 5) {
            break;
        }
    }
    
    $response['forecast'] = $forecast;
}

// Return the response (strip any stray output)
$json = json_encode($response);
ob_clean();
echo $json;
exit;
?>
