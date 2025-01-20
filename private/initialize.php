<?php
ob_start(); // turn on output buffering

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Debug session state
error_log("Session state in initialize.php:");
error_log("Session ID: " . session_id());
error_log("Session Data: " . print_r($_SESSION, true));

// Assign file paths to PHP constants
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . '/public');
define("SHARED_PATH", PRIVATE_PATH . '/shared');
define("TEMPLATES_PATH", PRIVATE_PATH . '/templates');

// Assign the root URL to a PHP constant
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", $doc_root);

// Load core functions
require_once(PRIVATE_PATH . '/core/functions.php');
require_once(PRIVATE_PATH . '/core/database_functions.php');
require_once(PRIVATE_PATH . '/core/db_credentials.php');
require_once(PRIVATE_PATH . '/core/validation_functions.php');
require_once(PRIVATE_PATH . '/core/error_functions.php');
require_once(PRIVATE_PATH . '/core/auth.php');

// Autoload class definitions
function my_autoload($class) {
    if(preg_match('/\A\w+\Z/', $class)) {
        $file = PRIVATE_PATH . '/classes/' . $class . '.class.php';
        if(file_exists($file)) {
            require_once($file);
        }
    }
}

// Register autoloader
spl_autoload_register('my_autoload');

// Initialize database connection
db_connect();

// Create session object - MUST be after autoload
$session = new Session();

// Debug session object
error_log("Session object created:");
error_log("Is logged in: " . ($session->is_logged_in() ? "true" : "false"));

// Development mode error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
