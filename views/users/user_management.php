<?php
require_once('../../private/initialize.php');
require_login();

// Only admins and super admins can access this page
if(!$session->is_admin()) {
    $session->message('Access denied. Admin privileges required.');
    redirect_to(url_for('/index.php'));
}

// Get all regular users
$users = User::find_all_regular_users();

$page_title = 'User Management';
include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>">

<div class="admin-management">
    <h1>User Management</h1>
    
    <?php echo display_session_message(); ?>
    
    <div class="actions">
        <a class="action" href="<?php echo url_for('/users/new_user.php'); ?>">Create New User</a>
    </div>

    <table class="list">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
            <th>Recipes</th>
            <th>Actions</th>
        </tr>

        <?php foreach($users as $user) { ?>
            <tr>
                <td><?php echo h($user->username); ?></td>
                <td><?php echo h($user->email); ?></td>
                <td>
                    <span class="status <?php echo $user->is_active ? 'active' : 'inactive'; ?>">
                        <?php echo $user->is_active ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td><?php echo Recipe::count_by_user($user->id); ?></td>
                <td class="actions">
                    <a class="action" href="<?php echo url_for('/users/edit_user.php?id=' . h(u($user->id))); ?>">Edit</a>
                    <a class="action" href="<?php echo url_for('/users/view_user.php?id=' . h(u($user->id))); ?>">View</a>
                    <a class="action delete" href="<?php echo url_for('/users/delete_user.php?id=' . h(u($user->id))); ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
