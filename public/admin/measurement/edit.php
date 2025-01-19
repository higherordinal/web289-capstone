<?php
require_once('../../../private/initialize.php');
require_login();

if(!$session->is_admin()) {
    $session->message('Access denied. Admin privileges required.');
    redirect_to(url_for('/index.php'));
}

if(!isset($_GET['id'])) {
    redirect_to(url_for('/admin/recipe_metadata.php'));
}
$id = $_GET['id'];
$measurement = Measurement::find_by_id($id);
if($measurement === false) {
    redirect_to(url_for('/admin/recipe_metadata.php'));
}

if(is_post_request()) {
    $args = $_POST['measurement'];
    $measurement->merge_attributes($args);
    if($measurement->save()) {
        $session->message('Measurement unit updated successfully.');
        redirect_to(url_for('/admin/recipe_metadata.php'));
    }
}

$page_title = 'Edit Measurement Unit';
include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>">

<div class="admin edit">
    <h1>Edit Measurement Unit: <?php echo h($measurement->name); ?></h1>

    <?php echo display_session_message(); ?>

    <form action="<?php echo url_for('/admin/measurement/edit.php?id=' . h(u($id))); ?>" method="post">
        <div class="form-group">
            <label for="name">Unit Name</label>
            <input type="text" name="measurement[name]" id="name" value="<?php echo h($measurement->name); ?>" required>
            <small>Example: cup, teaspoon, gram, ounce</small>
        </div>

        <div class="form-group">
            <label for="abbreviation">Abbreviation</label>
            <input type="text" name="measurement[abbreviation]" id="abbreviation" value="<?php echo h($measurement->abbreviation); ?>" required>
            <small>Example: c, tsp, g, oz</small>
        </div>

        <div class="form-group">
            <label for="type">Measurement Type</label>
            <select name="measurement[type]" id="type" required>
                <option value="volume" <?php echo $measurement->type === 'volume' ? 'selected' : ''; ?>>Volume</option>
                <option value="weight" <?php echo $measurement->type === 'weight' ? 'selected' : ''; ?>>Weight</option>
                <option value="count" <?php echo $measurement->type === 'count' ? 'selected' : ''; ?>>Count</option>
            </select>
        </div>

        <div class="form-group">
            <label for="conversion_factor">Conversion Factor (Optional)</label>
            <input type="number" step="0.0001" name="measurement[conversion_factor]" id="conversion_factor" value="<?php echo h($measurement->conversion_factor); ?>">
            <small>Factor to convert to base unit (ml for volume, g for weight)</small>
        </div>

        <div class="form-buttons">
            <input type="submit" value="Update Measurement">
            <a class="cancel" href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Cancel</a>
        </div>
    </form>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
