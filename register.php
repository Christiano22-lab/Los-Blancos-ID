<?php
$page_title = "Sign Up";
$page_description = "Create your Los Blancos ID account";
$current_page = "register";

require_once 'includes/db.php';

// Check if user is already logged in
if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Check if email already exists
        $email_check = db_escape($email);
        $query = "SELECT * FROM users WHERE email = '$email_check' LIMIT 1";
        $result = db_query($query);
        
        if (db_num_rows($result) > 0) {
            $error = "Email address is already registered.";
        } else {
            // Register the user
            $user_id = register_user($name, $email, $password);
            
            if ($user_id) {
                // Auto login after registration
                login_user($email, $password);
                
                $_SESSION['message'] = "Registration successful! Welcome to Los Blancos ID.";
                $_SESSION['message_type'] = "success";
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Create Account</h1>
                <p>Join the Los Blancos ID community today!</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form action="register.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small>Password must be at least 8 characters long</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group terms">
                    <label>
                        <input type="checkbox" name="terms" required> I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-block">Create Account</button>
            </form>
            
            <div class="auth-divider">
                <span>OR</span>
            </div>
            
            <div class="social-auth">
                <button class="btn btn-social btn-google">
                    <i class="fab fa-google"></i> Sign up with Google
                </button>
                <button class="btn btn-social btn-facebook">
                    <i class="fab fa-facebook-f"></i> Sign up with Facebook
                </button>
            </div>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>