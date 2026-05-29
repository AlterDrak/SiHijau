<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['super_admin', 'admin'])) {
    header('Location: ../user/login.php'); exit;
}

// Statistik Pendayagunaan
try {
    $totalMustahik = $pdo->query("SELECT COUNT(DISTINCT mustahik_id) FROM distribution_records")->fetchColumn();
    $totalDisalurkan = $pdo->query("SELECT COALESCE(SUM(amount_distributed),0) FROM distribution_records")->fetchColumn();
    $asnafStats = $pdo->query("SELECT asnaf_category, COUNT(*) as count, SUM(amount_distributed) as total FROM distribution_records GROUP BY asnaf_category")->fetchAll();
    $programCount = $pdo->query("SELECT COUNT(DISTINCT program_id) FROM distribution_records")->fetchColumn();
} catch (PDOException $e) {
    $totalMustahik = $totalDisalurkan = 0;
    $asnafStats = [];
    $programCount = 0;
}

$asnafLabels = ['fakir'=>'Fakir','miskin'=>'Miskin','amil'=>'Amil','mualaf'=>'Mualaf','gharimin'=>'Gharimin','ibnu_sabil'=>'Ibnu Sabil','fisabilillah'=>'Fisabilillah','riqab'=>'Riqab'];

$judul = 'Dashboard Pendayagunaan - Lazpersis';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <div style="margin-bottom:2rem;">
        <a href="dashboard.php" style="color:#0b5345;text-decoration:none;">← Kembali ke Admin Panel</a>
        <h1 style="margin:0.5rem 0 0 0;">🎯 Dashboard Divisi Pendayagunaan</h1>
        <p style="color:#666;margin:0;">Monitoring penyaluran dana & program untuk mustahik</p>
    </div>

    <!-- Statistik -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:2rem;">
        <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">👥 Total Mustahik</div>
            <div style="font-size:2.5rem;font-weight:bold;margin:0.5rem 0;"><?=$totalMustahik?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Penerima manfaat</div>
        </div>
        <div style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">💵 Total Disalurkan</div>
            <div style="font-size:2rem;font-weight:bold;margin:0.5rem 0;">Rp <?=number_format($totalDisalurkan,0,',','.')?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Dana tersalurkan</div>
        </div>
        <div style="background:linear-gradient(135deg,#5f2c82 0%,#49a09d 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;">📦 Program Aktif</div>
            <div style="font-size:2.5rem;font-weight:bold;margin:0.5rem 0;"><?=$programCount?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Program berjalan</div>
        </div>
        <div style="background:linear-gradient(135deg,#00b09b 0%,#96c93d 100%);color:#fff;padding:1.5rem;border-radius:10px;">
            <div style="font-size:0.9rem;opacity:0.9;"> Asnaf Terlayani</div>
            <div style="font-size:2.5rem;font-weight:bold;margin:0.5rem 0;"><?=count($asnafStats)?></div>
            <div style="font-size:0.85rem;opacity:0.8;">Dari 8 asnaf</div>
        </div>
    </div>

    <!-- Distribusi per Asnaf -->
    <h3 style="margin-bottom:1rem;">📊 Distribusi Berdasarkan Asnaf</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;">
        <?php foreach ($asnafLabels as $key=>$label): 
            $stat = array_filter($asnafStats, fn($s) => $s['asnaf_category']===$key);
            $stat = !empty($stat) ? reset($stat) : ['count'=>0,'total'=>0];
        ?>
        <div style="background:#fff;padding:1.2rem;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.08);border-top:3px solid <?= $stat['count']>0 ? '#28a745' : '#dee2e6' ?>">
            <div style="font-weight:600;margin-bottom:0.5rem;"><?=$label?></div>
            <div style="font-size:1.5rem;font-weight:bold;color:#28a745;"><?=$stat['count']?></div>
            <div style="color:#666;font-size:0.85rem;">Rp <?=number_format($stat['total']??0,0,',','.')?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Menu -->
    <h3 style="margin-bottom:1rem;">📋 Menu Pendayagunaan</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
        <a href="penyaluran.php" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #11998e;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📝 Catat Penyaluran</div>
            <div style="color:#666;font-size:0.9rem;">Input data penyaluran dana</div>
        </a>
        <a href="mustahik.php" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #fc4a1a;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">👥 Data Mustahik</div>
            <div style="color:#666;font-size:0.9rem;">Kelola data penerima manfaat</div>
        </a>
        <a href="monitoring.php?division=Pendayagunaan" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #5f2c82;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📝 Aktivitas Divisi</div>
            <div style="color:#666;font-size:0.9rem;">Catat kegiatan pendayagunaan</div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>