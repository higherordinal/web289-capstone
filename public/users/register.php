<?php
require_once('../private/initialize.php');
$page_title = 'Sign Up';

// Initialize variables
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = '';
$confirm_password = '';
$errors = [];

if(is_post_request()) {
    // Get form values
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate username
    if(empty($username)) {
        $errors[] = "Username cannot be blank.";
    } elseif(strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    } elseif(strlen($username) > 30) {
        $errors[] = "Username cannot exceed 30 characters.";
    } elseif(!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, underscores, and hyphens.";
    } elseif(User::find_by_username($username)) {
        $errors[] = "Username is already taken.";
    }
    
    // Validate email
    if(empty($email)) {
        $errors[] = "Email cannot be blank.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email format is invalid.";
    } elseif(User::find_by_email($email)) {
        $errors[] = "Email is already registered.";
    }
    
    // Validate password
    if(empty($password)) {
        $errors[] = "Password cannot be blank.";
    } elseif(strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    } elseif(!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    } elseif(!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    } elseif(!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    } elseif(!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character.";
    }
    
    // Validate password confirmation
    if($password !== $confirm_password) {
        $errors[] = "Password confirmation does not match.";
    }
    
    if(empty($errors)) {
        // Create new user
        $user = new User([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'user_type' => 'user',
            'status' => 'active'
        ]);
        
        if($user->save()) {
            // Log the user in
            $session->login($user);
            $session->message('Welcome to Flavor Connect! Your account has been created successfully.');
            redirect_to(url_for('/recipes/index.php'));
        } else {
            $errors = array_merge($errors, $user->errors);
        }
    }
}

include(SHARED_PATH . '/header.php');
?>

<div class="auth-container">
    <h1>Create Account</h1>
    
    <?php echo display_errors($errors); ?>
    <?php echo display_session_message(); ?>
    
    <form action="<?php echo url_for('/users/register.php'); ?>" method="POST" class="auth-form" id="registerForm">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo h($username); ?>" 
                   required minlength="3" maxlength="30" pattern="[a-zA-Z0-9_-]+"
                   title="Username can only contain letters, numbers, underscores, and hyphens">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo h($email); ?>" 
                   required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-group">
                <input type="password" id="password" name="password" required
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$">
                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="password-strength-meter">
                <div id="strengthMeter"></div>
            </div>
            <div class="password-requirements">
                Password must contain:
                <ul>
                    <li id="length">At least 8 characters</li>
                    <li id="uppercase">At least one uppercase letter</li>
                    <li id="lowercase">At least one lowercase letter</li>
                    <li id="number">At least one number</li>
                    <li id="special">At least one special character</li>
                </ul>
            </div>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="password-group">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>
        
        <button type="submit" class="auth-button" id="registerButton" disabled>Create Account</button>
    </form>
    
    <div class="auth-links">
        Already have an account? <a href="<?php echo url_for('/users/login.php'); ?>">Log In</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const registerButton = document.getElementById('registerButton');
    const strengthMeter = document.getElementById('strengthMeter');
    
    // Password requirements elements
    const requirements = {
        length: document.getElementById('length'),
        uppercase: document.getElementById('uppercase'),
        lowercase: document.getElementById('lowercase'),
        number: document.getElementById('number'),
        special: document.getElementById('special')
    };
    
    function updatePasswordStrength(password) {
        let strength = 0;
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };
        
        // Update requirement indicators
        Object.keys(checks).forEach(key => {
            if(checks[key]) {
                requirements[key].classList.add('valid');
                requirements[key].classList.remove('invalid');
                strength++;
            } else {
                requirements[key].classList.add('invalid');
                requirements[key].classList.remove('valid');
            }
        });
        
        // Update strength meter
        strengthMeter.className = '';
        if(strength < 2) strengthMeter.classList.add('strength-weak');
        else if(strength < 3) strengthMeter.classList.add('strength-fair');
        else if(strength < 5) strengthMeter.classList.add('strength-good');
        else strengthMeter.classList.add('strength-strong');
        
        // Enable/disable register button
        registerButton.disabled = !Object.values(checks).every(Boolean) || 
                                !confirmInput.value || 
                                passwordInput.value !== confirmInput.value;
    }
    
    passwordInput.addEventListener('input', () => {
        updatePasswordStrength(passwordInput.value);
    });
    
    confirmInput.addEventListener('input', () => {
        updatePasswordStrength(passwordInput.value);
    });
});

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