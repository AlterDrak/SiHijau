<?php
require '../includes/auth.php'; requireLogin();
$judul = 'Dashboard User'; include '../includes/header.php';
?>
<h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
<div class="grid">
    <a href="bayar.php" class="card"> Bayar Zakat / Shadaqah</a>
    <a href="riwayat.php" class="card">📜 Riwayat Transaksi</a>
</div>
<?php include '../includes/footer.php'; ?>