<?php
ob_start(); // turn on output buffering

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Assign file paths to PHP constants
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
define("PUBLIC_PATH", PROJECT_PATH . '/public');
define("SHARED_PATH", PROJECT_PATH . '/views/shared');

// Assign the root URL to a PHP constant
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", $doc_root);

// Load helper functions
require_once(PRIVATE_PATH . '/helpers/functions.php');
require_once(PRIVATE_PATH . '/helpers/validation_functions.php');
require_once(PRIVATE_PATH . '/helpers/auth.php');
require_once(PRIVATE_PATH . '/helpers/database_functions.php');
require_once(PRIVATE_PATH . '/helpers/db_credentials.php');

// Load base DatabaseObject class first
require_once(PRIVATE_PATH . '/classes/DatabaseObject.class.php');

// Autoload class definitions
function my_autoload($class) {
    if(preg_match('/\A\w+\Z/', $class)) {
        $file = PRIVATE_PATH . '/classes/' . $class . '.class.php';
        if(file_exists($file)) {
            require_once($file);
        }
    }
}
spl_autoload_register('my_autoload');

// Initialize objects
$database = db_connect();
DatabaseObject::set_database($database);
$session = new Session();

// Development mode error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
