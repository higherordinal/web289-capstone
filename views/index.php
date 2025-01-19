<?php
require_once('../private/initialize.php');
$page_title = 'Welcome to Flavor Connect';

// Get featured recipes
$featured_recipes = Recipe::find_featured(3);

// Get recipe stats
$total_recipes = Recipe::count_all();
$total_users = User::count_all();
$total_reviews = Review::count_all();

include(SHARED_PATH . '/header.php');
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

<!-- Featured Recipes Section -->
<section class="featured-section">
    <h2>Featured Recipes</h2>
    <div class="featured-grid">
        <?php foreach($featured_recipes as $recipe) { ?>
            <article class="featured-card">
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
            </article>
        <?php } ?>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <h2>Popular Categories</h2>
    <div class="category-grid">
        <?php
        $categories = [
            'Breakfast' => 'breakfast.jpg',
            'Main Course' => 'main-course.jpg',
            'Desserts' => 'desserts.jpg',
            'Vegetarian' => 'vegetarian.jpg',
            'Quick & Easy' => 'quick-easy.jpg',
            'Healthy' => 'healthy.jpg'
        ];
        
        foreach($categories as $name => $image) {
        ?>
            <a href="<?php echo url_for('/recipes/index.php?category=' . u($name)); ?>" 
               class="category-card">
                <img src="<?php echo url_for('/images/categories/' . $image); ?>" 
                     alt="<?php echo h($name); ?>">
                <div class="category-overlay">
                    <?php echo h($name); ?>
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
