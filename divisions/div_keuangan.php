<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['super_admin', 'admin'])) {
    header('Location: ../user/login.php'); exit;
}

// Statistik Keuangan
try {
    $totalMasuk = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE status='verified'")->fetchColumn();
    $totalKeluar = $pdo->query("SELECT COALESCE(SUM(amount_distributed),0) FROM distribution_records")->fetchColumn();
    $saldo = $totalMasuk - $totalKeluar;
    $pendingCount = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status='pending'")->fetchColumn();
    $thisMonth = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE status='verified' AND MONTH(created_at)=MONTH(CURRENT_DATE())")->fetchColumn();
} catch (PDOException $e) {
    $totalMasuk = $totalKeluar = $saldo = $thisMonth = 0;
    $pendingCount = 0;
}

$judul = 'Dashboard Keuangan - Lazpersis';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <div style="margin-bottom:2rem;">
        <a href="dashboard.php" style="color:#0b5345;text-decoration:none;">← Kembali ke Admin Panel</a>
        <h1 style="margin:0.5rem 0 0 0;">💰 Dashboard Divisi Keuangan</h1>
        <p style="color:#666;margin:0;">Monitoring arus kas, laporan keuangan, dan verifikasi transaksi</p>
    </div>

    <!-- Kartu Statistik -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:2rem;">
        <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">💵 Dana Masuk</div>
            <div style="font-size:2rem;font-weight:bold;margin:0.5rem 0;">Rp <?=number_format($totalMasuk,0,',','.')?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Total terverifikasi</div>
        </div>
        <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">💸 Dana Keluar</div>
            <div style="font-size:2rem;font-weight:bold;margin:0.5rem 0;">Rp <?=number_format($totalKeluar,0,',','.')?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Total disalurkan</div>
        </div>
        <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">🏦 Saldo Saat Ini</div>
            <div style="font-size:2rem;font-weight:bold;margin:0.5rem 0;">Rp <?=number_format($saldo,0,',','.')?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Dana tersedia</div>
        </div>
        <div style="background:linear-gradient(135deg,#43e97b 0%,#38f9d7 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">📊 Bulan Ini</div>
            <div style="font-size:2rem;font-weight:bold;margin:0.5rem 0;">Rp <?=number_format($thisMonth,0,',','.')?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Perolehan <?=date('M Y')?></div>
        </div>
    </div>

    <!-- Menu Cepat -->
    <h3 style="margin-bottom:1rem;">📋 Menu Keuangan</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;margin-bottom:2rem;">
        <a href="transactions.php?status=pending" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #ffc107;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">⏳ Transaksi Pending</div>
            <div style="font-size:2rem;font-weight:bold;color:#ffc107;"><?=$pendingCount?></div>
            <div style="color:#666;font-size:0.9rem;">Perlu verifikasi</div>
        </a>
        <a href="laporan_keuangan.php" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #0b5345;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📈 Laporan Keuangan</div>
            <div style="color:#666;font-size:0.9rem;">Cetak laporan bulanan/tahunan</div>
        </a>
        <a href="monitoring.php?division=Keuangan" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #0d6efd;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📝 Aktivitas Divisi</div>
            <div style="color:#666;font-size:0.9rem;">Catat kegiatan keuangan</div>
        </a>
    </div>

    <!-- Transaksi Terbaru -->
    <h3 style="margin-bottom:1rem;">💳 5 Transaksi Terakhir</h3>
    <?php
    try {
        $recentTx = $pdo->query("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id=u.id ORDER BY t.created_at DESC LIMIT 5")->fetchAll();
    } catch (PDOException $e) { $recentTx = []; }
    ?>
    <div style="background:#fff;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.08);overflow:hidden;">
        <?php if (empty($recentTx)): ?>
            <p style="padding:2rem;text-align:center;color:#666;">Belum ada transaksi</p>
        <?php else: ?>
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="padding:1rem;text-align:left;">Kode</th>
                        <th style="padding:1rem;text-align:left;">User</th>
                        <th style="padding:1rem;text-align:left;">Jenis</th>
                        <th style="padding:1rem;text-align:right;">Nominal</th>
                        <th style="padding:1rem;text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTx as $tx): 
                        $badge = $tx['status']=='verified' ? ['background'=>'#28a745','text'=>'✅ Verified'] : 
                                ($tx['status']=='pending' ? ['background'=>'#ffc107','text'=>'⏳ Pending'] : ['background'=>'#dc3545','text'=>'❌ Failed']);
                    ?>
                    <tr style="border-bottom:1px solid #dee2e6;">
                        <td style="padding:1rem;font-family:monospace;"><?=htmlspecialchars($tx['reference_code'])?></td>
                        <td style="padding:1rem;"><?=htmlspecialchars($tx['username'])?></td>
                        <td style="padding:1rem;"><?=ucfirst($tx['type'])?> <?= $tx['zakat_type'] ? '('.$tx['zakat_type'].')' : '' ?></td>
                        <td style="padding:1rem;text-align:right;font-weight:600;">Rp <?=number_format($tx['amount'],0,',','.')?></td>
                        <td style="padding:1rem;text-align:center;"><span style="background:<?=$badge['background']?>;color:#fff;padding:0.3rem 0.7rem;border-radius:12px;font-size:0.85rem;"><?=$badge['text']?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>