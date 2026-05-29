<?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['role'] === 'super_admin'): ?>
        <li><a href="../dashboard.php">👑 Super Admin Hub</a></li>
    <?php elseif ($_SESSION['role'] === 'staff'): ?>
        <li><a href="../divisions/<?= $_SESSION['division'] ?>/index.php">Dashboard Divisi</a></li>
    <?php endif; ?>
    
    <li><a href="../user/dashboard.php">Dashboard</a></li>
    <li><a href="../user/logout.php">Keluar</a></li>
<?php else: ?>
    <li><a href="../user/login.php">Masuk</a></li>
<?php endif; ?>