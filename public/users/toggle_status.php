<?php
require_once('../../private/initialize.php');
require_login();

// Only admins can toggle user status
if(!$session->is_admin()) {
    $session->message('Access denied. Admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(!isset($_GET['id'])) {
    redirect_to(url_for('/users/user_management.php'));
}

$id = $_GET['id'];
$user = User::find_by_id($id);

if($user === false) {
    $session->message('User not found.', 'error');
    redirect_to(url_for('/users/user_management.php'));
}

// Regular admins cannot modify super admin or other admin accounts
if(!$session->is_super_admin() && ($user->is_admin() || $user->is_super_admin())) {
    $session->message('You do not have permission to modify admin accounts.', 'error');
    redirect_to(url_for('/users/user_management.php'));
}

if($user->toggle_active()) {
    $status = $user->is_active ? 'activated' : 'deactivated';
    $session->message("User {$user->username} has been {$status}.");
} else {
    $session->message('Failed to update user status.', 'error');
}

// Redirect back to the appropriate management page
if($user->is_admin() || $user->is_super_admin()) {
    redirect_to(url_for('/users/admin_management.php'));
} else {
    redirect_to(url_for('/users/user_management.php'));
}
?>
