<?php
// Get all recipe attributes for dropdowns
$styles = RecipeAttribute::find_all(RecipeAttribute::TYPE_STYLE);
$diets = RecipeAttribute::find_all(RecipeAttribute::TYPE_DIET);
$types = RecipeAttribute::find_all(RecipeAttribute::TYPE_TYPE);
?>

<div class="form-group">
    <label for="title" class="form-label">
        Recipe Title
        <span class="required" aria-hidden="true">*</span>
    </label>
    <input type="text" class="form-control <?php echo isset($recipe->errors['title']) ? 'is-invalid' : ''; ?>" 
           id="title" name="title" value="<?php echo h($recipe->title ?? ''); ?>" 
           required aria-required="true" aria-describedby="title-help title-error">
    <small id="title-help" class="form-text">Enter a descriptive title for your recipe</small>
    <?php if(isset($recipe->errors['title'])) { ?>
        <div id="title-error" class="invalid-feedback"><?php echo $recipe->errors['title']; ?></div>
    <?php } ?>
</div>

<div class="form-group">
    <label for="description" class="form-label">
        Description
        <span class="required" aria-hidden="true">*</span>
    </label>
    <textarea class="form-control <?php echo isset($recipe->errors['description']) ? 'is-invalid' : ''; ?>" 
              id="description" name="description" rows="3" required aria-required="true" 
              aria-describedby="description-help description-error"><?php echo h($recipe->description ?? ''); ?></textarea>
    <small id="description-help" class="form-text">Describe your recipe, including any special notes or tips</small>
    <?php if(isset($recipe->errors['description'])) { ?>
        <div id="description-error" class="invalid-feedback"><?php echo $recipe->errors['description']; ?></div>
    <?php } ?>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="style_id" class="form-label">
            Style
            <span class="required" aria-hidden="true">*</span>
        </label>
        <select class="form-control <?php echo isset($recipe->errors['style_id']) ? 'is-invalid' : ''; ?>" 
                id="style_id" name="style_id" required aria-required="true" 
                aria-describedby="style-error">
            <option value="">Select Style</option>
            <?php foreach($styles as $style) { ?>
                <option value="<?php echo h($style->id); ?>" <?php if(isset($recipe->style_id) && $recipe->style_id == $style->id) echo 'selected'; ?>>
                    <?php echo h($style->name); ?>
                </option>
            <?php } ?>
        </select>
        <?php if(isset($recipe->errors['style_id'])) { ?>
            <div id="style-error" class="invalid-feedback"><?php echo $recipe->errors['style_id']; ?></div>
        <?php } ?>
    </div>
    
    <div class="form-group col-md-4">
        <label for="diet_id" class="form-label">
            Diet
            <span class="required" aria-hidden="true">*</span>
        </label>
        <select class="form-control <?php echo isset($recipe->errors['diet_id']) ? 'is-invalid' : ''; ?>" 
                id="diet_id" name="diet_id" required aria-required="true" 
                aria-describedby="diet-error">
            <option value="">Select Diet</option>
            <?php foreach($diets as $diet) { ?>
                <option value="<?php echo h($diet->id); ?>" <?php if(isset($recipe->diet_id) && $recipe->diet_id == $diet->id) echo 'selected'; ?>>
                    <?php echo h($diet->name); ?>
                </option>
            <?php } ?>
        </select>
        <?php if(isset($recipe->errors['diet_id'])) { ?>
            <div id="diet-error" class="invalid-feedback"><?php echo $recipe->errors['diet_id']; ?></div>
        <?php } ?>
    </div>
    
    <div class="form-group col-md-4">
        <label for="type_id" class="form-label">
            Type
            <span class="required" aria-hidden="true">*</span>
        </label>
        <select class="form-control <?php echo isset($recipe->errors['type_id']) ? 'is-invalid' : ''; ?>" 
                id="type_id" name="type_id" required aria-required="true" 
                aria-describedby="type-error">
            <option value="">Select Type</option>
            <?php foreach($types as $type) { ?>
                <option value="<?php echo h($type->id); ?>" <?php if(isset($recipe->type_id) && $recipe->type_id == $type->id) echo 'selected'; ?>>
                    <?php echo h($type->name); ?>
                </option>
            <?php } ?>
        </select>
        <?php if(isset($recipe->errors['type_id'])) { ?>
            <div id="type-error" class="invalid-feedback"><?php echo $recipe->errors['type_id']; ?></div>
        <?php } ?>
    </div>
</div>

