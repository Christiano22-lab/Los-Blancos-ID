<?php
session_start();

// Hapus semua session
session_destroy();

// Redirect ke halaman utama
header('Location: index.php');
exit();
?>