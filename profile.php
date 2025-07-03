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
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile
if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi jika ganti password
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $error = 'Masukkan password lama untuk mengganti password!';
        } elseif (md5($current_password) !== $user['password']) {
            $error = 'Password lama salah!';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Password baru dan konfirmasi tidak cocok!';
        } else {
            // Update dengan password baru
            $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email, password = MD5(:password) WHERE id = :id');
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $new_password);
            $stmt->bindValue(':id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                $message = 'Profil berhasil diperbarui!';
                // Refresh user data
                $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
                $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Gagal memperbarui profil!';
            }
        }
    } else {
        // Update tanpa ganti password
        $stmt = $pdo->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            $message = 'Profil berhasil diperbarui!';
            // Refresh user data
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Gagal memperbarui profil!';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/profile.css">

<main class="container">
    <div class="profile-container">
        <div class="profile-header">
            <h2>Profil Saya</h2>
            <p>Kelola informasi akun Anda</p>
        </div>

        <?php if ($message): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="profile-content">
            <div class="profile-info">
                <h3>Informasi Akun</h3>
                <div class="info-item">
                    <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?>
                </div>
                <div class="info-item">
                    <strong>Role:</strong> <?php echo ucfirst($user['role']); ?>
                </div>
                <div class="info-item">
                    <strong>Bergabung:</strong> <?php echo date('d F Y', strtotime($user['created_at'])); ?>
                </div>
            </div>

            <div class="profile-form">
                <h3>Edit Profil</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap:</label>
                        <input type="text" id="name" name="name" value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <hr>
                    <h4>Ganti Password (Opsional)</h4>

                    <div class="form-group">
                        <label for="current_password">Password Lama:</label>
                        <input type="password" id="current_password" name="current_password">
                    </div>

                    <div class="form-group">
                        <label for="new_password">Password Baru:</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru:</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>

                    <button type="submit" class="btn-update">Perbarui Profil</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
