<?php
/**
 * Error handling and display functions
 */

function display_errors($errors=array()) {
    $output = '';
    if(!empty($errors)) {
        $output .= "<div class=\"errors\">";
        $output .= "Please fix the following errors:";
        $output .= "<ul>";
        foreach($errors as $error) {
            $output .= "<li>" . h($error) . "</li>";
        }
        $output .= "</ul>";
        $output .= "</div>";
    }
    return $output;
}

function error_404() {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    exit();
}

function error_500() {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit();
}

function display_session_message() {
    global $session;
    $msg = $session->message();
    if(isset($msg) && $msg != '') {
        $session->clear_message();
        return '<div id="message">' . h($msg) . '</div>';
    }
    return '';
}
?>
