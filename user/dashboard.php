<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

// Data simulasi saldo (nanti ganti dengan database)
$saldo = 250000; // Rp 250.000
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Dashboard Donatur - Lazpersis Kabupaten Tasikmalaya</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f9fa; color: #333; }
        
        /* Header Responsif */
        .header { background: #0b5345; color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .header h1 { font-size: 1.3rem; }
        .logout-btn { background: rgba(255,255,255,0.2); color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }
        
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1.5rem; }
        
        /* Welcome Card */
        .welcome-card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 1.5rem; text-align: center; }
        .welcome-card h2 { color: #0b5345; margin-bottom: 0.5rem; word-break: break-word; }
        .welcome-card p { color: #666; }
        .status-badge { display: inline-block; background: #d1e7dd; color: #0f5132; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-top: 0.5rem; }
        
        /* Saldo Card */
        .saldo-card { background: linear-gradient(135deg, #0b5345, #198754); color: #fff; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .saldo-info h4 { font-size: 0.85rem; opacity: 0.9; margin-bottom: 0.3rem; }
        .saldo-nominal { font-size: 1.8rem; font-weight: bold; word-break: break-word; }
        
        /* Tombol Top Up sekarang jadi link */
        .btn-topup { background: rgba(255, 255, 255, 0.2); border: none; color: #fff; padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer; transition: 0.3s; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-topup:hover { background: rgba(255, 255, 255, 0.3); }
        
        /* Menu Grid */
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
        .menu-card { background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); text-align: center; transition: 0.3s; text-decoration: none; color: inherit; border-top: 4px solid #0b5345; cursor: pointer; display: block; }
        .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .menu-card .icon { font-size: 2rem; margin-bottom: 0.8rem; }
        .menu-card h3 { color: #0b5345; margin-bottom: 0.5rem; font-size: 1.1rem; }
        .menu-card p { font-size: 0.8rem; color: #666; }
        
        /* Tombol Riwayat */
        .riwayat-btn-wrapper { text-align: center; margin-top: 0.5rem; margin-bottom: 1rem; }
        .btn-riwayat { background: transparent; border: 2px solid #0b5345; color: #0b5345; padding: 0.7rem 1.5rem; border-radius: 30px; font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 0.9rem; }
        .btn-riwayat:hover { background: #0b5345; color: #fff; }
        
        /* Modal Riwayat */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #fff; border-radius: 16px; width: 90%; max-width: 500px; max-height: 80vh; overflow: auto; padding: 1.5rem; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e2e8f0; }
        .modal-header h3 { color: #0b5345; }
        .close-modal { font-size: 1.8rem; cursor: pointer; color: #94a3b8; line-height: 1; }
        .riwayat-item { display: flex; justify-content: space-between; padding: 0.8rem 0; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 0.5rem; }
        .riwayat-keterangan { font-weight: 500; }
        .riwayat-tanggal { font-size: 0.7rem; color: #64748b; margin-top: 0.2rem; }
        .riwayat-nominal { font-weight: 600; color: #0b5345; }
        .riwayat-nominal.minus { color: #dc2626; }
        
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
            
            .welcome-card {
                padding: 1.2rem;
            }
            
            .welcome-card h2 {
                font-size: 1.2rem;
            }
            
            .welcome-card p {
                font-size: 0.85rem;
            }
            
            .saldo-card {
                flex-direction: column;
                text-align: center;
                padding: 1.2rem;
            }
            
            .saldo-nominal {
                font-size: 1.5rem;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .menu-card {
                padding: 1rem;
                display: flex;
                align-items: center;
                text-align: left;
                gap: 1rem;
            }
            
            .menu-card .icon {
                font-size: 1.8rem;
                margin-bottom: 0;
            }
            
            .menu-card .menu-text {
                flex: 1;
            }
            
            .menu-card h3 {
                font-size: 1rem;
                margin-bottom: 0.2rem;
            }
            
            .menu-card p {
                font-size: 0.75rem;
            }
            
            .btn-riwayat {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
            
            .modal-content {
                padding: 1rem;
                width: 95%;
            }
            
            .riwayat-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .saldo-nominal {
                font-size: 1.3rem;
            }
            
            .menu-card {
                padding: 0.8rem;
            }
            
            .menu-card .icon {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>🌙 Lazpersis Kab. Tasikmalaya</h1>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit" class="logout-btn">🚪 Keluar</button>
        </form>
    </header>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p>Terima kasih telah bergabung sebagai donatur Lazpersis Kabupaten Tasikmalaya</p>
            <span class="status-badge">✅ Akun Aktif</span>
        </div>

        <!-- SALDO CARD - Tombol Top Up sekarang link ke topup.php -->
        <div class="saldo-card">
            <div class="saldo-info">
                <h4>💳 SALDO ANDA</h4>
                <div class="saldo-nominal">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
            </div>
            <a href="topup.php" class="btn-topup">Top Up Saldo →</a>
        </div>

        <div class="menu-grid">
            <a href="topup.php" class="menu-card">
                <div class="icon">💳</div>
                <div class="menu-text">
                    <h3>Top Up Saldo</h3>
                    <p>Isi saldo untuk kemudahan transaksi</p>
                </div>
            </a>
            <a href="zakat.php" class="menu-card">
                <div class="icon">💰</div>
                <div class="menu-text">
                    <h3>Bayar Zakat</h3>
                    <p>Maal, Perdagangan, Perhiasan, Fitrah, Pertanian</p>
                </div>
            </a>
            <a href="infaq.php" class="menu-card">
                <div class="icon">🤲</div>
                <div class="menu-text">
                    <h3>Infaq</h3>
                    <p>Salurkan infaq untuk program umum</p>
                </div>
            </a>
            <a href="shodaqoh.php" class="menu-card">
                <div class="icon">🌟</div>
                <div class="menu-text">
                    <h3>Shodaqoh</h3>
                    <p>Berikan shodaqoh sukarela untuk sesama</p>
                </div>
            </a>
        </div>

        <!-- TOMBOL RIWAYAT (buka modal) -->
        <div class="riwayat-btn-wrapper">
            <button class="btn-riwayat" onclick="openRiwayatModal()">📜 Lihat Riwayat Transaksi</button>
        </div>
    </div>

    <!-- MODAL RIWAYAT TRANSAKSI -->
    <div id="riwayatModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📜 Riwayat Transaksi</h3>
                <span class="close-modal" onclick="closeRiwayatModal()">&times;</span>
            </div>
            <div class="riwayat-list">
                <div class="riwayat-item">
                    <div>
                        <div class="riwayat-keterangan">Zakat Fitrah</div>
                        <div class="riwayat-tanggal">28 Mei 2026</div>
                    </div>
                    <div class="riwayat-nominal minus">-Rp 45.000</div>
                </div>
                <div class="riwayat-item">
                    <div>
                        <div class="riwayat-keterangan">Infaq Umum</div>
                        <div class="riwayat-tanggal">20 Mei 2026</div>
                    </div>
                    <div class="riwayat-nominal minus">-Rp 100.000</div>
                </div>
                <div class="riwayat-item">
                    <div>
                        <div class="riwayat-keterangan">Top Up Saldo</div>
                        <div class="riwayat-tanggal">15 Mei 2026</div>
                    </div>
                    <div class="riwayat-nominal">+Rp 200.000</div>
                </div>
                <div class="riwayat-item">
                    <div>
                        <div class="riwayat-keterangan">Shodaqoh</div>
                        <div class="riwayat-tanggal">10 Mei 2026</div>
                    </div>
                    <div class="riwayat-nominal minus">-Rp 50.000</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openRiwayatModal() {
            document.getElementById('riwayatModal').style.display = 'flex';
        }
        
        function closeRiwayatModal() {
            document.getElementById('riwayatModal').style.display = 'none';
        }
        
        // Tutup modal jika klik di luar
        window.onclick = function(event) {
            var modal = document.getElementById('riwayatModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>