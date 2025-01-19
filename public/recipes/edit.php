<form action="<?php echo url_for('/recipes/edit.php?id=' . h(u($id))); ?>" method="post" enctype="multipart/form-data">
    <?php include(SITE_ROOT . '/public/recipes/form_fields.php'); ?>
    
    <div class="form-group mt-4">
        <button type="submit" class="btn btn-primary">Update Recipe</button>
        <a href="<?php echo url_for('/recipes/show.php?id=' . h(u($id))); ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>