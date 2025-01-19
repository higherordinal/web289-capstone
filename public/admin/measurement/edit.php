<?php
require_once('../../private/initialize.php');
require_login();

if(!$session->is_admin()) {
    redirect_to(url_for('/index.php'));
}

$id = $_GET['id'] ?? '';
if(!$id) {
    redirect_to(url_for('/admin/recipe_metadata.php'));
}

$measurement = Measurement::find_by_id($id);
if($measurement == false) {
    redirect_to(url_for('/admin/recipe_metadata.php'));
}

if(is_post_request()) {
    $args = $_POST['measurement'];
    $measurement->merge_attributes($args);
    if($measurement->save()) {
        $session->message('Measurement updated successfully.');
        redirect_to(url_for('/admin/recipe_metadata.php'));
    }
}

$page_title = 'Edit Measurement';
include(SHARED_PATH . '/header.php');
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1><?php echo h($page_title); ?></h1>
            
            <?php echo display_errors($measurement->errors); ?>
            
            <form action="<?php echo url_for('/admin/measurement/edit.php?id=' . h(u($id))); ?>" method="post">
                <?php include(TEMPLATES_PATH . '/admin/measurement/form_fields.php'); ?>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Update Measurement</button>
                    <a class="btn btn-secondary" href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
