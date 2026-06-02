<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'penghimpunan')) { header('Location: ../../user/dashboard.php'); exit; }

try {
    $donatur = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM transactions WHERE status='success'")->fetchColumn();
    $realisasi = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type IN ('infaq','shodaqoh') AND status='success'")->fetchColumn();
    $campaign = $pdo->query("SELECT COUNT(*) FROM campaigns WHERE status='aktif'")->fetchColumn();
} catch(PDOException $e) { 
    $donatur = $realisasi = $campaign = 0; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divisi Penghimpunan - Lazpersis</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f9fa; color: #333; }
        
        .header { background: #28a745; color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .header h1 { font-size: 1.3rem; }
        .logout-btn { background: rgba(255,255,255,0.2); color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }
        
        .container { max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem; }
        .welcome { background: linear-gradient(135deg, #28a745, #20c997); color: #fff; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; }
        .welcome h2 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .welcome p { font-size: 0.95rem; opacity: 0.95; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-left: 4px solid #28a745; }
        .stat-card h3 { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .stat-card .value { font-size: 2rem; font-weight: bold; color: #28a745; }
        
        .menu-title { color: #0b5345; margin-bottom: 1rem; font-size: 1.2rem; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; }
        .menu-card { background: #fff; padding: 1.8rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); text-decoration: none; color: inherit; transition: 0.3s; border-top: 4px solid #28a745; display: block; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .menu-card .icon { font-size: 2.5rem; margin-bottom: 0.8rem; }
        .menu-card h3 { color: #28a745; margin-bottom: 0.5rem; font-size: 1.2rem; }
        .menu-card p { font-size: 0.9rem; color: #666; }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .header { flex-direction: column; text-align: center; padding: 1rem; }
            .header h1 { font-size: 1.1rem; }
            .container { padding: 0 1rem; margin: 1rem auto; }
            .welcome { padding: 1.2rem; text-align: center; }
            .welcome h2 { font-size: 1.2rem; }
            .stats-grid, .menu-grid { grid-template-columns: 1fr; gap: 1rem; }
            .stat-card { text-align: center; border-left: none; border-top: 4px solid #28a745; }
            .menu-card { text-align: center; padding: 1.2rem; }
        }
        @media (max-width: 480px) {
            .stat-card .value { font-size: 1.5rem; }
            .welcome { padding: 1rem; }
            .menu-card { padding: 1rem; }
        }
    </style>
</head>
<body>
<header class="header">
    <h1>🤲 Divisi Penghimpunan - Lazpersis</h1>
    <form action="../../user/logout.php" method="POST">
        <button type="submit" class="logout-btn"> Keluar</button>
    </form>
</header>

<div class="container">
    <div class="welcome">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <p>Dashboard penghimpunan dana, donatur, & campaign</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>👥 Total Donatur</h3>
            <div class="value"><?= $donatur ?></div>
        </div>
        <div class="stat-card">
            <h3>💰 Realisasi Infaq/Shodaqoh</h3>
            <div class="value">Rp <?= number_format($realisasi,0,',','.') ?></div>
        </div>
        <div class="stat-card">
            <h3>📢 Campaign Aktif</h3>
            <div class="value"><?= $campaign ?></div>
        </div>
    </div>
    
    <h3 class="menu-title">Menu Pengelolaan</h3>
    <div class="menu-grid">
        <a href="donatur.php" class="menu-card">
            <div class="icon">👥</div>
            <h3>Data Donatur</h3>
            <p>Kelola & pantau riwayat donatur</p>
        </a>
        <a href="monitoring.php" class="menu-card">
            <div class="icon">📊</div>
            <h3>Monitoring Campaign</h3>
            <p>Pantau progres penghimpunan & kinerja staff</p>
        </a>
    </div>
</div>
</body>
</html>