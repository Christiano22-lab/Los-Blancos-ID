<?php
// Include semua file yang diperlukan
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect jika sudah login
if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_POST) {
    $login_input = sanitize_input($_POST['login_input']); // Could be email or username
    $password = $_POST['password'];
    
    if (!empty($login_input) && !empty($password)) {
        if (login_user_with_username_or_email($login_input, $password)) {
            $_SESSION['message'] = 'Login berhasil! Selamat datang ' . $_SESSION['user_name'];
            $_SESSION['message_type'] = 'success';
            header("Location: index.php");
            exit();
        } else {
            $error = 'Username/Email atau password salah!';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="login_input">
                        <i class="fas fa-user"></i>
                        Username atau Email
                    </label>
                    <input type="text" id="login_input" name="login_input" 
                           value="<?php echo isset($_POST['login_input']) ? htmlspecialchars($_POST['login_input']) : ''; ?>" 
                           placeholder="Masukkan username atau email Anda"
                           required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" 
                               required autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk
                </button>
            </form>
            
            <div class="auth-links">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                <p><a href="index.php">← Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
