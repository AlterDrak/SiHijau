<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'pendayagunaan')) { header('Location: ../../user/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Monitoring - Pendayagunaan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#fd7e14;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:900px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:2rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);text-align:center}
.card h2{color:#fd7e14;margin-bottom:1rem}
.card p{color:#666;line-height:1.6}
</style></head>
<body>
<header class="header"><h1>📊 Monitoring Program</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container"><div class="card">
<h2> Halaman dalam Pengembangan</h2>
<p>Fitur monitoring distribusi, verifikasi mustahik, & laporan penyaluran akan segera ditambahkan.<br><br>
💡 <strong>Tip:</strong> Gunakan halaman ini untuk mencatat progres program bantuan.</p>
</div></div></body></html>