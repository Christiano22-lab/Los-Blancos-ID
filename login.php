<?php
<<<<<<< HEAD
// Include semua file yang diperlukan
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect jika sudah login
if (is_logged_in()) {
    header("Location: index.php");
    exit();
=======
$page_title = "Sign In";
$page_description = "Sign in to your Los Blancos ID account";
$current_page = "login";

require_once 'includes/db.php';

// Check if user is already logged in
if (is_logged_in()) {
    header("Location: index.php");
    exit;
>>>>>>> 3f9dcde68acb56b96a6b5a0664e4b76626655a50
}

$error = '';

<<<<<<< HEAD
if ($_POST) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    if (!empty($email) && !empty($password)) 
    {
        if (login_user($email, $password)) 
        {
            $_SESSION['message'] = 'Login berhasil! Selamat datang ' . $_SESSION['user_name'];
            $_SESSION['message_type'] = 'success';
            header("Location: index.php");
            exit();
        } else 
        {
            $error = 'Email atau password salah!';
        }
    } else {
        $error = 'Harap isi semua field!';
    }
}




?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Los Blancos ID</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Los Blancos ID</h1>
                <h2>Masuk ke Akun Anda</h2>
                <p>Bergabunglah dengan komunitas Madridista terbesar di Indonesia</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        Email
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required autocomplete="email">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        Password
                    </label>
                    <input type="password" id="password" name="password" 
                           required autocomplete="current-password">
                </div>
                
                <button type="submit" class="btn">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/>
                    </svg>
                    Masuk
                </button>
            </form>
            <div class="auth-links">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                <p><a href="index.php">← Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
</body>
</html>
=======
// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        if (login_user($email, $password)) {
            // Redirect to intended page or home
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
            unset($_SESSION['redirect_after_login']);
            
            header("Location: $redirect");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Sign In</h1>
                <p>Welcome back! Sign in to access your account.</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group remember-me">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-block">Sign In</button>
            </form>
            
            <div class="auth-divider">
                <span>OR</span>
            </div>
            
            <div class="social-auth">
                <button class="btn btn-social btn-google">
                    <i class="fab fa-google"></i> Sign in with Google
                </button>
                <button class="btn btn-social btn-facebook">
                    <i class="fab fa-facebook-f"></i> Sign in with Facebook
                </button>
            </div>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
>>>>>>> 3f9dcde68acb56b96a6b5a0664e4b76626655a50