<fieldset class="form-group">
    <legend class="form-label">Preparation Time</legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="prep_hours" class="form-label">Hours</label>
            <input type="number" class="form-control" id="prep_hours" name="prep_hours" 
                   min="0" value="<?php echo h($recipe->prep_hours ?? '0'); ?>" 
                   aria-describedby="prep-time-help">
        </div>
        <div class="form-group col-md-6">
            <label for="prep_minutes" class="form-label">Minutes</label>
            <input type="number" class="form-control" id="prep_minutes" name="prep_minutes" 
                   min="0" max="59" value="<?php echo h($recipe->prep_minutes ?? '0'); ?>">
        </div>
    </div>
    <small id="prep-time-help" class="form-text">How long does it take to prepare the ingredients?</small>
</fieldset>

<fieldset class="form-group">
    <legend class="form-label">Cooking Time</legend>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="cook_hours" class="form-label">Hours</label>
            <input type="number" class="form-control" id="cook_hours" name="cook_hours" 
                   min="0" value="<?php echo h($recipe->cook_hours ?? '0'); ?>" 
                   aria-describedby="cook-time-help">
        </div>
        <div class="form-group col-md-6">
            <label for="cook_minutes" class="form-label">Minutes</label>
            <input type="number" class="form-control" id="cook_minutes" name="cook_minutes" 
                   min="0" max="59" value="<?php echo h($recipe->cook_minutes ?? '0'); ?>">
        </div>
    </div>
    <small id="cook-time-help" class="form-text">How long does it take to cook the recipe?</small>
</fieldset>

<div class="form-group">
    <label for="video_url" class="form-label">Video URL</label>
    <input type="url" class="form-control <?php echo isset($recipe->errors['video_url']) ? 'is-invalid' : ''; ?>" 
           id="video_url" name="video_url" value="<?php echo h($recipe->video_url ?? ''); ?>" 
           aria-describedby="video-help video-error">
    <small id="video-help" class="form-text">Optional: Add a link to your recipe video</small>
    <?php if(isset($recipe->errors['video_url'])) { ?>
        <div id="video-error" class="invalid-feedback"><?php echo $recipe->errors['video_url']; ?></div>
    <?php } ?>
</div>

<div class="form-group">
    <label for="recipe_image" class="form-label">
        Recipe Image
        <span class="required" aria-hidden="true">*</span>
    </label>
    <input type="file" class="form-control-file <?php echo isset($recipe->errors['img_file_path']) ? 'is-invalid' : ''; ?>" 
           id="recipe_image" name="recipe_image" accept="image/*" 
           <?php echo isset($recipe->img_file_path) ? '' : 'required aria-required="true"'; ?> 
           aria-describedby="image-help image-error">
    <small id="image-help" class="form-text">Upload a clear photo of your finished recipe (JPEG, PNG)</small>
    <?php if(isset($recipe->errors['img_file_path'])) { ?>
        <div id="image-error" class="invalid-feedback"><?php echo $recipe->errors['img_file_path']; ?></div>
    <?php } ?>
</div>

<div class="form-group">
    <label for="alt_text" class="form-label">
        Image Description
        <span class="required" aria-hidden="true">*</span>
    </label>
    <input type="text" class="form-control <?php echo isset($recipe->errors['alt_text']) ? 'is-invalid' : ''; ?>" 
           id="alt_text" name="alt_text" value="<?php echo h($recipe->alt_text ?? ''); ?>" 
           required aria-required="true" aria-describedby="alt-help alt-error">
    <small id="alt-help" class="form-text">Describe the image for visually impaired users</small>
    <?php if(isset($recipe->errors['alt_text'])) { ?>
        <div id="alt-error" class="invalid-feedback"><?php echo $recipe->errors['alt_text']; ?></div>
    <?php } ?>
</div>

<style>
/* Accessibility improvements */
.form-label {
    color: #2d3748; /* Darker color for better contrast */
    font-weight: 500;
}

.required {
    color: #e53e3e; /* High contrast red for required field indicator */
    margin-left: 0.25rem;
}

.form-text {
    color: #4a5568; /* Ensured sufficient contrast for help text */
}

.form-control {
    border: 2px solid #cbd5e0; /* Thicker border for better visibility */
}

.form-control:focus {
    outline: none;
    border-color: #3182ce; /* Higher contrast focus color */
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.3);
}

.is-invalid {
    border-color: #e53e3e; /* High contrast red for error state */
}

.invalid-feedback {
    color: #c53030; /* Darker red for error messages */
    font-weight: 500;
}
</style>