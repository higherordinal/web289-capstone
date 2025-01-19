<?php
require_once(__DIR__ . '/../private/initialize.php');

// Create image directories if they don't exist
$image_dirs = [
    PUBLIC_PATH . '/images/cuisines',
    PUBLIC_PATH . '/images/diets',
    PUBLIC_PATH . '/images/types',
];

foreach ($image_dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Sample image URLs for each category
$category_images = [
    'cuisines' => [
        'italian' => 'https://source.unsplash.com/800x800/?italian,food',
        'mediterranean' => 'https://source.unsplash.com/800x800/?mediterranean,food',
        'korean' => 'https://source.unsplash.com/800x800/?korean,food',
        'mexican' => 'https://source.unsplash.com/800x800/?mexican,food',
        'indian' => 'https://source.unsplash.com/800x800/?indian,food',
        'chinese' => 'https://source.unsplash.com/800x800/?chinese,food',
        'japanese' => 'https://source.unsplash.com/800x800/?japanese,food',
        'thai' => 'https://source.unsplash.com/800x800/?thai,food'
    ],
    'diets' => [
        'vegetarian' => 'https://source.unsplash.com/800x800/?vegetarian,food',
        'vegan' => 'https://source.unsplash.com/800x800/?vegan,food',
        'gluten-free' => 'https://source.unsplash.com/800x800/?gluten,free,food',
        'keto' => 'https://source.unsplash.com/800x800/?keto,food',
        'paleo' => 'https://source.unsplash.com/800x800/?paleo,food'
    ],
    'types' => [
        'main-course' => 'https://source.unsplash.com/800x800/?main,course,dinner',
        'appetizer' => 'https://source.unsplash.com/800x800/?appetizer,food',
        'dessert' => 'https://source.unsplash.com/800x800/?dessert,food',
        'breakfast' => 'https://source.unsplash.com/800x800/?breakfast,food',
        'lunch' => 'https://source.unsplash.com/800x800/?lunch,food',
        'snack' => 'https://source.unsplash.com/800x800/?snack,food',
        'beverage' => 'https://source.unsplash.com/800x800/?beverage,drink'
    ]
];

// Create placeholder images
$placeholder_size = 800;
$placeholder_images = [
    PUBLIC_PATH . '/images/cuisine-placeholder.jpg',
    PUBLIC_PATH . '/images/diet-placeholder.jpg',
    PUBLIC_PATH . '/images/type-placeholder.jpg'
];

foreach ($placeholder_images as $placeholder) {
    $image = imagecreatetruecolor($placeholder_size, $placeholder_size);
    
    // Set background color (light gray)
    $bg_color = imagecolorallocate($image, 240, 240, 240);
    imagefill($image, 0, 0, $bg_color);
    
    // Add a circle
    $circle_color = imagecolorallocate($image, 200, 200, 200);
    $center = $placeholder_size / 2;
    $radius = $placeholder_size / 3;
    imagefilledellipse($image, $center, $center, $radius * 2, $radius * 2, $circle_color);
    
    // Add text
    $text_color = imagecolorallocate($image, 150, 150, 150);
    $text = basename($placeholder, '.jpg');
    $font_size = 40;
    $font_path = PUBLIC_PATH . '/fonts/PlayfairDisplay-Regular.ttf';
    
    // Get text dimensions
    $bbox = imagettfbbox($font_size, 0, $font_path, $text);
    $text_width = $bbox[2] - $bbox[0];
    $text_height = $bbox[1] - $bbox[7];
    
    // Center text
    $text_x = ($placeholder_size - $text_width) / 2;
    $text_y = ($placeholder_size + $text_height) / 2;
    
    imagettftext($image, $font_size, 0, $text_x, $text_y, $text_color, $font_path, $text);
    
    // Save image
    imagejpeg($image, $placeholder, 90);
    imagedestroy($image);
    echo "Created placeholder image: " . basename($placeholder) . "\n";
}

// Download category images
foreach ($category_images as $category => $images) {
    foreach ($images as $name => $url) {
        $target_path = PUBLIC_PATH . "/images/{$category}/{$name}.jpg";
        if (!file_exists($target_path)) {
            $image_data = file_get_contents($url);
            if ($image_data !== false) {
                file_put_contents($target_path, $image_data);
                echo "Downloaded {$name} image for {$category}\n";
            }
        }
    }
}

echo "Done downloading images!\n";
?>
