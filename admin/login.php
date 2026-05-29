<?php
require '../includes/auth.php'; requireRole(['super_admin', 'admin']);
$judul = 'Admin Panel'; include '../includes/header.php';
?>
<h2>Panel Manajemen</h2>
<div class="grid">
    <a href="users.php" class="card"> Kelola User</a>
    <a href="monitoring.php" class="card"> Monitoring Divisi</a>
</div>
<?php include '../includes/footer.php'; ?>