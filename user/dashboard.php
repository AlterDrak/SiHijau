<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Donatur - Lazpersis Rajapolah</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f9fa; color: #333; }
        .header { background: #0b5345; color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.3rem; }
        .logout-btn { background: rgba(255,255,255,0.2); color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1.5rem; }
        .welcome-card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 2rem; text-align: center; }
        .welcome-card h2 { color: #0b5345; margin-bottom: 0.5rem; }
        .welcome-card p { color: #666; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
        .menu-card { background: #fff; padding: 1.8rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); text-align: center; transition: 0.3s; text-decoration: none; color: inherit; border-top: 4px solid #0b5345; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .menu-card .icon { font-size: 2.5rem; margin-bottom: 0.8rem; }
        .menu-card h3 { color: #0b5345; margin-bottom: 0.5rem; }
        .menu-card p { font-size: 0.9rem; color: #666; }
        .status-badge { display: inline-block; background: #d1e7dd; color: #0f5132; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-top: 0.5rem; }
    </style>
</head>
<body>
    <header class="header">
        <h1>🕌 Lazpersis Rajapolah</h1>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit" class="logout-btn">🚪 Keluar</button>
        </form>
    </header>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Terima kasih telah bergabung sebagai donatur Lazpersis Rajapolah</p>
            <span class="status-badge">✅ Akun Aktif</span>
        </div>

        <div class="menu-grid">
            <a href="topup.php" class="menu-card">
                <div class="icon">💳</div>
                <h3>Top Up Saldo</h3>
                <p>Isi saldo untuk kemudahan transaksi</p>
            </a>
            <a href="zakat.php" class="menu-card">
                <div class="icon">💰</div>
                <h3>Bayar Zakat</h3>
                <p>Maal, Perdagangan, Perhiasan, Fitrah, Pertanian</p>
            </a>
            <a href="infaq.php" class="menu-card">
                <div class="icon">🤲</div>
                <h3>Infaq</h3>
                <p>Salurkan infaq untuk program umum</p>
            </a>
            <a href="shodaqoh.php" class="menu-card">
                <div class="icon">🌟</div>
                <h3>Shodaqoh</h3>
                <p>Berikan shodaqoh sukarela untuk sesama</p>
            </a>
            <a href="riwayat.php" class="menu-card">
                <div class="icon">📜</div>
                <h3>Riwayat Transaksi</h3>
                <p>Lihat catatan pembayaran & penyaluran</p>
            </a>
        </div>
    </div>
</body>
</html>