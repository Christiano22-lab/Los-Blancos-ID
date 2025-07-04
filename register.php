<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_POST) {
    $name = sanitize_input($_POST['name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!empty($name) && !empty($username) && !empty($email) && !empty($password)) {
        if ($password === $confirm_password) {
            // Check if email already exists
            $email_escaped = db_escape($email);
            $check_email_query = "SELECT * FROM users WHERE email = '$email_escaped' LIMIT 1";
            $email_result = db_query($check_email_query);
            
            // Check if username already exists
            $username_escaped = db_escape($username);
            $check_username_query = "SELECT * FROM users WHERE username = '$username_escaped' LIMIT 1";
            $username_result = db_query($check_username_query);
            
            if (db_num_rows($email_result) > 0) {
                $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
            } elseif (db_num_rows($username_result) > 0) {
                $error = 'Username sudah digunakan. Silakan pilih username lain.';
            } elseif (strlen($username) < 3) {
                $error = 'Username minimal 3 karakter.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $error = 'Username hanya boleh mengandung huruf, angka, dan underscore.';
            } else {
                $name_escaped = db_escape($name);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_query = "INSERT INTO users (name, username, email, password, role, created_at) 
                               VALUES ('$name_escaped', '$username_escaped', '$email_escaped', '$hashed_password', 'user', NOW())";
                
                if (db_query($insert_query)) {
                    $success = 'Registrasi berhasil! Silakan login dengan username atau email Anda.';
                } else {
                    $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
                }
            }
        } else {
            $error = 'Password dan konfirmasi password tidak cocok!';
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
    <title>Daftar - Los Blancos ID</title>
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Los Blancos ID</h1>
                <h2>Daftar Akun Baru</h2>
                <p>Bergabunglah dengan komunitas Madridista terbesar di Indonesia</p>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name">
                        <i class="fas fa-user"></i>
                        Nama Lengkap
                    </label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-at"></i>
                        Username
                    </label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                           placeholder="Minimal 3 karakter, hanya huruf, angka, dan underscore"
                           required>
                    <small class="form-help">Username akan digunakan untuk login dan profil publik Anda</small>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i>
                        Email
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i>
                        Konfirmasi Password
                    </label>
                    <div class="password-input">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye" id="confirm_password-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-user-plus"></i>
                    Daftar
                </button>
            </form>
            
            <div class="auth-links">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
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

        // Username validation
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const regex = /^[a-zA-Z0-9_]+$/;
            
            if (username.length > 0 && !regex.test(username)) {
                this.setCustomValidity('Username hanya boleh mengandung huruf, angka, dan underscore');
            } else if (username.length > 0 && username.length < 3) {
                this.setCustomValidity('Username minimal 3 karakter');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
