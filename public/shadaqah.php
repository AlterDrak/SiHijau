<?php
$judul = 'Shadaqah - Lazpersis';
require '../config/db.php';
require '../includes/header.php';
?>
<h1>Shadaqah & Infaq</h1>
<p>Shadaqah adalah pemberian sukarela untuk kebaikan.</p>
<div class="grid">
    <div class="card"><h3>Bantuan Sosial</h3><p>Untuk masyarakat membutuhkan</p></div>
    <div class="card"><h3>Kesehatan</h3><p>Bantuan medis & ambulans</p></div>
</div>
<a href="../user/login.php" class="btn">Salurkan Shadaqah</a>
<?php require '../includes/footer.php'; ?>