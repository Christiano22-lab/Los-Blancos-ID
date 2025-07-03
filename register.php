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
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!empty($name) && !empty($email) && !empty($password)) {
        if ($password === $confirm_password) {
            $email_escaped = db_escape($email);
            $check_query = "SELECT * FROM users WHERE email = '$email_escaped' LIMIT 1";
            $result = db_query($check_query);
            
            if (db_num_rows($result) == 0) {
                $name_escaped = db_escape($name);
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $insert_query = "INSERT INTO users (name, email, password, role, created_at) 
                               VALUES ('$name_escaped', '$email_escaped', '$hashed_password', 'user', NOW())";
                
                if (db_query($insert_query)) {
                    $success = 'Registrasi berhasil! Silakan login dengan akun Anda.';
                } else {
                    $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
                }
            } else {
                $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
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
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="name">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Nama Lengkap
                    </label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        Email
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        Password
                    </label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        Konfirmasi Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn">
                    <svg class="icon" viewBox="0 0 24 24">
                        <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Daftar
                </button>
            </form>
            
            <div class="auth-links">
                <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                <p><a href="index.php">← Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
</body>
</html>
