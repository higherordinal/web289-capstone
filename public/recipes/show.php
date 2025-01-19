<?php
require_once('../../private/initialize.php');

// Get recipe ID from URL
$id = $_GET['id'] ?? '1';
$recipe = Recipe::find_by_id($id);
if(!$recipe) {
    redirect_to(url_for('/index.php'));
}

// Handle new review submission
if(is_post_request() && $session->is_logged_in()) {
    $args = [];
    $args['recipe_id'] = $recipe->recipe_id;
    $args['user_id'] = $session->user_id;
    $args['rating_value'] = $_POST['review']['rating'] ?? '';
    $args['comment_text'] = $_POST['review']['comment'] ?? '';
    
    $review = new Review($args);
    if($review->save()) {
        $session->message('Review submitted successfully.');
        redirect_to(url_for('/recipes/show.php?id=' . $id));
    } else {
        // Keep errors for display
    }
}

// Get all reviews for this recipe
$reviews = Review::find_by_recipe_id($recipe->recipe_id);

$page_title = $recipe->title;
$page_style = 'recipe-show';
include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/recipe-show.css'); ?>">

<div class="recipe-show">
    <a href="<?php echo url_for('/recipes/index.php'); ?>" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Recipes
    </a>
    <div class="recipe-header-image">
        <?php if($recipe->img_file_path) { ?>
            <img src="<?php echo url_for($recipe->image_path()); ?>" 
                 alt="<?php echo h($recipe->alt_text ?? $recipe->title); ?>">
        <?php } else { ?>
            <img src="<?php echo url_for('/images/recipe-placeholder.jpg'); ?>" 
                 alt="Recipe placeholder image">
        <?php } ?>
        <div class="recipe-header-overlay">
            <h1><?php echo h($recipe->title); ?></h1>
            <div class="recipe-meta">
                <span>
                    <i class="fas fa-clock"></i> 
                    Prep: <?php echo h($recipe->prep_hours); ?>h <?php echo h($recipe->prep_minutes); ?>m
                </span>
                <span>
                    <i class="fas fa-fire"></i>
                    Cook: <?php echo h($recipe->cook_hours); ?>h <?php echo h($recipe->cook_minutes); ?>m
                </span>
                <span>
                    <i class="fas fa-utensils"></i>
                    <?php echo h($recipe->style()->name ?? 'Any Style'); ?>
                </span>
                <span>
                    <i class="fas fa-leaf"></i>
                    <?php echo h($recipe->diet()->name ?? 'Any Diet'); ?>
                </span>
                <span>
                    <i class="fas fa-tag"></i>
                    <?php echo h($recipe->type()->name ?? 'Any Type'); ?>
                </span>
            </div>
        </div>
    </div>

    <div class="recipe-description">
        <?php echo h($recipe->description); ?>
    </div>

    <?php if($recipe->video_url) { ?>
        <div class="recipe-video">
            <h2>Watch How to Make It</h2>
            <div class="video-container">
                <?php
                // Convert YouTube URL to embed URL
                $video_id = '';
                if(preg_match('/[?&]v=([^&]+)/', $recipe->video_url, $matches)) {
                    $video_id = $matches[1];
                } elseif(preg_match('/youtu\.be\/([^?&]+)/', $recipe->video_url, $matches)) {
                    $video_id = $matches[1];
                }
                if($video_id) {
                    echo '<iframe src="https://www.youtube.com/embed/' . h($video_id) . '" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>';
                }
                ?>
            </div>
        </div>
    <?php } ?>

    <div class="comments-section">
        <div class="comments-header">
            <h2><i class="fas fa-comments"></i> Reviews & Comments</h2>
            <?php if($reviews) { 
                $avg_rating = array_reduce($reviews, function($carry, $review) {
                    return $carry + (int)$review->rating_value;
                }, 0) / count($reviews);
            ?>
                <div class="average-rating">
                    <span class="stars">
                        <?php for($i = 1; $i <= 5; $i++) { ?>
                            <i class="fas fa-star <?php echo $i <= round($avg_rating) ? 'filled' : ''; ?>"></i>
                        <?php } ?>
                    </span>
                    <?php echo number_format($avg_rating, 1); ?> / 5
                    (<?php echo count($reviews); ?> reviews)
                </div>
            <?php } ?>
        </div>

        <?php if($session->is_logged_in()) { ?>
            <form action="<?php echo url_for('/recipes/show.php?id=' . h(u($id))); ?>" 
                  method="POST" 
                  class="comment-form">
                <h3><i class="fas fa-pen"></i> Write a Review</h3>
                <div class="star-rating">
                    <input type="radio" id="star5" name="review[rating]" value="5">
                    <label for="star5" title="5 stars">★</label>
                    <input type="radio" id="star4" name="review[rating]" value="4">
                    <label for="star4" title="4 stars">★</label>
                    <input type="radio" id="star3" name="review[rating]" value="3">
                    <label for="star3" title="3 stars">★</label>
                    <input type="radio" id="star2" name="review[rating]" value="2">
                    <label for="star2" title="2 stars">★</label>
                    <input type="radio" id="star1" name="review[rating]" value="1">
                    <label for="star1" title="1 star">★</label>
                </div>

                <textarea name="review[comment]" 
                          class="comment-textarea" 
                          placeholder="Share your thoughts about this recipe..."
                          required></textarea>

                <button type="submit" class="submit-comment">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
            </form>
        <?php } else { ?>
            <div class="login-prompt">
                <i class="fas fa-lock"></i>
                Please <a href="<?php echo url_for('/users/login.php'); ?>">log in</a> 
                to leave a review.
            </div>
        <?php } ?>

        <div class="comments-list">
            <?php foreach($reviews as $review) { 
                $user = $review->user();
            ?>
                <div class="comment">
                    <div class="comment-header">
                        <span class="comment-author">
                            <i class="fas fa-user"></i>
                            <?php echo h($review->user()->username); ?>
                        </span>
                        <?php if($review->created_date) { ?>
                            <span class="comment-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo h($review->created_date); ?>
                            </span>
                        <?php } ?>
                    </div>
                    <div class="comment-rating">
                        <?php for($i = 1; $i <= 5; $i++) { ?>
                            <span class="star <?php echo $i <= $review->rating_value ? 'filled' : ''; ?>">
                                ★
                            </span>
                        <?php } ?>
                    </div>
                    <?php if($review->comment_text) { ?>
                        <div class="comment-content">
                            <?php echo h($review->comment_text); ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>