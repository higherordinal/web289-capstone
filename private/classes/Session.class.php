<?php

class Session {
    private $user_id;
    private $username;
    private $is_admin;
    public $message;
    public $message_type;
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        error_log("Session constructor called");
        error_log("Session data in constructor: " . print_r($_SESSION, true));
        $this->check_stored_login();
        $this->check_message();
    }

    public function login($user) {
        if($user) {
            // prevent session fixation attacks
            session_regenerate_id();
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['username'] = $user->username;
            $_SESSION['is_admin'] = ($user->user_level === 'a');
            $this->user_id = $user->user_id;
            $this->username = $user->username;
            $this->is_admin = ($user->user_level === 'a');
            error_log("User logged in: " . print_r($_SESSION, true));
            error_log("Session object state after login:");
            error_log("user_id: " . $this->user_id);
            error_log("username: " . $this->username);
            error_log("is_admin: " . ($this->is_admin ? "true" : "false"));
        }
        return true;
    }

    public function is_logged_in() {
        $logged_in = isset($this->user_id) && isset($_SESSION['user_id']) && ($this->user_id === $_SESSION['user_id']);
        error_log("is_logged_in check: " . ($logged_in ? "true" : "false"));
        error_log("user_id property: " . (isset($this->user_id) ? $this->user_id : "not set"));
        error_log("session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "not set"));
        return $logged_in;
    }

    public function is_admin() {
        return isset($this->is_admin) && $this->is_admin === true;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['is_admin']);
        unset($this->user_id);
        unset($this->username);
        unset($this->is_admin);
        error_log("User logged out: " . print_r($_SESSION, true));
        return true;
    }

    private function check_stored_login() {
        error_log("Checking stored login");
        error_log("Session data before check: " . print_r($_SESSION, true));
        if(isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
            $this->username = $_SESSION['username'];
            $this->is_admin = $_SESSION['is_admin'];
            error_log("Restored session values:");
            error_log("user_id: " . $this->user_id);
            error_log("username: " . $this->username);
            error_log("is_admin: " . ($this->is_admin ? "true" : "false"));
        }
    }

    public function get_username() {
        return $this->username ?? '';
    }

    public function message($msg="") {
        if(!empty($msg)) {
            // Set message
            $_SESSION['message'] = $msg;
            return true;
        } else {
            // Get message
            $msg = $_SESSION['message'] ?? "";
            unset($_SESSION['message']);
            return $msg;
        }
    }

    public function set_message_type($type) {
        $_SESSION['message_type'] = $type;
    }

    public function message_type() {
        $type = $_SESSION['message_type'] ?? "";
        unset($_SESSION['message_type']);
        return $type;
    }

    private function check_message() {
        if(isset($_SESSION['message'])) {
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        } else {
            $this->message = "";
        }
    }
}
?>