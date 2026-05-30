<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'keuangan')) { header('Location: ../../user/dashboard.php'); exit; }

try {
    $pendingUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user' AND is_active=0")->fetchColumn();
    $pendingTx = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='pending'")->fetchColumn();
    $totalVerified = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE status='success'")->fetchColumn();
    $staffCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff' AND division='keuangan' AND is_active=1")->fetchColumn();
} catch(PDOException $e) { $pendingUsers=$pendingTx=$totalVerified=$staffCount=0; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Divisi Keuangan - Lazpersis</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            background: #f8f9fa;
            color: #333;
        }
        .header {
            background: #0b5345;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .header h1 {
            font-size: 1.3rem;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .welcome {
            background: linear-gradient(135deg, #0b5345, #198754);
            color: #fff;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .welcome h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }
        .welcome p {
            font-size: 0.95rem;
            opacity: 0.95;
        }
        
        /* Stats Grid - Responsif */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            border-left: 4px solid #0b5345;
        }
        .stat-card h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #0b5345;
            word-break: break-word;
        }
        
        .menu-title {
            color: #0b5345;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        /* Menu Grid - Responsif */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }
        .menu-card {
            background: #fff;
            padding: 1.8rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
            border-top: 4px solid #0b5345;
            display: block;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .menu-card .icon {
            font-size: 2.5rem;
            margin-bottom: 0.8rem;
        }
        .menu-card h3 {
            color: #0b5345;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        .menu-card p {
            font-size: 0.9rem;
            color: #666;
        }
        
        /* ========= RESPONSIVE UNTUK HP ========= */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                padding: 1rem;
            }
            
            .header h1 {
                font-size: 1.1rem;
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
                font-size: 1.2rem;
            }
            
            .welcome p {
                font-size: 0.85rem;
            }
            
            /* Grid stats jadi 1 kolom di HP */
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1rem;
                text-align: center;
                border-left: none;
                border-top: 4px solid #0b5345;
            }
            
            .stat-card .value {
                font-size: 1.8rem;
            }
            
            .stat-card h3 {
                font-size: 0.85rem;
            }
            
            .menu-title {
                text-align: center;
                font-size: 1.1rem;
                margin-bottom: 1rem;
            }
            
            /* Grid menu jadi 1 kolom di HP */
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .menu-card {
                padding: 1.2rem;
                text-align: center;
            }
            
            .menu-card .icon {
                font-size: 2rem;
            }
            
            .menu-card h3 {
                font-size: 1rem;
            }
            
            .menu-card p {
                font-size: 0.8rem;
            }
        }
        
        /* Untuk layar sangat kecil (HP < 480px) */
        @media (max-width: 480px) {
            .stat-card .value {
                font-size: 1.5rem;
            }
            
            .welcome {
                padding: 1rem;
            }
            
            .menu-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <h1>Divisi Keuangan - Lazpersis</h1>
    <form action="../../user/logout.php" method="POST">
        <button type="submit" class="logout-btn">Keluar</button>
    </form>
</header>
<div class="container">
    <div class="welcome">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <p>Dashboard verifikasi transaksi, aktivasi user, & monitoring keuangan</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>👥 User Pending</h3>
            <div class="value"><?= $pendingUsers ?></div>
        </div>
        <div class="stat-card">
            <h3>💳 Transaksi Pending</h3>
            <div class="value"><?= $pendingTx ?></div>
        </div>
        <div class="stat-card">
            <h3>✅ Total Terverifikasi</h3>
            <div class="value">Rp <?= number_format($totalVerified,0,',','.') ?></div>
        </div>
        <div class="stat-card">
            <h3>👥 Staff Aktif</h3>
            <div class="value"><?= $staffCount ?></div>
        </div>
    </div>
    
    <h3 class="menu-title">Menu Pengelolaan</h3>
    <div class="menu-grid">
        <a href="activations.php" class="menu-card">
            <div class="icon">👥</div>
            <h3>Aktivasi User</h3>
            <p>Aktivasi & nonaktifkan akun donatur</p>
        </a>
        <a href="transactions.php" class="menu-card">
            <div class="icon">✅</div>
            <h3>Verifikasi Transaksi</h3>
            <p>Terima/tolak pembayaran masuk</p>
        </a>
        <a href="monitoring.php" class="menu-card">
            <div class="icon">📊</div>
            <h3>Monitoring Keuangan</h3>
            <p>Laporan arus kas & rekonsiliasi</p>
        </a>
    </div>
</div>
</body>
</html>