<?php

class Session {
    private $user_id;
    private $is_admin;
    public $message;
    public $message_type;
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->check_stored_login();
        $this->check_message();
    }

    public function login($user) {
        if($user) {
            // prevent session fixation attacks
            session_regenerate_id();
            $_SESSION['user_id'] = $user->id;
            $_SESSION['is_admin'] = $user->is_admin;
            $this->user_id = $user->id;
            $this->is_admin = $user->is_admin;
        }
        return true;
    }

    public function is_logged_in() {
        return isset($this->user_id);
    }

    public function is_admin() {
        return isset($this->is_admin) && $this->is_admin == 1;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['is_admin']);
        unset($this->user_id);
        unset($this->is_admin);
        return true;
    }

    private function check_stored_login() {
        if(isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
            $this->is_admin = $_SESSION['is_admin'];
        }
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