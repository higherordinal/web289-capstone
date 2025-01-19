<?php
require_once('../../private/initialize.php');
require_login();

// Only super admins can create new admins
if(!$session->is_super_admin()) {
    $session->message('Access denied. Super admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(is_post_request()) {
    $args = $_POST['admin'];
    $admin = new User($args);
    $admin->user_level = 'a'; // Set as admin
    
    if($admin->save()) {
        $session->message('Admin created successfully.');
        redirect_to(url_for('/users/admin_management.php'));
    }
} else {
    $admin = new User;
}

$page_title = 'Create Admin';
include(SHARED_PATH . '/header.php');
?>

<div class="admin new">
    <h1>Create New Admin</h1>

    <?php echo display_session_message(); ?>

    <form action="<?php echo url_for('/users/new_admin.php'); ?>" method="post">
        <?php include('form_fields.php'); ?>
        
        <div class="form-buttons">
            <input type="submit" value="Create Admin" />
            <a class="cancel" href="<?php echo url_for('/users/admin_management.php'); ?>">Cancel</a>
        </div>
    </form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
