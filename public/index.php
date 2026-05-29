<?php
$judul = 'Beranda - Lazpersis Rajapolah';
require '../config/db.php';
require '../includes/header.php';
?>

<h1>Selamat Datang di Lazpersis Rajapolah</h1>
<p>Lembaga Amil Zakat Persis Cabang Rajapolah, Kabupaten Tasikmalaya</p>

<div class="grid">
    <div class="card">
        <h3>📰 Berita & Kegiatan</h3>
        <p>Update terbaru dari kegiatan Lazpersis Rajapolah</p>
    </div>
    <div class="card">
        <h3>💰 Zakat</h3>
        <p>Tunaikan zakat Anda melalui Lazpersis</p>
        <a href="../user/login.php" class="btn">Bayar Sekarang</a>
    </div>
    <div class="card">
        <h3>🤲 Shadaqah</h3>
        <p>Salurkan infaq dan shadaqah Anda</p>
        <a href="../user/login.php" class="btn">Salurkan Sekarang</a>
    </div>
</div>

<?php require '../includes/footer.php'; ?>