<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/config.php';
require_once 'includes/db.php';

$page_title = "Profil";

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Ambil data user
$user_id = (int)$_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = db_query($query);
$user = db_fetch_array($result);

// Update profile
if ($_POST) {
    $name = sanitize_input($_POST['name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if username is taken by another user
    $username_escaped = db_escape($username);
    $check_username_query = "SELECT id FROM users WHERE username = '$username_escaped' AND id != $user_id LIMIT 1";
    $username_result = db_query($check_username_query);
    
    // Check if email is taken by another user
    $email_escaped = db_escape($email);
    $check_email_query = "SELECT id FROM users WHERE email = '$email_escaped' AND id != $user_id LIMIT 1";
    $email_result = db_query($check_email_query);

    if (db_num_rows($username_result) > 0) {
        $error = 'Username sudah digunakan oleh pengguna lain!';
    } elseif (db_num_rows($email_result) > 0) {
        $error = 'Email sudah digunakan oleh pengguna lain!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username hanya boleh mengandung huruf, angka, dan underscore!';
    } else {
        // Validasi jika ganti password
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $error = 'Masukkan password lama untuk mengganti password!';
            } elseif (!password_verify($current_password, $user['password'])) {
                $error = 'Password lama salah!';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Password baru dan konfirmasi tidak cocok!';
            } elseif (strlen($new_password) < 6) {
                $error = 'Password baru minimal 6 karakter!';
            } else {
                // Update dengan password baru
                $name_escaped = db_escape($name);
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $update_query = "UPDATE users SET name = '$name_escaped', username = '$username_escaped', email = '$email_escaped', password = '$hashed_password' WHERE id = $user_id";
                
                if (db_query($update_query)) {
                    $message = 'Profil dan password berhasil diperbarui!';
                    // Refresh user data
                    $result = db_query("SELECT * FROM users WHERE id = $user_id LIMIT 1");
                    $user = db_fetch_array($result);
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['username'] = $user['username'];
                } else {
                    $error = 'Gagal memperbarui profil!';
                }
            }
        } else {
            // Update tanpa ganti password
            $name_escaped = db_escape($name);
            
            $update_query = "UPDATE users SET name = '$name_escaped', username = '$username_escaped', email = '$email_escaped' WHERE id = $user_id";
            
            if (db_query($update_query)) {
                $message = 'Profil berhasil diperbarui!';
                // Refresh user data
                $result = db_query("SELECT * FROM users WHERE id = $user_id LIMIT 1");
                $user = db_fetch_array($result);
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['username'] = $user['username'];
            } else {
                $error = 'Gagal memperbarui profil!';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<main class="profile-main">
    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-title">
                    <h1>Profil Saya</h1>
                    <p>Kelola informasi akun Anda</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="profile-content">
                <div class="profile-info">
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle"></i> Informasi Akun</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Username:</label>
                                <span>@<?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Role:</label>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <label>Bergabung:</label>
                                <span><?php echo date('d F Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Status:</label>
                                <span class="status-badge status-active">
                                    <i class="fas fa-circle"></i> Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-form">
                    <div class="form-card">
                        <h3><i class="fas fa-edit"></i> Edit Profil</h3>
                        <form method="POST" class="edit-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fas fa-user"></i>
                                        Nama Lengkap
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" 
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="username">
                                        <i class="fas fa-at"></i>
                                        Username
                                    </label>
                                    <input type="text" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user['username']); ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       required>
                            </div>

                            <div class="password-section">
                                <h4><i class="fas fa-key"></i> Ganti Password (Opsional)</h4>
                                <div class="password-note">
                                    <i class="fas fa-info-circle"></i>
                                    Kosongkan jika tidak ingin mengubah password
                                </div>

                                <div class="form-group">
                                    <label for="current_password">Password Lama</label>
                                    <div class="password-input">
                                        <input type="password" id="current_password" name="current_password">
                                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye" id="current_password-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="new_password">Password Baru</label>
                                        <div class="password-input">
                                            <input type="password" id="new_password" name="new_password">
                                            <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                                <i class="fas fa-eye" id="new_password-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password">Konfirmasi Password Baru</label>
                                        <div class="password-input">
                                            <input type="password" id="confirm_password" name="confirm_password">
                                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                                <i class="fas fa-eye" id="confirm_password-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Simpan Perubahan
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

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

    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = this.value;
        
        if (confirmPassword.length > 0 && newPassword !== confirmPassword) {
            this.setCustomValidity('Password tidak cocok');
        } else {
            this.setCustomValidity('');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
