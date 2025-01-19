<?php
require_once('../../private/initialize.php');

$page_title = 'Register';
$page_style = 'auth';
include(SHARED_PATH . '/header.php');

$errors = [];
$user = new User([
    'username' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => ''
]);

if(is_post_request()) {
    $args = $_POST['user'];
    $user->merge_attributes($args);
    
    $result = $user->save();
    if($result === true) {
        $session->message('Registration successful! Please log in.');
        redirect_to(url_for('/users/login.php'));
    } else {
        $errors = $user->errors;
    }
}
?>

<div class="auth-container">
    <div class="auth-card" role="main">
        <div class="auth-header">
            <h1 id="register-title">Create Account</h1>
            <p>Join FlavorConnect and start sharing your recipes</p>
        </div>
        
        <div class="auth-body">
            <?php if(!empty($errors)) { ?>
                <div class="alert alert-danger" role="alert" aria-live="polite">
                    <p>Please fix the following errors:</p>
                    <ul>
                        <?php foreach($errors as $error) { ?>
                            <li><?php echo $error; ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <form action="<?php echo url_for('/users/register.php'); ?>" method="post" class="auth-form" 
                  aria-labelledby="register-title">
                <div class="form-group">
                    <label for="username" class="form-label">
                        Username
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input type="text" class="form-control <?php echo isset($user->errors['username']) ? 'is-invalid' : ''; ?>" 
                           id="username" name="user[username]" value="<?php echo h($user->username); ?>"
                           required aria-required="true"
                           aria-describedby="username-help username-error"
                           placeholder="Choose a unique username">
                    <small id="username-help" class="form-text">Username must be between 3 and 50 characters</small>
                    <?php if(isset($user->errors['username'])) { ?>
                        <div id="username-error" class="invalid-feedback"><?php echo $user->errors['username']; ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input type="email" class="form-control <?php echo isset($user->errors['email']) ? 'is-invalid' : ''; ?>" 
                           id="email" name="user[email]" value="<?php echo h($user->email); ?>"
                           required aria-required="true"
                           aria-describedby="email-help email-error"
                           placeholder="Enter your email address">
                    <small id="email-help" class="form-text">We'll never share your email with anyone else</small>
                    <?php if(isset($user->errors['email'])) { ?>
                        <div id="email-error" class="invalid-feedback"><?php echo $user->errors['email']; ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input type="password" class="form-control <?php echo isset($user->errors['password']) ? 'is-invalid' : ''; ?>" 
                           id="password" name="user[password]"
                           required aria-required="true"
                           aria-describedby="password-help password-error"
                           placeholder="Create a strong password">
                    <small id="password-help" class="form-text">Password must be at least 8 characters and include uppercase, lowercase, and numbers</small>
                    <?php if(isset($user->errors['password'])) { ?>
                        <div id="password-error" class="invalid-feedback"><?php echo $user->errors['password']; ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        Confirm Password
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input type="password" class="form-control <?php echo isset($user->errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                           id="confirm_password" name="user[confirm_password]"
                           required aria-required="true"
                           aria-describedby="confirm-password-help confirm-password-error"
                           placeholder="Confirm your password">
                    <small id="confirm-password-help" class="form-text">Re-enter your password to confirm</small>
                    <?php if(isset($user->errors['confirm_password'])) { ?>
                        <div id="confirm-password-error" class="invalid-feedback"><?php echo $user->errors['confirm_password']; ?></div>
                    <?php } ?>
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn-auth">Create Account</button>
                </div>

                <div class="auth-links">
                    <p>Already have an account? <a href="<?php echo url_for('/users/login.php'); ?>" class="auth-link">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>