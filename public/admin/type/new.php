<?php
require_once('../../private/initialize.php');
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

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1><?php echo h($page_title); ?></h1>
            
            <?php echo display_errors($type->errors); ?>
            
            <form action="<?php echo url_for('/admin/type/new.php'); ?>" method="post">
                <?php include(TEMPLATES_PATH . '/admin/type/form_fields.php'); ?>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Create Recipe Type</button>
                    <a class="btn btn-secondary" href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
