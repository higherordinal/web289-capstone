<?php
// Get current year for copyright
$current_year = date('Y');
?>
</main>
</div> <!-- End of main-content -->

<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section brand">
            <a href="<?php echo url_for('/'); ?>" class="logo-link">
                <span class="logo-icon"><i class="fas fa-mortar-pestle"></i></span>
                <span class="logo-text">Flavor<span class="accent">Connect</span></span>
            </a>
            <p>Discover, create, and share delicious recipes with our community of food lovers.</p>
        </div>
        
        <div class="footer-section">
            <h3>Quick Links</h3>
            <nav class="footer-nav">
                <a href="<?php echo url_for('/'); ?>">Home</a>
                <a href="<?php echo url_for('/recipes'); ?>">All Recipes</a>
                <?php if($session->is_logged_in()) { ?>
                    <a href="<?php echo url_for('/recipes/my_recipes.php'); ?>">My Recipes</a>
                    <a href="<?php echo url_for('/recipes/favorites.php'); ?>">My Favorites</a>
                <?php } else { ?>
                    <a href="<?php echo url_for('/users/login.php'); ?>">Login</a>
                    <a href="<?php echo url_for('/users/register.php'); ?>">Sign Up</a>
                <?php } ?>
            </nav>
        </div>

        <div class="footer-section">
            <h3>Categories</h3>
            <nav class="footer-nav">
                <a href="<?php echo url_for('/recipes?style=Italian'); ?>">Italian</a>
                <a href="<?php echo url_for('/recipes?style=Mexican'); ?>">Mexican</a>
                <a href="<?php echo url_for('/recipes?style=Asian'); ?>">Asian</a>
                <a href="<?php echo url_for('/recipes?style=American'); ?>">American</a>
            </nav>
        </div>

        <div class="footer-section">
            <h3>Connect With Us</h3>
            <div class="social-links">
                <a href="#" class="social-link" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-link" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link" title="Pinterest">
                    <i class="fab fa-pinterest-p"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo $current_year; ?> Flavor Connect. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
