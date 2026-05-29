<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }

$judul = 'Laporan Keuangan';
require_once __DIR__ . '/../../includes/header.php';
?>

<div style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <a href="index.php" style="color:#0b5345;">← Kembali</a>
    <h1>📈 Laporan Keuangan</h1>
    
    <div style="background:#fff;padding:2rem;border-radius:8px;margin-top:1rem;">
        <p>Fitur laporan keuangan bulanan/tahunan akan ditampilkan di sini.</p>
        <ul>
            <li>Laporan Arus Kas</li>
            <li>Laporan Posisi Keuangan</li>
            <li>Laporan Perubahan Dana</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>