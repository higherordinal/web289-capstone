<?php
require_once('../../private/initialize.php');
$page_title = 'Log In';

// Redirect if already logged in
if($session->is_logged_in()) {
    redirect_to(url_for('/recipes/index.php'));
}

// Initialize variables
$username = '';
$errors = [];

if(is_post_request()) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validate presence
    if(empty($username)) {
        $errors[] = "Username or email cannot be blank.";
    }
    if(empty($password)) {
        $errors[] = "Password cannot be blank.";
    }
    
    if(empty($errors)) {
        // Check if input is email or username
        $user = filter_var($username, FILTER_VALIDATE_EMAIL) 
                ? User::find_by_email($username)
                : User::find_by_username($username);
        
        if($user && $user->verify_password($password)) {
            if($user->status === 'inactive') {
                $errors[] = "Your account has been deactivated. Please contact support.";
            } else {
                // Set remember me cookie if requested
                if($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    $user->remember_token = $token;
                    $user->save();
                    
                    setcookie('remember_token', $token, $expiry, '/', '', true, true);
                    setcookie('remember_user', $user->id, $expiry, '/', '', true, true);
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

include(SHARED_PATH . '/header.php');
?>

<link rel="stylesheet" href="<?php echo url_for('/css/auth.css'); ?>">

<div class="auth-page">
    <div class="auth-container">
        <h1>Welcome Back</h1>
        
        <?php echo display_errors($errors); ?>
        <?php echo display_session_message(); ?>
        
        <form action="<?php echo url_for('/users/login.php'); ?>" method="POST" class="auth-form">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" value="<?php echo h($username); ?>" 
                       required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-group">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" id="remember">
                    Remember me
                </label>
            </div>
            
            <button type="submit" class="auth-button">Log In</button>
        </form>
        
        <div class="auth-links">
            <p><a href="<?php echo url_for('/users/forgot_password.php'); ?>">Forgot Password?</a></p>
            <p>Don't have an account? <a href="<?php echo url_for('/users/register.php'); ?>">Sign Up</a></p>
        </div>
        
        <div class="social-login">
            <a href="<?php echo url_for('/auth/google'); ?>" class="social-button google-button">
                <i class="fab fa-google"></i> Continue with Google
            </a>
            <a href="<?php echo url_for('/auth/facebook'); ?>" class="social-button facebook-button">
                <i class="fab fa-facebook"></i> Continue with Facebook
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if(input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include(SHARED_PATH . '/footer.php'); ?>