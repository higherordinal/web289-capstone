<?php
/**
 * Application Configuration
 */

// Database Configuration - Local Development
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'flavor_connect');

// Database Configuration - Production
// Uncomment this block and comment out the above block when deploying
/*
define('DB_HOST', 'your-production-host.com');
define('DB_USER', 'your_production_user');
define('DB_PASS', 'your_production_password');
define('DB_NAME', 'flavor_connect_prod');
*/

// Path Configuration
define('PROJECT_ROOT', dirname(__DIR__));
define('PUBLIC_PATH', PROJECT_ROOT . '/public');
define('PRIVATE_PATH', PROJECT_ROOT . '/private');
define('VIEWS_PATH', PROJECT_ROOT . '/views');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');

// URL Configuration
// Set this to your domain in production, leave empty for local development
define('WWW_ROOT', '');  // Production example: '/flavor_connect'

// Application Settings
define('APP_NAME', 'Flavor Connect');
define('APP_EMAIL', 'admin@flavorconnect.com');
define('ITEMS_PER_PAGE', 10);

// Upload Settings
define('MAX_FILE_SIZE', 5242880);  // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Session Settings
define('SESSION_EXPIRY', 7200);  // 2 hours in seconds

// Environment Setting
define('DEVELOPMENT_MODE', true);  // Set to false in production

// Error Reporting - Based on environment
if (DEVELOPMENT_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Time Zone
date_default_timezone_set('America/New_York');
