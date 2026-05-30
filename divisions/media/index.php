<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'media')) { header('Location: ../../user/dashboard.php'); exit; }

try {
    $publikasi = $pdo->query("SELECT COUNT(*) FROM publications WHERE status='published'")->fetchColumn();
    $dokumentasi = $pdo->query("SELECT COUNT(*) FROM media_files")->fetchColumn();
    $reach = $pdo->query("SELECT COALESCE(SUM(reach),0) FROM social_stats WHERE month=MONTH(CURRENT_DATE())")->fetchColumn();
} catch(PDOException $e) { $publikasi=$dokumentasi=$reach=0; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Divisi Media - Lazpersis</title>
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
            background: #6f42c1;
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
            background: linear-gradient(135deg, #6f42c1, #5a2d82);
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
            border-left: 4px solid #6f42c1;
        }
        .stat-card h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #6f42c1;
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
            border-top: 4px solid #6f42c1;
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
            color: #6f42c1;
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
                border-top: 4px solid #6f42c1;
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
    <h1>Divisi Media - Lazpersis</h1>
    <form action="../../user/logout.php" method="POST">
        <button type="submit" class="logout-btn">Keluar</button>
    </form>
</header>
<div class="container">
    <div class="welcome">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
        <p>Dashboard dokumentasi, publikasi, & media sosial</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>📰 Publikasi Bulan Ini</h3>
            <div class="value"><?= $publikasi ?></div>
        </div>
        <div class="stat-card">
            <h3>📸 Dokumentasi</h3>
            <div class="value"><?= $dokumentasi ?></div>
        </div>
        <div class="stat-card">
            <h3>📊 Reach Media Sosial</h3>
            <div class="value"><?= number_format($reach) ?></div>
        </div>
    </div>
    
    <h3 class="menu-title">Menu Pengelolaan</h3>
    <div class="menu-grid">
        <a href="publications.php" class="menu-card">
            <div class="icon">📝</div>
            <h3>Publikasi & Berita</h3>
            <p>Kelola artikel & siaran pers</p>
        </a>
        <a href="monitoring.php" class="menu-card">
            <div class="icon">📊</div>
            <h3>Monitoring Media</h3>
            <p>Pantau engagement & dokumentasi</p>
        </a>
    </div>
</div>
</body>
</html>