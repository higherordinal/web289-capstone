<?php
require_once('../private/initialize.php');

$page_title = 'Welcome to Flavor Connect';
$page_style = 'home';
include(SHARED_PATH . '/header.php');

// Get featured recipes
$featured_recipes = Recipe::find_featured(3);

// Get recipe stats
$total_recipes = Recipe::count_all();
$total_users = User::count_all();
$total_reviews = Review::count_all();

// Get categories
$styles = RecipeAttribute::get_all(RecipeAttribute::TYPE_STYLE);
$diets = RecipeAttribute::get_all(RecipeAttribute::TYPE_DIET);
$types = RecipeAttribute::get_all(RecipeAttribute::TYPE_TYPE);

?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Discover & Share Amazing Recipes</h1>
        <p>Join our community of food lovers and explore thousands of delicious recipes from around the world.</p>
        <div class="hero-buttons">
            <a href="<?php echo url_for('/recipes/index.php'); ?>" class="btn btn-primary">Browse Recipes</a>
            <?php if(!$session->is_logged_in()) { ?>
                <a href="<?php echo url_for('/users/register.php'); ?>" class="btn btn-secondary">Join Now</a>
            <?php } else { ?>
                <a href="<?php echo url_for('/recipes/new.php'); ?>" class="btn btn-secondary">Share Recipe</a>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="category-group">
        <h2>Cuisine Styles</h2>
        <div class="category-grid">
            <?php foreach($styles as $style) { 
                $image_name = strtolower(str_replace(' ', '-', $style->name));
                $image_path = '/images/cuisines/' . $image_name . '.jpg';
                $placeholder_path = '/images/cuisine-placeholder.jpg';
                $final_path = file_exists(PUBLIC_PATH . $image_path) ? $image_path : $placeholder_path;
            ?>
                <a href="<?php echo url_for('/recipes/index.php?style_id=' . h(u($style->id))); ?>" 
                   class="category-item">
                    <div class="category-image">
                        <img src="<?php echo url_for($final_path); ?>" 
                             alt="<?php echo h($style->name); ?> Cuisine">
                        <div class="category-overlay">
                            <span class="category-name"><?php echo h($style->name); ?></span>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="category-group">
        <h2>Dietary Preferences</h2>
        <div class="category-grid">
            <?php foreach($diets as $diet) { 
                $image_name = strtolower(str_replace(' ', '-', $diet->name));
                $image_path = '/images/diets/' . $image_name . '.jpg';
                $placeholder_path = '/images/diet-placeholder.jpg';
                $final_path = file_exists(PUBLIC_PATH . $image_path) ? $image_path : $placeholder_path;
            ?>
                <a href="<?php echo url_for('/recipes/index.php?diet_id=' . h(u($diet->id))); ?>" 
                   class="category-item">
                    <div class="category-image">
                        <img src="<?php echo url_for($final_path); ?>" 
                             alt="<?php echo h($diet->name); ?> Diet">
                        <div class="category-overlay">
                            <span class="category-name"><?php echo h($diet->name); ?></span>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="category-group">
        <h2>Meal Types</h2>
        <div class="category-grid">
            <?php foreach($types as $type) { 
                $image_name = strtolower(str_replace(' ', '-', $type->name));
                $image_path = '/images/types/' . $image_name . '.jpg';
                $placeholder_path = '/images/type-placeholder.jpg';
                $final_path = file_exists(PUBLIC_PATH . $image_path) ? $image_path : $placeholder_path;
            ?>
                <a href="<?php echo url_for('/recipes/index.php?type_id=' . h(u($type->id))); ?>" 
                   class="category-item">
                    <div class="category-image">
                        <img src="<?php echo url_for($final_path); ?>" 
                             alt="<?php echo h($type->name); ?> Type">
                        <div class="category-overlay">
                            <span class="category-name"><?php echo h($type->name); ?></span>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>

<!-- Featured Recipes Section -->
<section class="featured-section">
    <h2>Featured Recipes</h2>
    <div class="featured-grid">
        <?php foreach($featured_recipes as $recipe) { ?>
            <a href="<?php echo url_for('/recipes/show.php?id=' . h(u($recipe->recipe_id))); ?>" class="featured-card">
                <div class="card-image">
                    <img src="<?php echo url_for($recipe->image_path()); ?>" 
                         alt="<?php echo h($recipe->title); ?>">
                </div>
                <div class="card-content">
                    <h3><?php echo h($recipe->title); ?></h3>
                    <p><?php echo h(substr($recipe->description, 0, 100)) . '...'; ?></p>
                    <div class="card-meta">
                        <span>By <?php echo h($recipe->author()->username); ?></span>
                        <span><?php echo h($recipe->rating_average()); ?> ★</span>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number"><?php echo number_format($total_recipes); ?></div>
            <div class="stat-label">Recipes</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo number_format($total_users); ?></div>
            <div class="stat-label">Community Members</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo number_format($total_reviews); ?></div>
            <div class="stat-label">Reviews</div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-content">
        <h2>Share Your Culinary Journey</h2>
        <p>Whether you're a home cook or a professional chef, your recipes and experiences can inspire others. Join our community today and start sharing your favorite dishes.</p>
        <?php if(!$session->is_logged_in()) { ?>
            <a href="<?php echo url_for('/users/register.php'); ?>" class="btn btn-primary">Get Started</a>
        <?php } else { ?>
            <a href="<?php echo url_for('/recipes/new.php'); ?>" class="btn btn-primary">Share Your First Recipe</a>
        <?php } ?>
    </div>
</section>

<?php include(SHARED_PATH . '/footer.php'); ?>
