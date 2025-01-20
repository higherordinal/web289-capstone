<?php
require_once('../../private/initialize.php');
require_login();

$page_title = 'Create New Recipe';
$page_style = 'recipe-form';
include(SHARED_PATH . '/header.php');

$recipe = new Recipe();

if(is_post_request()) {
    // Handle file upload
    if(isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
        $temp_path = $_FILES['recipe_image']['tmp_name'];
        $extension = strtolower(pathinfo($_FILES['recipe_image']['name'], PATHINFO_EXTENSION));
        $filename = uniqid('recipe_') . '.' . $extension;
        $target_path = PUBLIC_PATH . '/uploads/recipes/' . $filename;

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if(!in_array($extension, $allowed_types)) {
            $session->message('Invalid file type. Please upload a JPG, PNG, or GIF image.', 'error');
        } else {
            // Move file to target location
            if(move_uploaded_file($temp_path, $target_path)) {
                $_POST['img_file_path'] = $filename;
            } else {
                $session->message('Error uploading image. Please try again.', 'error');
            }
        }
    }

    // Set user_id from session
    $_POST['user_id'] = $session->user_id;
    
    // Create recipe
    $result = $recipe->merge_attributes($_POST);
    $result = $recipe->save();

    if($result === true) {
        $new_id = $recipe->recipe_id;
        $session->message('Recipe created successfully.');
        redirect_to(url_for('/recipes/show.php?id=' . $new_id));
    } else {
        // Form submission failed
        $session->message('Error creating recipe. Please check the form and try again.', 'error');
    }
}
?>

<div class="recipe-form">
    <h1>Create New Recipe</h1>
    
    <form action="<?php echo url_for('/recipes/new.php'); ?>" method="post" enctype="multipart/form-data">
        <?php include(TEMPLATES_PATH . '/recipes/form_fields.php'); ?>
        
        <div class="form-group mt-4">
            <button type="submit" class="btn btn-primary">Create Recipe</button>
            <a href="<?php echo url_for('/recipes/index.php'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>