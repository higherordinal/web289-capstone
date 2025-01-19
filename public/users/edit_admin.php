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
if($admin === false) {
    redirect_to(url_for('/users/admin_management.php'));
}

if(is_post_request()) {
    $args = $_POST['admin'];
    
    // Prevent changing super admin status through form manipulation
    if($admin->user_level === 's') {
        $args['user_level'] = 's';
    }
    
    $admin->merge_attributes($args);
    if($admin->save()) {
        $session->message('Admin updated successfully.');
        redirect_to(url_for('/users/admin_management.php'));
    }
}

$page_title = 'Edit Admin';
include(SHARED_PATH . '/header.php');
?>

<div class="admin edit">
    <h1>Edit Admin: <?php echo h($admin->username); ?></h1>

    <?php echo display_session_message(); ?>

    <form action="<?php echo url_for('/users/edit_admin.php?id=' . h(u($id))); ?>" method="post">
        <?php include('form_fields.php'); ?>
        
        <div class="form-buttons">
            <input type="submit" value="Update Admin" />
            <a class="cancel" href="<?php echo url_for('/users/admin_management.php'); ?>">Cancel</a>
        </div>
    </form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
