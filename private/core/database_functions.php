<?php

function db_connect() {
  $connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  confirm_db_connect($connection);
  DatabaseObject::set_database($connection);
  return $connection;
}

function confirm_db_connect($connection) {
  if($connection->connect_errno) {
    $msg = "Database connection failed: ";
    $msg .= $connection->connect_error;
    $msg .= " (" . $connection->connect_errno . ")";
    exit($msg);
  }
}

function db_escape($connection, $string) {
  if (!$connection instanceof mysqli) {
    // If connection is not a mysqli object, try to get the global connection
    global $database;
    if ($database instanceof mysqli) {
      $connection = $database;
    } else {
      // If no valid connection is found, return the string with basic escaping
      return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
  }
  return mysqli_real_escape_string($connection, $string);
}
?>