<?php

function fix_initialize_paths($directory) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );

    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            
            // Get relative depth to private directory
            $relativePath = str_repeat('../', substr_count(str_replace($directory, '', $path), DIRECTORY_SEPARATOR));
            
            // Update initialize.php path
            $content = preg_replace(
                '/require_once\([\'"].*?initialize\.php[\'"]\)/',
                'require_once(\'' . $relativePath . 'private/initialize.php\')',
                $content
            );
            
            file_put_contents($path, $content);
        }
    }
}

fix_initialize_paths('c:/xampp/htdocs/project/public');
echo "Paths updated successfully!\n";
?>
