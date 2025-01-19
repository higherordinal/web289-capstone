<?php
require_once('../private/initialize.php');

// Create placeholder images
$placeholders = [
    PUBLIC_PATH . '/images/cuisine-placeholder.jpg',
    PUBLIC_PATH . '/images/diet-placeholder.jpg',
    PUBLIC_PATH . '/images/type-placeholder.jpg'
];

foreach ($placeholders as $placeholder) {
    // Skip if placeholder already exists
    if (file_exists($placeholder)) {
        continue;
    }
    
    // Create a 1x1 transparent PNG as a minimal placeholder
    $image = imagecreatetruecolor(1, 1);
    $bg = imagecolorallocate($image, 240, 240, 240);
    imagefill($image, 0, 0, $bg);
    imagejpeg($image, $placeholder);
    imagedestroy($image);
    
    echo "Created placeholder: " . basename($placeholder) . "\n";
}

echo "Done creating placeholders!\n";
?>
