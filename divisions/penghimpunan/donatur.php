<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$judul = 'Data Donatur';
require_once __DIR__ . '/../../includes/header.php';
?>
<div style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <a href="index.php">← Kembali</a>
    <h1>👥 Data Donatur/Muzakki</h1>
    <p>Daftar donatur dan riwayat kontribusi akan ditampilkan di sini.</p>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>