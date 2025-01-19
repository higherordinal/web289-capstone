<?php
// Validation functions

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
    global $database;
    $sql = "SELECT * FROM user_account ";
    $sql .= "WHERE username = '" . db_escape($database, $username) . "' ";
    $sql .= "AND id != '" . db_escape($database, $current_id) . "'";
    $result = mysqli_query($database, $sql);
    $user_count = mysqli_num_rows($result);
    mysqli_free_result($result);
    return $user_count === 0;
}

function has_unique_email($email, $current_id="0") {
    global $database;
    $sql = "SELECT * FROM user_account ";
    $sql .= "WHERE email = '" . db_escape($database, $email) . "' ";
    $sql .= "AND id != '" . db_escape($database, $current_id) . "'";
    $result = mysqli_query($database, $sql);
    $user_count = mysqli_num_rows($result);
    mysqli_free_result($result);
    return $user_count === 0;
}
?>
