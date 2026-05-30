<?php
session_start();
require 'config/db.php';

// Hanya super_admin yang boleh akses hub ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: user/dashboard.php');
    exit;
}

// Statistik Global
try {
    $totalUser = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
    $totalStaff = $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff' AND is_active=1")->fetchColumn();
    $totalTx = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
    $totalVerified = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE status='success'")->fetchColumn();
    $pendingUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user' AND is_active=0")->fetchColumn();
} catch (PDOException $e) {
    $totalUser = $totalStaff = $totalTx = $totalVerified = $pendingUsers = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Super Admin Hub - Lazpersis Rajapolah</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
        body{background:#f8f9fa;color:#333}
        
        /* Header */
        .header{background:#0b5345;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem}
        .header h1{font-size:1.4rem}
        .logout-btn{background:rgba(255,255,255,0.2);color:#fff;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;transition:0.3s}
        .logout-btn:hover{background:rgba(255,255,255,0.3)}
        
        /* Container */
        .container{max-width:1200px;margin:2rem auto;padding:0 1.5rem}
        
        /* Welcome Card */
        .welcome{background:linear-gradient(135deg,#0b5345,#155724);color:#fff;padding:1.5rem;border-radius:12px;margin-bottom:1.5rem}
        .welcome h2{font-size:1.3rem;margin-bottom:0.5rem;word-break:break-word}
        .welcome p{font-size:0.9rem;opacity:0.95}
        
        /* Stats Grid - Responsive */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:2rem}
        .stat-card{background:#fff;padding:1rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);border-left:4px solid #0b5345}
        .stat-card h3{font-size:0.7rem;color:#666;margin-bottom:0.3rem;text-transform:uppercase;letter-spacing:0.5px}
        .stat-card .value{font-size:1.4rem;font-weight:bold;color:#0b5345;word-break:break-word}
        
        /* Section Title */
        .section-title{color:#0b5345;margin:1.5rem 0 1rem 0;font-size:1.1rem;border-bottom:2px solid #e9ecef;padding-bottom:0.5rem}
        
        /* Div Grid - Responsive */
        .div-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem}
        .div-card{background:#fff;padding:1.2rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);text-decoration:none;color:inherit;transition:0.3s;border-top:4px solid #0b5345;display:block}
        .div-card:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(0,0,0,0.1)}
        .div-card h3{color:#0b5345;margin-bottom:0.3rem;font-size:1rem}
        .div-card p{font-size:0.8rem;color:#666;line-height:1.4}
        .badge{background:#ffc107;color:#000;padding:0.2rem 0.6rem;border-radius:12px;font-size:0.7rem;font-weight:600;display:inline-block}
        
        /* ========= RESPONSIVE HP ========= */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }
            
            .container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
            
            .welcome {
                padding: 1.2rem;
                text-align: center;
            }
            
            .welcome h2 {
                font-size: 1.1rem;
            }
            
            .welcome p {
                font-size: 0.8rem;
            }
            
            /* Stats jadi 2 kolom di HP */
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }
            
            .stat-card {
                padding: 0.8rem;
                text-align: center;
            }
            
            .stat-card h3 {
                font-size: 0.65rem;
            }
            
            .stat-card .value {
                font-size: 1.1rem;
            }
            
            /* Divisi grid jadi 1 kolom di HP */
            .div-grid {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
            
            .div-card {
                padding: 1rem;
            }
            
            .div-card h3 {
                font-size: 0.95rem;
            }
            
            .div-card p {
                font-size: 0.75rem;
            }
            
            .section-title {
                font-size: 1rem;
                margin: 1.2rem 0 0.8rem 0;
            }
        }
        
        /* Untuk HP kecil (max 480px) */
        @media (max-width: 480px) {
            .stats-grid {
                gap: 0.6rem;
            }
            
            .stat-card {
                padding: 0.6rem;
            }
            
            .stat-card .value {
                font-size: 1rem;
            }
            
            .badge {
                font-size: 0.65rem;
                padding: 0.15rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Super Admin Hub</h1>
        <form action="user/logout.php" method="POST" style="display:inline;">
            <button type="submit" class="logout-btn">🚪 Keluar</button>
        </form>
    </header>

    <div class="container">
        <div class="welcome">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Pusat kendali operasional, monitoring lintas divisi, & manajemen sistem Lazpersis Kabupaten Tasikmalaya</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card"><h3>Donatur Terdaftar</h3><div class="value"><?= $totalUser ?></div></div>
            <div class="stat-card"><h3>Staff Aktif</h3><div class="value"><?= $totalStaff ?></div></div>
            <div class="stat-card"><h3>Total Transaksi</h3><div class="value"><?= $totalTx ?></div></div>
            <div class="stat-card"><h3>Dana Terverifikasi</h3><div class="value">Rp <?= number_format($totalVerified,0,',','.') ?></div></div>
            <div class="stat-card"><h3>User Pending</h3><div class="value"><span class="badge"><?= $pendingUsers ?> ⏳</span></div></div>
        </div>

        <h3 class="section-title">📂 Akses Divisi</h3>
        <div class="div-grid">
            <a href="divisions/keuangan/index.php" class="div-card">
                <h3>💰 Divisi Keuangan</h3>
                <p>Verifikasi transaksi, aktivasi user, monitoring arus kas</p>
            </a>
            <a href="divisions/penghimpunan/index.php" class="div-card">
                <h3>🤲 Divisi Penghimpunan</h3>
                <p>Data donatur, campaign, realisasi infaq & shodaqoh</p>
            </a>
            <a href="divisions/pendayagunaan/index.php" class="div-card">
                <h3>🎯 Divisi Pendayagunaan</h3>
                <p>Mustahik, penyaluran dana, program sosial</p>
            </a>
            <a href="divisions/media/index.php" class="div-card">
                <h3>📱 Divisi Media</h3>
                <p>Publikasi, dokumentasi, monitoring media sosial</p>
            </a>
        </div>

        <h3 class="section-title">⚙️ Manajemen Sistem</h3>
        <div class="div-grid">
            <a href="divisions/keuangan/activations.php" class="div-card" style="border-top-color:#28a745">
                <h3> Aktivasi User Global</h3>
                <p>Kelola status akun donatur (aktif/nonaktif)</p>
            </a>
            <a href="divisions/keuangan/transactions.php" class="div-card" style="border-top-color:#ffc107">
                <h3>✅ Verifikasi Transaksi</h3>
                <p>Terima/tolak pembayaran masuk secara manual</p>
            </a>
        </div>
    </div>
</body>
</html>