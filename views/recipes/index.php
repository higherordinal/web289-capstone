<?php
require_once('../../private/initialize.php');
$page_title = 'Recipe Gallery';

// Get filter parameters
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = $_GET['search'] ?? '';
$style_id = !empty($_GET['style']) ? (int)$_GET['style'] : null;
$diet_id = !empty($_GET['diet']) ? (int)$_GET['diet'] : null;
$sort = $_GET['sort'] ?? 'newest';
$per_page = 25;

// Get all styles and diets for filters
$styles = Style::find_all();
$diets = Diet::find_all();

// Calculate offset
$offset = ($current_page - 1) * $per_page;

// Get total filtered recipes for pagination
$total_recipes = Recipe::count_all_filtered($search, $style_id, $diet_id);
$total_pages = ceil($total_recipes / $per_page);

// Ensure current page is within valid range
if ($current_page < 1) {
    redirect_to('/recipes/index.php');
}
if ($current_page > $total_pages && $total_pages > 0) {
    redirect_to('/recipes/index.php?page=' . $total_pages);
}

// Get filtered and sorted recipes
$recipes = Recipe::find_by_page_with_relations($per_page, $offset, $search, $style_id, $diet_id, $sort);

include(SHARED_PATH . '/header.php');

// Helper function to maintain query parameters
function build_query_string($params_to_update=[]) {
    $current_params = $_GET;
    $params = array_merge($current_params, $params_to_update);
    unset($params['page']); // Remove page when changing filters
    return http_build_query($params);
}
?>

