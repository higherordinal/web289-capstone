<?php
require_once('../../private/initialize.php');
require_login();

$page_title = 'Add New Recipe';

if(is_post_request()) {
    $recipe = new Recipe($_POST);
    $recipe->user_id = $session->user_id;
    
    // Handle file upload
    if(isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = PUBLIC_PATH . '/uploads/recipes/';
        $temp_path = $_FILES['recipe_image']['tmp_name'];
        $filename = uniqid('recipe_') . '_' . $_FILES['recipe_image']['name'];
        $target_path = $upload_dir . $filename;
        
        if(move_uploaded_file($temp_path, $target_path)) {
            $recipe->img_file_path = '/uploads/recipes/' . $filename;
        } else {
            $session->message('Error uploading image', 'danger');
        }
    }
    
    if($recipe->save()) {
        $session->message('Recipe created successfully!', 'success');
        redirect_to(url_for('/recipes/show.php?id=' . $recipe->recipe_id));
    } else {
        // Re-render the form with error messages
    }
} else {
    $recipe = new Recipe();
}
?>

<?php include(SHARED_PATH . '/header.php'); ?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1><?php echo h($page_title); ?></h1>
            
            <?php echo display_errors($recipe->errors); ?>
            
            <form action="<?php echo url_for('/recipes/new.php'); ?>" method="post" enctype="multipart/form-data">
                <?php include('form_fields.php'); ?>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Create Recipe</button>
                    <a href="<?php echo url_for('/recipes/index.php'); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>