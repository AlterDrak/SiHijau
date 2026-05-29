<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['super_admin', 'admin'])) {
    header('Location: ../user/login.php'); exit;
}

// Statistik Penghimpunan
try {
    $totalMuzakki = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM transactions WHERE status='verified'")->fetchColumn();
    $totalZakat = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type='zakat' AND status='verified'")->fetchColumn();
    $totalInfaq = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type='shadaqah' AND status='verified'")->fetchColumn();
    $targetBulanan = 50000000; // Contoh target
    $realisasiBulanIni = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE status='verified' AND MONTH(created_at)=MONTH(CURRENT_DATE())")->fetchColumn();
    $persentase = $targetBulanan > 0 ? ($realisasiBulanIni / $targetBulanan * 100) : 0;
} catch (PDOException $e) {
    $totalMuzakki = $totalZakat = $totalInfaq = $realisasiBulanIni = 0;
    $persentase = 0;
}

$judul = 'Dashboard Penghimpunan - Lazpersis';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <div style="margin-bottom:2rem;">
        <a href="dashboard.php" style="color:#0b5345;text-decoration:none;">← Kembali ke Admin Panel</a>
        <h1 style="margin:0.5rem 0 0 0;">🤲 Dashboard Divisi Penghimpunan</h1>
        <p style="color:#666;margin:0;">Monitoring penghimpunan zakat, infaq, dan shadaqah</p>
    </div>

    <!-- Statistik -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:2rem;">
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #28a745;">
            <div style="color:#666;font-size:0.9rem;">👥 Total Muzakki</div>
            <div style="font-size:2.5rem;font-weight:bold;color:#28a745;margin:0.5rem 0;"><?=$totalMuzakki?></div>
            <div style="color:#666;font-size:0.85rem;">Donatur terdaftar</div>
        </div>
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #0d6efd;">
            <div style="color:#666;font-size:0.9rem;">💰 Total Zakat</div>
            <div style="font-size:2rem;font-weight:bold;color:#0d6efd;margin:0.5rem 0;">Rp <?=number_format($totalZakat,0,',','.')?></div>
            <div style="color:#666;font-size:0.85rem;">Terkumpul</div>
        </div>
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #fd7e14;">
            <div style="color:#666;font-size:0.9rem;">🌟 Total Infaq/Shadaqah</div>
            <div style="font-size:2rem;font-weight:bold;color:#fd7e14;margin:0.5rem 0;">Rp <?=number_format($totalInfaq,0,',','.')?></div>
            <div style="color:#666;font-size:0.85rem;">Terkumpul</div>
        </div>
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #6f42c1;">
            <div style="color:#666;font-size:0.9rem;">📊 Capaian Bulan Ini</div>
            <div style="font-size:1.8rem;font-weight:bold;color:#6f42c1;margin:0.5rem 0;"><?=number_format($persentase,1)%>%</div>
            <div style="color:#666;font-size:0.85rem;">Dari Rp <?=number_format($targetBulanan,0,',','.')?></div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);margin-bottom:2rem;">
        <h3 style="margin-top:0;margin-bottom:1rem;">📈 Progress Penghimpunan Bulan <?=date('F Y')?></h3>
        <div style="background:#e9ecef;height:30px;border-radius:15px;overflow:hidden;margin-bottom:0.5rem;">
            <div style="background:linear-gradient(90deg,#28a745,#20c997);height:100%;width:<?=$persentase>100?100:$persentase?>%;transition:width 0.5s;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:bold;"><?=number_format($persentase>100?100:$persentase,1)%>?></div>
        </div>
        <div style="display:flex;justify-content:space-between;color:#666;font-size:0.9rem;">
            <span>Target: Rp <?=number_format($targetBulanan,0,',','.')?></span>
            <span>Realisasi: Rp <?=number_format($realisasiBulanIni,0,',','.')?></span>
        </div>
    </div>

    <!-- Menu -->
    <h3 style="margin-bottom:1rem;">📋 Menu Penghimpunan</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
        <a href="transactions.php?type=zakat" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #0d6efd;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">💰 Data Zakat</div>
            <div style="color:#666;font-size:0.9rem;">Lihat semua transaksi zakat</div>
        </a>
        <a href="transactions.php?type=shadaqah" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #fd7e14;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">🌟 Data Infaq/Shadaqah</div>
            <div style="color:#666;font-size:0.9rem;">Lihat semua infaq & shadaqah</div>
        </a>
        <a href="monitoring.php?division=Penghimpunan" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #28a745;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📝 Aktivitas Divisi</div>
            <div style="color:#666;font-size:0.9rem;">Catat kegiatan penghimpunan</div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>