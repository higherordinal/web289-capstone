<?php
require_once('../../../private/initialize.php');
require_login();

if(!$session->is_admin()) {
    $session->message('Access denied. Admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(is_post_request()) {
    $args = $_POST['diet'];
    $diet = new RecipeDiet($args);
    if($diet->save()) {
        $session->message('Diet type created successfully.');
        redirect_to(url_for('/admin/recipe_metadata.php'));
    }
} else {
    $diet = new RecipeDiet;
}

$page_title = 'Create Diet Type';
include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>">

<div class="admin new">
    <h1>Create New Diet Type</h1>

    <?php echo display_session_message(); ?>

    <form action="<?php echo url_for('/admin/diet/new.php'); ?>" method="post">
        <div class="form-group">
            <label for="name">Diet Name</label>
            <input type="text" name="diet[name]" id="name" value="<?php echo h($diet->name); ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="diet[description]" id="description" rows="3" required><?php echo h($diet->description); ?></textarea>
        </div>

        <div class="form-group">
            <label for="restrictions">Dietary Restrictions</label>
            <textarea name="diet[restrictions]" id="restrictions" rows="3"><?php echo h($diet->restrictions); ?></textarea>
            <small>List any specific ingredients or food groups that are not allowed in this diet.</small>
        </div>

        <div class="form-buttons">
            <input type="submit" value="Create Diet">
            <a class="cancel" href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Cancel</a>
        </div>
    </form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
