<?php
require_once('../../private/initialize.php');
require_login();

if(!$session->is_super_admin()) {
    $session->message('Access denied. Super admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(!isset($_GET['id'])) {
    redirect_to(url_for('/users/admin_management.php'));
}
$id = $_GET['id'];
$admin = User::find_by_id($id);

if($admin === false || $admin->user_level === 's') {
    $session->message('Cannot delete super admin accounts.');
    redirect_to(url_for('/users/admin_management.php'));
}

if(is_post_request()) {
    if($admin->delete()) {
        $session->message('Admin deleted successfully.');
    } else {
        $session->message('Failed to delete admin.', 'error');
    }
    redirect_to(url_for('/users/admin_management.php'));
}

$page_title = 'Delete Admin';
include(SHARED_PATH . '/header.php');
?>

<div class="admin delete">
    <h1>Delete Admin</h1>
    
    <?php echo display_session_message(); ?>

    <div class="delete-confirmation">
        <p>Are you sure you want to delete the admin account for: <strong><?php echo h($admin->username); ?></strong>?</p>
        <p class="warning">This action cannot be undone.</p>

        <form action="<?php echo url_for('/users/delete_admin.php?id=' . h(u($id))); ?>" method="post">
            <div class="form-buttons delete">
                <input type="submit" value="Delete Admin" />
                <a class="cancel" href="<?php echo url_for('/users/admin_management.php'); ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
