<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'pendayagunaan')) { header('Location: ../../user/dashboard.php'); exit; }

try {
    $mustahik = $pdo->query("SELECT COUNT(DISTINCT mustahik_id) FROM distributions")->fetchColumn();
    $tersalurkan = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM distributions WHERE status='selesai'")->fetchColumn();
    $program = $pdo->query("SELECT COUNT(*) FROM programs WHERE status='aktif'")->fetchColumn();
} catch(PDOException $e) { $mustahik=$tersalurkan=$program=0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Divisi Pendayagunaan - Lazpersis</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#fd7e14;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.3rem}
.logout-btn{background:rgba(255,255,255,0.2);color:#fff;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer}
.logout-btn:hover{background:rgba(255,255,255,0.3)}
.container{max-width:1100px;margin:2rem auto;padding:0 1.5rem}
.welcome{background:linear-gradient(135deg,#fd7e14,#e85d04);color:#fff;padding:2rem;border-radius:12px;margin-bottom:2rem}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;margin-bottom:2rem}
.stat-card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);border-left:4px solid #fd7e14}
.stat-card h3{font-size:0.9rem;color:#666;margin-bottom:0.5rem}
.stat-card .value{font-size:2rem;font-weight:bold;color:#fd7e14}
.menu-title{color:#0b5345;margin-bottom:1rem;font-size:1.2rem}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem}
.menu-card{background:#fff;padding:1.8rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);text-decoration:none;color:inherit;transition:0.3s;border-top:4px solid #fd7e14}
.menu-card:hover{transform:translateY(-5px);box-shadow:0 8px 20px rgba(0,0,0,0.1)}
.menu-card .icon{font-size:2.5rem;margin-bottom:0.8rem}
.menu-card h3{color:#fd7e14;margin-bottom:0.5rem}
.menu-card p{font-size:0.9rem;color:#666}
</style></head>
<body>
<header class="header"><h1>🎯 Divisi Pendayagunaan - Lazpersis</h1><form action="../../user/logout.php" method="POST"><button type="submit" class="logout-btn">🚪 Keluar</button></form></header>
<div class="container">
<div class="welcome"><h2>Selamat Datang, <?=htmlspecialchars($_SESSION['username'])?></h2><p>Dashboard penyaluran dana, mustahik, & program sosial</p></div>
<div class="stats-grid">
<div class="stat-card"><h3>👥 Total Mustahik</h3><div class="value"><?=$mustahik?></div></div>
<div class="stat-card"><h3>💵 Dana Tersalurkan</h3><div class="value">Rp <?=number_format($tersalurkan,0,',','.')?></div></div>
<div class="stat-card"><h3>📦 Program Aktif</h3><div class="value"><?=$program?></div></div>
</div>
<h3 class="menu-title">Menu Pengelolaan</h3>
<div class="menu-grid">
<a href="distributions.php" class="menu-card"><div class="icon">📋</div><h3>Data Penyaluran</h3><p>Kelola distribusi ke mustahik</p></a>
<a href="monitoring.php" class="menu-card"><div class="icon">📊</div><h3>Monitoring Program</h3><p>Pantau progress & laporan penyaluran</p></a>
</div>
</div></body></html>