<?php
require_once('../../private/initialize.php');
require_login();

if(!$session->is_admin()) {
    $session->message('Access denied. Admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(!isset($_GET['id'])) {
    redirect_to(url_for('/admin/recipe_metadata.php'));
}
$id = $_GET['id'];
$diet = RecipeDiet::find_by_id($id);
if($diet === false) {
    redirect_to(url_for('/admin/recipe_metadata.php'));
}

if(is_post_request()) {
    // Check if diet is in use
    $recipe_count = Recipe::count_by_diet($diet->id);
    if($recipe_count > 0) {
        $session->message("Cannot delete diet. It is used by {$recipe_count} recipes.", 'error');
    } else {
        if($diet->delete()) {
            $session->message('Diet type deleted successfully.');
        }
    }
    redirect_to(url_for('/admin/recipe_metadata.php'));
}

$page_title = 'Delete Diet Type';
include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>">

<div class="admin delete">
    <h1>Delete Diet Type</h1>

    <?php echo display_session_message(); ?>

    <div class="delete-confirmation">
        <p>Are you sure you want to delete the diet type: <strong><?php echo h($diet->name); ?></strong>?</p>
        
        <?php $recipe_count = Recipe::count_by_diet($diet->id); ?>
        <?php if($recipe_count > 0) { ?>
            <p class="warning">Warning: This diet type is currently used by <?php echo $recipe_count; ?> recipe(s).</p>
            <p>You cannot delete a diet type that is in use. Please reassign these recipes to a different diet type first.</p>
        <?php } else { ?>
            <p class="warning">This action cannot be undone.</p>
            
            <form action="<?php echo url_for('/admin/diet/delete.php?id=' . h(u($id))); ?>" method="post">
                <div class="form-buttons delete">
                    <input type="submit" value="Delete Diet">
                    <a class="cancel" href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Cancel</a>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