<div class="recipe-gallery">
    <h1>Recipe Gallery</h1>
    
    <!-- Search and Filter Form -->
    <form action="<?php echo url_for('/recipes/index.php'); ?>" method="GET" class="filters-form">
        <div class="search-box">
            <input type="text" name="search" value="<?php echo h($search); ?>" 
                   placeholder="Search recipes..." class="search-input">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <div class="filter-controls">
            <select name="style" class="filter-select">
                <option value="">All Styles</option>
                <?php foreach($styles as $style) { ?>
                    <option value="<?php echo h($style->id); ?>" 
                            <?php if($style_id === $style->id) echo 'selected'; ?>>
                        <?php echo h($style->name); ?>
                    </option>
                <?php } ?>
            </select>
            
            <select name="diet" class="filter-select">
                <option value="">All Diets</option>
                <?php foreach($diets as $diet) { ?>
                    <option value="<?php echo h($diet->id); ?>"
                            <?php if($diet_id === $diet->id) echo 'selected'; ?>>
                        <?php echo h($diet->name); ?>
                    </option>
                <?php } ?>
            </select>
            
            <select name="sort" class="filter-select">
                <option value="newest" <?php if($sort === 'newest') echo 'selected'; ?>>
                    Newest First
                </option>
                <option value="oldest" <?php if($sort === 'oldest') echo 'selected'; ?>>
                    Oldest First
                </option>
                <option value="rating" <?php if($sort === 'rating') echo 'selected'; ?>>
                    Highest Rated
                </option>
                <option value="popular" <?php if($sort === 'popular') echo 'selected'; ?>>
                    Most Popular
                </option>
                <option value="title" <?php if($sort === 'title') echo 'selected'; ?>>
                    Title A-Z
                </option>
            </select>
            
            <button type="submit" class="filter-button">Apply Filters</button>
            <?php if(!empty($search) || !empty($style_id) || !empty($diet_id) || $sort !== 'newest') { ?>
                <a href="<?php echo url_for('/recipes/index.php'); ?>" class="clear-filters">Clear Filters</a>
            <?php } ?>
        </div>
    </form>

    <!-- Results Summary -->
    <div class="results-summary">
        <?php
        $filter_descriptions = [];
        if(!empty($search)) $filter_descriptions[] = "matching \"" . h($search) . "\"";
        if(!empty($style_id)) {
            $style = Style::find_by_id($style_id);
            if($style) $filter_descriptions[] = "in " . h($style->name) . " style";
        }
        if(!empty($diet_id)) {
            $diet = Diet::find_by_id($diet_id);
            if($diet) $filter_descriptions[] = "for " . h($diet->name) . " diet";
        }
        
        echo "<p>";
        echo "Showing " . count($recipes) . " of " . $total_recipes . " recipes";
        if(!empty($filter_descriptions)) {
            echo " " . implode(", ", $filter_descriptions);
        }
        echo "</p>";
        ?>
    </div>

    <?php if(empty($recipes)) { ?>
        <div class="no-results">
            <p>No recipes found matching your criteria.</p>
            <p>Try adjusting your filters or <a href="<?php echo url_for('/recipes/index.php'); ?>">view all recipes</a>.</p>
        </div>
    <?php } else { ?>
        <div class="recipe-grid">
            <?php foreach($recipes as $recipe) { ?>
                <article class="recipe-card">
                    <a href="<?php echo url_for('/recipes/show.php?id=' . h(u($recipe->id))); ?>">
                        <div class="recipe-image">
                            <?php if($recipe->image_path) { ?>
                                <img src="<?php echo url_for('/uploads/' . h($recipe->image_path)); ?>" 
                                     alt="<?php echo h($recipe->title); ?>"
                                     loading="lazy">
                            <?php } else { ?>
                                <img src="<?php echo url_for('/images/recipe-placeholder.jpg'); ?>" 
                                     alt="No image available"
                                     loading="lazy">
                            <?php } ?>
                        </div>
                        <div class="recipe-content">
                            <h2><?php echo h($recipe->title); ?></h2>
                            <div class="recipe-meta">
                                <?php if($recipe->style()) { ?>
                                    <span class="recipe-style">
                                        <i class="fas fa-utensils"></i> <?php echo h($recipe->style()->name); ?>
                                    </span>
                                <?php } ?>
                                <?php if($recipe->diet()) { ?>
                                    <span class="recipe-diet">
                                        <i class="fas fa-leaf"></i> <?php echo h($recipe->diet()->name); ?>
                                    </span>
                                <?php } ?>
                            </div>
                            <div class="recipe-rating" data-rating="<?php echo h($recipe->rating); ?>">
                                <?php for($i = 1; $i <= 5; $i++) { ?>
                                    <i class="fas fa-star <?php echo ($i <= $recipe->rating) ? 'active' : ''; ?>"></i>
                                <?php } ?>
                                <span class="rating-count">(<?php echo h($recipe->rating_count); ?>)</span>
                            </div>
                        </div>
                    </a>
                </article>
            <?php } ?>
        </div>

        <?php if($total_pages > 1) { ?>
            <div class="pagination">
                <?php if($current_page > 1) { ?>
                    <a href="<?php echo url_for('/recipes/index.php?' . build_query_string(['page' => $current_page - 1])); ?>" 
                       class="pagination-link">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php } ?>

                <div class="pagination-numbers">
                    <?php
                    // Show first page
                    if($current_page > 2) {
                        echo '<a href="' . url_for('/recipes/index.php?' . build_query_string(['page' => 1])) . '" ';
                        echo 'class="pagination-link">1</a>';
                        if($current_page > 3) {
                            echo '<span class="pagination-ellipsis">...</span>';
                        }
                    }

                    // Show pages around current page
                    for($i = max(1, $current_page - 1); $i <= min($total_pages, $current_page + 1); $i++) {
                        echo '<a href="' . url_for('/recipes/index.php?' . build_query_string(['page' => $i])) . '" ';
                        echo 'class="pagination-link' . ($i == $current_page ? ' active' : '') . '">';
                        echo $i . '</a>';
                    }

                    // Show last page
                    if($current_page < $total_pages - 1) {
                        if($current_page < $total_pages - 2) {
                            echo '<span class="pagination-ellipsis">...</span>';
                        }
                        echo '<a href="' . url_for('/recipes/index.php?' . build_query_string(['page' => $total_pages])) . '" ';
                        echo 'class="pagination-link">' . $total_pages . '</a>';
                    }
                    ?>
                </div>

                <?php if($current_page < $total_pages) { ?>
                    <a href="<?php echo url_for('/recipes/index.php?' . build_query_string(['page' => $current_page + 1])); ?>" 
                       class="pagination-link">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
