<?php
$judul = 'Zakat - Lazpersis';
require '../config/db.php';
require '../includes/header.php';
?>
<h1>Zakat</h1>
<p>Zakat adalah rukun Islam ketiga yang wajib ditunaikan.</p>
<div class="grid">
    <div class="card"><h3>Zakat Maal</h3><p>2.5% dari harta</p></div>
    <div class="card"><h3>Zakat Profesi</h3><p>2.5% dari penghasilan</p></div>
    <div class="card"><h3>Zakat Fitrah</h3><p>Wajib di bulan Ramadan</p></div>
</div>
<a href="../user/login.php" class="btn">Bayar Zakat</a>
<?php require '../includes/footer.php'; ?>