<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Welcome to The Corner Post - Your ultimate recipe-sharing community to discover, share, and enjoy culinary creations.">
    <title>The Scrumptious Rump | Recipe Sharing Community</title>
    <!-- Favicon -->
    <link rel="icon" href="/assets/favicon.ico" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <?php 
    require_once 'initialize.php'; 
    include 'header.php'; 
    ?>

    <main class="landing-page">
        <section class="hero">
            <div class="container">
                <h1>Welcome to The Scrumptious Rump</h1>
                <p>Your ultimate community for discovering, sharing, and enjoying recipes from around the world.</p>
                <a href="/recipes" class="btn-primary">Explore Recipes</a>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="feature">
                    <h2>Discover</h2>
                    <p>Explore a wide variety of recipes, from family favorites to international delights.</p>
                </div>
                <div class="feature">
                    <h2>Share</h2>
                    <p>Upload your own recipes and inspire others with your culinary creativity.</p>
                </div>
                <div class="feature">
                    <h2>Connect</h2>
                    <p>Engage with fellow food enthusiasts and exchange tips, tricks, and reviews.</p>
                </div>
            </div>
        </section>

        <section class="recipe-gallery">
            <div class="container">
                <h2>Featured Recipes</h2>
                <div class="gallery">
                    <?php
                    // Query to fetch recipes
                    $query = "SELECT id, title, description, image FROM recipes LIMIT 8";
                    $result = mysqli_query($connection, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($recipe = mysqli_fetch_assoc($result)) {
                            echo "<div class='card'>";
                            echo "<img src='" . htmlspecialchars($recipe['image']) . "' alt='" . htmlspecialchars($recipe['title']) . "'>";
                            echo "<div class='card-content'>";
                            echo "<h3>" . htmlspecialchars($recipe['title']) . "</h3>";
                            echo "<p>" . htmlspecialchars($recipe['description']) . "</p>";
                            echo "<a href='/recipe.php?id=" . $recipe['id'] . "' class='btn-secondary'>View Recipe</a>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No recipes found. Check back later!</p>";
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="call-to-action">
            <div class="container">
                <h2>Join The Corner Post Community</h2>
                <p>Sign up to save your favorite recipes, share your creations, and connect with other food lovers.</p>
                <a href="/register.php" class="btn-secondary">Join Now</a>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>