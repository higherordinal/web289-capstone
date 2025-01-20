<?php
// Validation functions

/**
 * Validates if a value is present
 * @param string|null $value The value to check
 * @return bool True if value is blank
 */
function is_blank($value) {
    return !isset($value) || trim($value) === '';
}

/**
 * Validates if a value is a valid email format
 * @param string $email The value to check
 * @return bool True if value is a valid email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validates if a value has a specific length
 * @param string $value The value to check
 * @param array $options Array with 'min' and/or 'max' length
 * @return bool True if length is valid
 */
function has_length($value, $options=[]) {
    if(isset($options['min']) && strlen($value) < $options['min']) {
        return false;
    } elseif(isset($options['max']) && strlen($value) > $options['max']) {
        return false;
    } elseif(isset($options['exact']) && strlen($value) != $options['exact']) {
        return false;
    }
    return true;
}

/**
 * Validates if a password has a valid format
 * @param string $password The value to check
 * @return bool True if password is valid
 */
function has_valid_password_format($password) {
    $has_length = has_length($password, ['min' => 8]);
    $has_upper = preg_match('/[A-Z]/', $password);
    $has_lower = preg_match('/[a-z]/', $password);
    $has_number = preg_match('/[0-9]/', $password);
    return $has_length && $has_upper && $has_lower && $has_number;
}

/**
 * Validates if a username is unique
 * @param string $username The value to check
 * @param string $current_id The current user ID
 * @return bool True if username is unique
 */
function has_unique_username($username, $current_id="0") {
    $database = DatabaseObject::get_database();
    $sql = "SELECT * FROM user_account ";
    $sql .= "WHERE username = '" . $database->real_escape_string($username) . "' ";
    $sql .= "AND user_id != '" . $database->real_escape_string($current_id) . "'";
    $result = $database->query($sql);
    if(!$result) {
        // Query failed, log error and return false to indicate validation failure
        error_log("Database error in has_unique_username: " . $database->error);
        return false;
    }
    $user_count = $result->num_rows;
    $result->free();
    return $user_count === 0;
}

/**
 * Validates if an email is unique
 * @param string $email The value to check
 * @param string $current_id The current user ID
 * @return bool True if email is unique
 */
function has_unique_email($email, $current_id="0") {
    $database = DatabaseObject::get_database();
    $sql = "SELECT * FROM user_account ";
    $sql .= "WHERE email = '" . $database->real_escape_string($email) . "' ";
    $sql .= "AND user_id != '" . $database->real_escape_string($current_id) . "'";
    $result = $database->query($sql);
    if(!$result) {
        // Query failed, log error and return false to indicate validation failure
        error_log("Database error in has_unique_email: " . $database->error);
        return false;
    }
    $user_count = $result->num_rows;
    $result->free();
    return $user_count === 0;
}

/**
 * Validates if a number is between min and max values
 * @param int|float $value The value to check
 * @param int|float $min Minimum value
 * @param int|float $max Maximum value
 * @return bool True if value is between min and max
 */
function has_number_between($value, $min, $max) {
    if(!is_numeric($value)) {
        return false;
    }
    $value = (float)$value;
    return ($value >= $min && $value <= $max);
}