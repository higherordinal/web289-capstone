<?php
require_once('../../private/initialize.php');

$page_title = 'Login';
$page_style = 'auth';
include(SHARED_PATH . '/header.php');

$errors = [];
$username = '';
$password = '';

if(is_post_request()) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validations
    if(is_blank($username)) {
        $errors['username'] = "Username cannot be blank.";
    }
    if(is_blank($password)) {
        $errors['password'] = "Password cannot be blank.";
    }
    
    // if there were no errors, try to login
    if(empty($errors)) {
        // Check if input is email or username
        $user = filter_var($username, FILTER_VALIDATE_EMAIL) 
                ? User::find_by_email($username)
                : User::find_by_username($username);
        
        if($user && $user->verify_password($password)) {
            if($user->is_active === false) {
                $errors[] = "Your account has been deactivated. Please contact support.";
            } else {
                // Set remember me cookie if requested
                if($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    $user->remember_token = $token;
                    $user->save();
                    
                    setcookie('remember_token', $token, $expiry, '/', '', true, true);
                    setcookie('remember_user', $user->user_id, $expiry, '/', '', true, true);
                }
                
                // Log the user in
                $session->login($user);
                
                // Redirect to intended page or default
                $redirect_to = $_SESSION['intended_url'] ?? url_for('/recipes/index.php');
                unset($_SESSION['intended_url']);
                
                redirect_to($redirect_to);
            }
        } else {
            // Use vague error message for security
            $errors[] = "Invalid username/email or password.";
            
            // Log failed attempt
            error_log("Failed login attempt for username: " . h($username) . " from IP: " . h($_SERVER['REMOTE_ADDR']));
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-card" role="main">
        <div class="auth-header">
            <h1 id="login-title">Welcome Back</h1>
            <p>Sign in to continue to FlavorConnect</p>
        </div>
        
        <div class="auth-body">
            <?php if(!empty($errors)) { ?>
                <div class="alert alert-danger" role="alert" aria-live="polite">
                    <?php if(isset($errors['login'])) { ?>
                        <p><?php echo $errors['login']; ?></p>
                    <?php } else { ?>
                        <p>Please fix the following errors:</p>
                        <ul>
                            <?php foreach($errors as $error) { ?>
                                <li><?php echo $error; ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            <?php } ?>

            <form action="<?php echo url_for('/users/login.php'); ?>" method="post" class="auth-form" 
                  aria-labelledby="login-title">
                <div class="form-group">
                    <label for="username" class="form-label">
                        Username or Email
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>"
                           id="username" name="username" value="<?php echo h($username); ?>" 
                           required aria-required="true"
                           aria-describedby="username-help username-error"
                           placeholder="Enter your username or email">
                    <small id="username-help" class="form-text">Enter your username or email address to log in</small>
                    <?php if(isset($errors['username'])) { ?>
                        <div id="username-error" class="invalid-feedback"><?php echo $errors['username']; ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>"
                           id="password" name="password" 
                           required aria-required="true"
                           aria-describedby="password-help password-error"
                           placeholder="Enter your password">
                    <small id="password-help" class="form-text">Enter your account password</small>
                    <?php if(isset($errors['password'])) { ?>
                        <div id="password-error" class="invalid-feedback"><?php echo $errors['password']; ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn-auth">Sign In</button>
                </div>

                <div class="auth-links">
                    <a href="<?php echo url_for('/users/forgot_password.php'); ?>" class="auth-link">Forgot Password?</a>
                    <a href="<?php echo url_for('/users/register.php'); ?>" class="auth-link">Create Account</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(SHARED_PATH . '/footer.php'); ?>