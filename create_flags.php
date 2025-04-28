<?php
// Script to generate flag images for language selection
$languages = [
    'en' => ['name' => 'English', 'flag' => '🇬🇧'],
    'hi' => ['name' => 'हिंदी', 'flag' => '🇮🇳']
];

// Create flags directory if it doesn't exist
$flagsDir = __DIR__ . '/assets/flags';
if (!file_exists($flagsDir)) {
    mkdir($flagsDir, 0777, true);
}

foreach ($languages as $code => $lang) {
    // Create an image with dimensions 30x20
    $img = imagecreatetruecolor(30, 20);
    
    // Add flag emoji
    $text = $lang['flag'];
    
    // Save the image
    $filename = $flagsDir . '/' . $code . '.png';
    imagepng($img, $filename);
    imagedestroy($img);
    
    echo "Created flag for {$lang['name']} ({$code})\n";
}

echo "Flag generation complete!\n";
?> 