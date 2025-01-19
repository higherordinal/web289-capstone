<?php
require_once('../../../private/initialize.php');
require_login();

if(!$session->is_admin()) {
    $session->message('Access denied. Admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(is_post_request()) {
    $args = $_POST['type'];
    $type = new RecipeType($args);
    if($type->save()) {
        $session->message('Recipe type created successfully.');
        redirect_to(url_for('/admin/recipe_metadata.php'));
    }
} else {
    $type = new RecipeType;
}

$page_title = 'Create Recipe Type';
include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>">

<div class="admin new">
    <h1>Create New Recipe Type</h1>

    <?php echo display_session_message(); ?>

    <form action="<?php echo url_for('/admin/type/new.php'); ?>" method="post">
        <div class="form-group">
            <label for="name">Type Name</label>
            <input type="text" name="type[name]" id="name" value="<?php echo h($type->name); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description (Optional)</label>
            <textarea name="type[description]" id="description" rows="3"><?php echo h($type->description); ?></textarea>
        </div>

        <div class="form-buttons">
            <input type="submit" value="Create Type">
            <a class="cancel" href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Cancel</a>
        </div>
    </form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
