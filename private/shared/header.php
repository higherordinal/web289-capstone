<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlavorConnect<?php if(isset($page_title)) { echo ' - ' . h($page_title); } ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo url_for('/favicon.ico'); ?>" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Base Styles -->
    <link rel="stylesheet" href="<?php echo url_for('/css/base.css'); ?>">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="<?php echo url_for('/css/components/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo url_for('/css/components/footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo url_for('/css/components/forms.css'); ?>">
    <link rel="stylesheet" href="<?php echo url_for('/css/components/auth.css'); ?>">
    
    <!-- Page-Specific Styles -->
    <?php if(isset($page_style)): ?>
    <link rel="stylesheet" href="<?php echo url_for('/css/pages/' . $page_style . '.css'); ?>">
    <?php endif; ?>
    
    <?php if(isset($use_admin_styles) && $use_admin_styles): ?>
    <link rel="stylesheet" href="<?php echo url_for('/css/pages/admin.css'); ?>">
    <?php endif; ?>
</head>
<body>
<?php
// Debug information
if(!isset($session)) {
    error_log("Session object not found in header.php");
    $session = new Session();
}
?>
<div class="main-content">
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo url_for('/'); ?>" class="logo-link">
                    <i class="fas fa-utensils logo-icon"></i>
                    <span class="logo-text">Flavor<span class="accent">Connect</span></span>
                </a>
            </div>

            <form action="<?php echo url_for('/recipes/index.php'); ?>" method="get" class="search-bar">
                <input type="text" name="search" placeholder="Search recipes..." 
                       value="<?php echo isset($_GET['search']) ? h($_GET['search']) : ''; ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo url_for('/'); ?>"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo url_for('/recipes'); ?>"><i class="fas fa-utensils"></i> Recipes</a></li>
                    
                    <?php if($session->is_logged_in()) { ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-book"></i> My Recipes <i class="fas fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url_for('/recipes/new.php'); ?>">Add Recipe</a></li>
                                <li><a href="<?php echo url_for('/recipes/my_recipes.php'); ?>">My Recipes</a></li>
                                <?php if($session->is_admin()) { ?>
                                    <li><a href="<?php echo url_for('/admin/recipes/index.php'); ?>">Manage Recipes</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        
                        <?php if($session->is_admin()) { ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle">
                                    <i class="fas fa-cog"></i> Admin <i class="fas fa-caret-down"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo url_for('/admin/users/index.php'); ?>">Manage Users</a></li>
                                    <li><a href="<?php echo url_for('/admin/recipe_attributes/index.php'); ?>">Recipe Attributes</a></li>
                                </ul>
                            </li>
                        <?php } ?>
                        
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-user"></i> <?php echo h($session->get_username()); ?> <i class="fas fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url_for('/users/profile.php'); ?>">Profile</a></li>
                                <li><a href="<?php echo url_for('/users/logout.php'); ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li><a href="<?php echo url_for('/users/login.php'); ?>" class="btn-primary"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="<?php echo url_for('/users/register.php'); ?>" class="btn-secondary"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <?php echo display_session_message(); ?>
    
    <main>
        <div class="container">
            <?php echo display_session_message(); ?>
