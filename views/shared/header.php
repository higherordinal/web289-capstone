<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavor Connect<?php if(isset($page_title)) { echo ' - ' . h($page_title); } ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo url_for('/favicon.ico'); ?>" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <!-- CSS Stylesheet -->
    <link rel="stylesheet" href="<?php echo url_for('/css/styles.css'); ?>?v=5">
    <link rel="stylesheet" href="<?php echo url_for('/css/recipe-gallery.css'); ?>?v=2">
    <link rel="stylesheet" href="<?php echo url_for('/css/auth.css'); ?>?v=1">
    <?php if(isset($use_admin_styles) && $use_admin_styles): ?>
    <link rel="stylesheet" href="<?php echo url_for('/css/admin.css'); ?>?v=1">
    <?php endif; ?>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="main-content">
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo url_for('/'); ?>" class="logo-link">
                    <span class="logo-icon"><i class="fas fa-mortar-pestle"></i></span>
                    <span class="logo-text">Flavor<span class="accent">Connect</span></span>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo url_for('/'); ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo url_for('/recipes'); ?>"><i class="fas fa-utensils"></i> Recipes</a></li>
                    
                    <?php if($session->is_logged_in()) { ?>
                        <!-- Dropdown for recipe actions -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-book"></i> My Recipes <i class="fas fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url_for('/recipes/new.php'); ?>">Add Recipe</a></li>
                                <li><a href="<?php echo url_for('/recipes/my_recipes.php'); ?>">My Recipes</a></li>
                                <li><a href="<?php echo url_for('/recipes/favorites.php'); ?>">My Favorites</a></li>
                            </ul>
                        </li>

                        <?php if($session->is_super_admin()) { ?>
                            <!-- Super Admin Menu -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <i class="fas fa-shield-alt"></i> Super Admin <i class="fas fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo url_for('/users/admin_management.php'); ?>">Manage Admins</a></li>
                                    <li><a href="<?php echo url_for('/users/user_management.php'); ?>">Manage Users</a></li>
                                    <li><a href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Recipe Metadata</a></li>
                                    <li><a href="<?php echo url_for('/admin/site_settings.php'); ?>">Site Settings</a></li>
                                </ul>
                            </li>
                        <?php } elseif($session->is_admin()) { ?>
                            <!-- Regular Admin Menu -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <i class="fas fa-user-shield"></i> Admin <i class="fas fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo url_for('/users/user_management.php'); ?>">Manage Users</a></li>
                                    <li><a href="<?php echo url_for('/admin/recipe_metadata.php'); ?>">Recipe Metadata</a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <!-- User Profile Menu -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-user-circle"></i> <?php echo h($session->username); ?> <i class="fas fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url_for('/users/profile.php'); ?>">My Profile</a></li>
                                <li><a href="<?php echo url_for('/users/settings.php'); ?>">Settings</a></li>
                                <li><a href="<?php echo url_for('/users/logout.php'); ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li><a href="<?php echo url_for('/users/login.php'); ?>" class="btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="<?php echo url_for('/users/register.php'); ?>" class="btn-secondary"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Add dropdown functionality -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdowns.forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('active');
                    }
                });
                dropdown.classList.toggle('active');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('active');
                });
            }
        });
    });
    </script>

    <main>
        <div class="container">
            <?php echo display_session_message(); ?>
