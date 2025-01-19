<?php
require_once('../initialize.php');

$image_data = 'YOUR_BASE64_IMAGE_DATA';  // Replace with actual base64 image data
$image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
$image_data = str_replace(' ', '+', $image_data);
$image_binary = base64_decode($image_data);

$target_dir = PUBLIC_PATH . '/uploads/recipes/';
$target_file = $target_dir . 'buddha-bowl.jpg';

// Ensure directory exists
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Save the image
if (file_put_contents($target_file, $image_binary)) {
    echo "Image saved successfully to: " . $target_file . "\n";
    echo "File size: " . filesize($target_file) . " bytes\n";
} else {
    echo "Failed to save image\n";
}

// Update database
$sql = "UPDATE recipe 
        SET img_file_path = 'buddha-bowl.jpg',
            alt_text = 'A beautifully arranged vegetarian Buddha bowl featuring crispy tofu, sliced avocado, edamame, shredded carrots, cucumber, fresh herbs, and bean sprouts in a black bowl, with a small blue and white patterned sauce dish'
        WHERE title LIKE '%Buddha Bowl%'";

$database = db_connect();
$result = mysqli_query($database, $sql);

if ($result) {
    echo "Database updated successfully\n";
} else {
    echo "Database update failed: " . mysqli_error($database) . "\n";
}
?>
