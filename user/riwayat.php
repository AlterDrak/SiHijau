<?php
session_start();
require '../config/db.php';

// Keamanan: Hanya user yang login & berstatus 'user'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data transaksi user
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();

// Hitung ringkasan (hanya transaksi sukses)
$totalTopup = 0;
$totalDonasi = 0;
foreach ($transactions as $tx) {
    if ($tx['status'] === 'success') {
        if ($tx['type'] === 'topup') {
            $totalTopup += $tx['amount'];
        } else {
            $totalDonasi += $tx['amount'];
        }
    }
}

// Ambil saldo aktif (fallback 0 jika tabel belum diinisialisasi)
try {
    $stmtBal = $pdo->prepare("SELECT balance FROM user_balances WHERE user_id = ?");
    $stmtBal->execute([$userId]);
    $saldo = $stmtBal->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $saldo = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Lazpersis Rajapolah</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f9fa; color: #333; }
        
        /* Header */
        .header { 
            background: #0b5345; color: #fff; padding: 1rem 2rem; 
            display: flex; justify-content: space-between; align-items: center; 
        }
        .header h1 { font-size: 1.2rem; }
        .back-btn { 
            color: #fff; text-decoration: none; padding: 0.5rem 1rem; 
            border: 1px solid rgba(255,255,255,0.3); border-radius: 6px; font-size: 0.9rem; 
        }
        
        /* Container & Cards */
        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .card { 
            background: #fff; padding: 1.5rem; border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 1.5rem; 
        }
        .card h2 { color: #0b5345; margin-bottom: 1rem; font-size: 1.3rem; }
        
        /* Summary Grid */
        .summary-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); 
            gap: 1rem; margin-bottom: 1.5rem; 
        }
        .summary-item { 
            background: #e8f5e9; padding: 1rem; border-radius: 10px; text-align: center; 
        }
        .summary-item h3 { font-size: 1.4rem; color: #0b5345; margin-bottom: 0.3rem; }
        .summary-item p { font-size: 0.85rem; color: #555; }
        
        /* Transaction List */
        .tx-list { list-style: none; }
        .tx-item { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 1rem 0; border-bottom: 1px solid #eee; flex-wrap: wrap; gap: 0.5rem; 
        }
        .tx-item:last-child { border-bottom: none; }
        .tx-info h4 { color: #222; font-size: 1rem; margin-bottom: 0.2rem; }
        .tx-info p { font-size: 0.85rem; color: #666; }
        .tx-amount { font-weight: 700; color: #0b5345; font-size: 1.05rem; }
        
        /* Status Badges */
        .badge { 
            padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; 
            display: inline-block; margin-top: 0.3rem; 
        }
        .badge-success { background: #d1e7dd; color: #0f5132; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-failed  { background: #f8d7da; color: #842029; }
        
        /* Empty State */
        .empty-state { text-align: center; padding: 2rem 1rem; color: #777; }
        .empty-state .icon { font-size: 3rem; margin-bottom: 0.8rem; display: block; }
        .empty-state p { font-size: 0.95rem; }
        
        /* Responsive */
        @media (max-width: 600px) {
            .tx-item { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
            .tx-amount { align-self: flex-end; }
        }
    </style>
</head>
<body>
    <header class="header">
        <h1> Riwayat Transaksi</h1>
        <a href="dashboard.php" class="back-btn">← Kembali</a>
    </header>

    <div class="container">
        <!-- Ringkasan Keuangan -->
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Rp <?= number_format($saldo, 0, ',', '.') ?></h3>
                <p>Saldo Aktif</p>
            </div>
            <div class="summary-item">
                <h3>Rp <?= number_format($totalTopup, 0, ',', '.') ?></h3>
                <p>Total Top Up</p>
            </div>
            <div class="summary-item">
                <h3>Rp <?= number_format($totalDonasi, 0, ',', '.') ?></h3>
                <p>Total Donasi</p>
            </div>
        </div>

        <!-- Daftar Transaksi -->
        <div class="card">
            <h2>Detail Transaksi</h2>
            
            <?php if (empty($transactions)): ?>
                <div class="empty-state">
                    <span class="icon"></span>
                    <p>Belum ada transaksi. Mulai berdonasi atau top up saldo sekarang!</p>
                </div>
            <?php else: ?>
                <ul class="tx-list">
                    <?php foreach ($transactions as $tx): 
                        // Mapping status ke class badge
                        $statusClass = match($tx['status']) {
                            'success' => 'badge-success',
                            'pending' => 'badge-pending',
                            default   => 'badge-failed'
                        };
                        $typeLabel = strtoupper($tx['type']);
                        $catLabel = $tx['category'] ? ' - ' . ucfirst($tx['category']) : '';
                    ?>
                    <li class="tx-item">
                        <div class="tx-info">
                            <h4><?= $typeLabel . htmlspecialchars($catLabel) ?></h4>
                            <p>
                                <?= date('d M Y, H:i', strtotime($tx['created_at'])) ?> • 
                                Ref: <span style="font-family:monospace;"><?= htmlspecialchars($tx['qris_ref']) ?></span>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div class="tx-amount">Rp <?= number_format($tx['amount'], 0, ',', '.') ?></div>
                            <span class="badge <?= $statusClass ?>"><?= ucfirst($tx['status']) ?></span>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>