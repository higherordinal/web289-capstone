<?php
// Get current year for copyright
$current_year = date('Y');
?>
</main>
</div><!-- /.main-content -->
    
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About FlavorConnect</h3>
                <p>Share and discover amazing recipes from around the world. Join our community of food lovers!</p>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo url_for('/recipes'); ?>">Browse Recipes</a></li>
                    <li><a href="<?php echo url_for('/recipes/new.php'); ?>">Submit Recipe</a></li>
                    <li><a href="<?php echo url_for('/about.php'); ?>">About Us</a></li>
                    <li><a href="<?php echo url_for('/contact.php'); ?>">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Categories</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo url_for('/recipes?type=1'); ?>">Breakfast</a></li>
                    <li><a href="<?php echo url_for('/recipes?type=2'); ?>">Lunch</a></li>
                    <li><a href="<?php echo url_for('/recipes?type=3'); ?>">Dinner</a></li>
                    <li><a href="<?php echo url_for('/recipes?type=4'); ?>">Desserts</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo $current_year; ?> FlavorConnect. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Utility Scripts -->
<script src="<?php echo url_for('/js/utils/helpers.js'); ?>"></script>
    
<!-- Component Scripts -->
<?php if(isset($page_scripts)): ?>
    <?php foreach($page_scripts as $script): ?>
    <script src="<?php echo url_for('/js/' . $script . '.js'); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
