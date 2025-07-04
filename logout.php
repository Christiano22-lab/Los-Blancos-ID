<?php
session_start();

// Cek jika ada konfirmasi logout
if (isset($_POST['confirm_logout']) && $_POST['confirm_logout'] === 'yes') {
    // Hapus semua session
    session_destroy();
    
    // Redirect ke halaman utama dengan pesan
    header('Location: index.php?logout=success');
    exit();
}

// Jika tidak ada konfirmasi, redirect kembali
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>

<div class="logout-container">
    <div class="logout-card">
        <div class="logout-header">
            <i class="fas fa-sign-out-alt"></i>
            <h3>Konfirmasi Logout</h3>
        </div>
        
        <p>Apakah Anda yakin ingin keluar?</p>
        
        <div class="logout-buttons">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="confirm_logout" value="yes">
                <button type="submit" class="btn-logout">Ya, Logout</button>
            </form>
            <a href="javascript:history.back()" class="btn-cancel">Batal</a>
        </div>
    </div>
</div>

<style>
.logout-container {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.logout-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 350px;
    width: 100%;
    border: 1px solid #e2e8f0;
}

.logout-header {
    margin-bottom: 1.5rem;
}

.logout-header i {
    font-size: 2.5rem;
    color: #e53e3e;
    margin-bottom: 1rem;
}

.logout-header h3 {
    color: #2d3748;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.logout-card p {
    color: #718096;
    margin-bottom: 2rem;
    font-size: 1rem;
}

.logout-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-logout {
    background: #e53e3e;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.btn-logout:hover {
    background: #c53030;
    transform: translateY(-1px);
}

.btn-cancel {
    background: #f7fafc;
    color: #4a5568;
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.btn-cancel:hover {
    background: #edf2f7;
    color: #2d3748;
    text-decoration: none;
    transform: translateY(-1px);
}

@media (max-width: 480px) {
    .logout-buttons {
        flex-direction: column;
    }
    
    .logout-card {
        margin: 1rem;
        padding: 1.5rem;
    }
}
</style>
