<?php
require_once('private/initialize.php');

echo "Testing database connection...\n";
var_dump($database);

echo "\nTesting user_account table...\n";
$sql = "SHOW TABLES LIKE 'user_account'";
$result = mysqli_query($database, $sql);
var_dump($result);

echo "\nTesting a sample query...\n";
$sql = "SELECT * FROM user_account LIMIT 1";
$result = mysqli_query($database, $sql);
var_dump($result);

if (!$result) {
    echo "Error: " . mysqli_error($database);
}
?>
