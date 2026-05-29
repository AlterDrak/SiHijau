<?php 
// 1. Pastikan Session Sudah Aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

// 2. Load Config & Auth (Gunakan require_once agar tidak error double include)
// Pastikan path ini sesuai dengan struktur folder Anda
if (file_exists(__DIR__ . '/../config/db.php')) {
    require_once __DIR__ . '/../config/db.php';
}
if (file_exists(__DIR__ . '/auth.php')) {
    require_once __DIR__ . '/auth.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul ?? 'Lazpersis Rajapolah' ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <nav>
        <a href="../public/index.php" class="logo">Lazpersis Rajapolah</a>
        <ul>
            <!-- Menu Publik (Selalu Muncul) -->
            <li><a href="../public/index.php">Beranda</a></li>
            <li><a href="../public/zakat.php">Zakat</a></li>
            <li><a href="../public/shadaqah.php">Shadaqah</a></li>
            
            <!-- Logika Menu Berdasarkan Login -->
            <?php 
            // Ambil role dari session, jika kosong beri nilai default string kosong
            $userRole = $_SESSION['role'] ?? ''; 
            $isLoggedIn = isset($_SESSION['user_id']);
            ?>

            <?php if ($isLoggedIn): ?>
                
                <!-- Menu KHUSUS Admin & Super Admin -->
                <?php if ($userRole === 'super_admin' || $userRole === 'admin'): ?>
                    <li><a href="../admin/dashboard.php">Admin Panel</a></li>
                <?php endif; ?>

                <!-- Menu Umum User yang Sudah Login -->
                <li><a href="../user/dashboard.php">Dashboard</a></li>
                <li><a href="../user/logout.php">Keluar</a></li>

            <?php else: ?>
                
                <!-- Menu Untuk Tamu (Belum Login) -->
                <li><a href="../user/login.php">Masuk</a></li>

            <?php endif; ?>
        </ul>
    </nav>
    
    <!-- Main Content Mulai Di Sini -->
    <main>