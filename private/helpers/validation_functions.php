<?php
// Validation functions

function is_blank($value) {
    return !isset($value) || trim($value) === '';
}

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

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

function has_valid_password_format($password) {
    $has_length = has_length($password, ['min' => 8]);
    $has_upper = preg_match('/[A-Z]/', $password);
    $has_lower = preg_match('/[a-z]/', $password);
    $has_number = preg_match('/[0-9]/', $password);
    return $has_length && $has_upper && $has_lower && $has_number;
}

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