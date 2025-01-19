<?php
// Authentication functions

function require_login() {
    global $session;
    if(!$session->is_logged_in()) {
        redirect_to('/users/login.php');
    }
}

function require_admin() {
    global $session;
    if(!$session->is_admin()) {
        redirect_to('/index.php');
    }
}

function log_in_user($user) {
    global $session;
    $session->login($user);
    redirect_to('/index.php');
}

function log_out_user() {
    global $session;
    $session->logout();
    redirect_to('/index.php');
}

function is_logged_in() {
    global $session;
    return $session->is_logged_in();
}

function is_admin() {
    global $session;
    return $session->is_admin();
}
?>
