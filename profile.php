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

    // Handle profile image upload
    $profile_image = $user['profile_image']; // Keep current image by default
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_info = pathinfo($_FILES['profile_image']['name']);
        $file_extension = strtolower($file_info['extension']);
        
        if (in_array($file_extension, $allowed_types)) {
            $file_size = $_FILES['profile_image']['size'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if ($file_size <= $max_size) {
                // Create upload directory if it doesn't exist
                $upload_dir = 'assets/images/profiles/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    // Delete old profile image if it exists and is not default
                    if ($user['profile_image'] && $user['profile_image'] != 'assets/images/user-image.png' && file_exists($user['profile_image'])) {
                        unlink($user['profile_image']);
                    }
                    $profile_image = $upload_path;
                } else {
                    $error = 'Gagal mengupload foto profil!';
                }
            } else {
                $error = 'Ukuran foto terlalu besar! Maksimal 2MB.';
            }
        } else {
            $error = 'Format foto tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.';
        }
    }

    // Continue with validation if no image upload error
    if (empty($error)) {
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
                    $profile_image_escaped = db_escape($profile_image);
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $update_query = "UPDATE users SET name = '$name_escaped', username = '$username_escaped', email = '$email_escaped', password = '$hashed_password', profile_image = '$profile_image_escaped' WHERE id = $user_id";
                    
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
                $profile_image_escaped = db_escape($profile_image);
                
                $update_query = "UPDATE users SET name = '$name_escaped', username = '$username_escaped', email = '$email_escaped', profile_image = '$profile_image_escaped' WHERE id = $user_id";
                
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
}

// Set default profile image if empty
$profile_image_path = $user['profile_image'] ?? '';

$profile_image_src = (!empty($profile_image_path) && file_exists($profile_image_path))
    ? $profile_image_path
    : 'assets/images/user-image.jpg';


?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<main class="profile-main">
    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="<?php echo htmlspecialchars($profile_image_src); ?>" 
                         alt="Profile Image" 
                         id="profile-preview"
                         onerror="this.src='assets/images/user-image.png'">
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
                        <form method="POST" class="edit-form" enctype="multipart/form-data">
                            
                            <!-- Profile Image Upload Section -->
                            <div class="form-group profile-image-section">
                                <label for="profile_image">
                                    <i class="fas fa-camera"></i>
                                    Foto Profil
                                </label>
                                <div class="image-upload-container">
                                    <div class="current-image">
                                        <img src="<?php echo htmlspecialchars($profile_image_src); ?>" 
                                             alt="Current Profile" 
                                             id="current-profile-preview"
                                             onerror="this.src='assets/images/user-image.png'">
                                    </div>
                                    <div class="upload-controls">
                                        <input type="file" 
                                               id="profile_image" 
                                               name="profile_image" 
                                               accept="image/*"
                                               onchange="previewImage(this)">
                                        <label for="profile_image" class="upload-btn">
                                            <i class="fas fa-upload"></i>
                                            Pilih Foto
                                        </label>
                                        <small class="upload-note">
                                            Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                                        </small>
                                    </div>
                                </div>
                            </div>

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

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('current-profile-preview').src = e.target.result;
                document.getElementById('profile-preview').src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
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

<style>
/* Additional CSS for profile image upload */
.profile-image-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    border: 2px dashed #ddd;
    border-radius: 10px;
    background: #f9f9f9;
}

.image-upload-container {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-top: 1rem;
}

.current-image img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.upload-controls {
    flex: 1;
}

.upload-controls input[type="file"] {
    display: none;
}

.upload-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #007bff;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
    text-decoration: none;
}

.upload-btn:hover {
    background: #0056b3;
}

.upload-btn i {
    margin-right: 0.5rem;
}

.upload-note {
    display: block;
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.85rem;
}

.profile-avatar img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-avatar i {
    font-size: 80px;
    color: #ddd;
}

@media (max-width: 768px) {
    .image-upload-container {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .current-image img {
        width: 80px;
        height: 80px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
