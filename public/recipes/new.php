<form action="<?php echo url_for('/recipes/new.php'); ?>" method="post" enctype="multipart/form-data">
    <?php include(TEMPLATES_PATH . '/recipes/form_fields.php'); ?>
    
    <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary">Create Recipe</button>
        <a href="<?php echo url_for('/recipes/index.php'); ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>