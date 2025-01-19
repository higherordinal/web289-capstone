<?php
require_once('../../private/initialize.php');
require_login();

// Only super admins can access this page
if(!$session->is_super_admin()) {
    $session->message('Access denied. Super admin privileges required.');
    redirect_to(url_for('/index.php'));
}

$page_title = 'Admin Management';
include(SHARED_PATH . '/header.php');

// Get all admin users (both admin and super admin)
$admins = User::find_all_admins();
?>

<link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>">

<div class="admin-management">
    <h1>Admin Management</h1>
    
    <?php echo display_session_message(); ?>
    
    <?php if($session->is_super_admin()) { ?>
        <div class="actions">
            <a class="action" href="<?php echo url_for('/users/new_admin.php'); ?>">Create New Admin</a>
        </div>
    <?php } ?>

    <table class="list">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Level</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php foreach($admins as $admin) { ?>
            <tr>
                <td><?php echo h($admin->username); ?></td>
                <td><?php echo h($admin->email); ?></td>
                <td><?php echo $admin->user_level === 'a' ? 'Admin' : 'Super Admin'; ?></td>
                <td>
                    <span class="status <?php echo $admin->is_active ? 'active' : 'inactive'; ?>">
                        <?php echo $admin->is_active ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td class="actions">
                    <a class="action" href="<?php echo url_for('/users/edit_admin.php?id=' . h(u($admin->id))); ?>">Edit</a>
                    <?php if($session->is_super_admin() && $admin->user_level !== 's') { ?>
                        <a class="action <?php echo $admin->is_active ? 'deactivate' : 'activate'; ?>" 
                           href="<?php echo url_for('/users/toggle_status.php?id=' . h(u($admin->id))); ?>"
                           onclick="return confirm('Are you sure you want to <?php echo $admin->is_active ? 'deactivate' : 'activate'; ?> this admin?');">
                            <?php echo $admin->is_active ? 'Deactivate' : 'Activate'; ?>
                        </a>
                        <a class="action delete" href="<?php echo url_for('/users/delete_admin.php?id=' . h(u($admin->id))); ?>">Delete</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
