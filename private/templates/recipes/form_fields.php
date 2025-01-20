<?php
// Get all recipe attributes
$styles = RecipeAttribute::get_all(RecipeAttribute::TYPE_STYLE);
$diets = RecipeAttribute::get_all(RecipeAttribute::TYPE_DIET);
$types = RecipeAttribute::get_all(RecipeAttribute::TYPE_TYPE);
?>

<div class="form-group">
    <label for="title">Recipe Title</label>
    <input type="text" name="title" id="title" class="form-control" value="<?php echo h($recipe->title ?? ''); ?>" required>
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" class="form-control" rows="4" required><?php echo h($recipe->description ?? ''); ?></textarea>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="style_id">Cuisine Style</label>
            <select name="style_id" id="style_id" class="form-control" required>
                <option value="">Select Style</option>
                <?php foreach($styles as $style) { ?>
                    <option value="<?php echo h($style->id); ?>" <?php if(isset($recipe->style_id) && $recipe->style_id == $style->id) echo 'selected'; ?>>
                        <?php echo h($style->name); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="diet_id">Diet Category</label>
            <select name="diet_id" id="diet_id" class="form-control" required>
                <option value="">Select Diet</option>
                <?php foreach($diets as $diet) { ?>
                    <option value="<?php echo h($diet->id); ?>" <?php if(isset($recipe->diet_id) && $recipe->diet_id == $diet->id) echo 'selected'; ?>>
                        <?php echo h($diet->name); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="type_id">Meal Type</label>
            <select name="type_id" id="type_id" class="form-control" required>
                <option value="">Select Type</option>
                <?php foreach($types as $type) { ?>
                    <option value="<?php echo h($type->id); ?>" <?php if(isset($recipe->type_id) && $recipe->type_id == $type->id) echo 'selected'; ?>>
                        <?php echo h($type->name); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <h4>Preparation Time</h4>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="prep_hours">Hours</label>
                    <input type="number" name="prep_hours" id="prep_hours" class="form-control" min="0" value="<?php echo h($recipe->prep_hours ?? 0); ?>">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="prep_minutes">Minutes</label>
                    <input type="number" name="prep_minutes" id="prep_minutes" class="form-control" min="0" max="59" value="<?php echo h($recipe->prep_minutes ?? 0); ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <h4>Cooking Time</h4>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="cook_hours">Hours</label>
                    <input type="number" name="cook_hours" id="cook_hours" class="form-control" min="0" value="<?php echo h($recipe->cook_hours ?? 0); ?>">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="cook_minutes">Minutes</label>
                    <input type="number" name="cook_minutes" id="cook_minutes" class="form-control" min="0" max="59" value="<?php echo h($recipe->cook_minutes ?? 0); ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="video_url">Video URL (optional)</label>
    <input type="url" name="video_url" id="video_url" class="form-control" value="<?php echo h($recipe->video_url ?? ''); ?>">
</div>

<div class="form-group">
    <label for="recipe_image">Recipe Image</label>
    <input type="file" name="recipe_image" id="recipe_image" class="form-control" accept="image/*" <?php echo isset($recipe->img_file_path) ? '' : 'required'; ?>>
    <?php if(isset($recipe->img_file_path) && $recipe->img_file_path) { ?>
        <div class="mt-2">
            <img src="<?php echo url_for($recipe->image_path()); ?>" alt="Current recipe image" class="img-thumbnail" style="max-width: 200px;">
        </div>
    <?php } ?>
</div>

<div class="form-group">
    <label for="alt_text">Image Description (for accessibility)</label>
    <input type="text" name="alt_text" id="alt_text" class="form-control" value="<?php echo h($recipe->alt_text ?? ''); ?>" required>
</div>