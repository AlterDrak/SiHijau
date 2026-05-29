<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['super_admin', 'admin'])) {
    header('Location: ../user/login.php'); exit;
}

// Statistik Media (simulasi)
try {
    $totalKegiatan = $pdo->query("SELECT COUNT(*) FROM monitoring_logs WHERE division='Media'")->fetchColumn();
    $totalDokumentasi = $pdo->query("SELECT COUNT(*) FROM media_documents")->fetchColumn();
} catch (PDOException $e) {
    $totalKegiatan = $totalDokumentasi = 0;
}

$judul = 'Dashboard Media - Lazpersis';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width:1200px;margin:2rem auto;padding:0 1rem;">
    <div style="margin-bottom:2rem;">
        <a href="dashboard.php" style="color:#0b5345;text-decoration:none;">← Kembali ke Admin Panel</a>
        <h1 style="margin:0.5rem 0 0 0;">📱 Dashboard Divisi Media</h1>
        <p style="color:#666;margin:0;">Dokumentasi, publikasi, dan komunikasi Lazpersis</p>
    </div>

    <!-- Statistik -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;margin-bottom:2rem;">
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #6f42c1;">
            <div style="color:#666;font-size:0.9rem;">📰 Total Publikasi</div>
            <div style="font-size:2.5rem;font-weight:bold;color:#6f42c1;margin:0.5rem 0;"><?=$totalKegiatan?></div>
            <div style="color:#666;font-size:0.85rem;">Kegiatan terpublikasi</div>
        </div>
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #e83e8c;">
            <div style="color:#666;font-size:0.9rem;">📸 Dokumentasi</div>
            <div style="font-size:2.5rem;font-weight:bold;color:#e83e8c;margin:0.5rem 0;"><?=$totalDokumentasi?></div>
            <div style="color:#666;font-size:0.85rem;">File tersimpan</div>
        </div>
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #20c997;">
            <div style="color:#666;font-size:0.9rem;">📺 Media Sosial</div>
            <div style="font-size:2.5rem;font-weight:bold;color:#20c997;margin:0.5rem 0;">Active</div>
            <div style="color:#666;font-size:0.85rem;">Instagram, Facebook, YouTube</div>
        </div>
        <div style="background:#fff;padding:1.5rem;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-left:4px solid #fd7e14;">
            <div style="color:#666;font-size:0.9rem;">📢 Siaran Pers</div>
            <div style="font-size:2.5rem;font-weight:bold;color:#fd7e14;margin:0.5rem 0;">12</div>
            <div style="color:#666;font-size:0.85rem;">Tahun ini</div>
        </div>
    </div>

    <!-- Menu -->
    <h3 style="margin-bottom:1rem;">📋 Menu Divisi Media</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1rem;">
        <a href="monitoring.php?division=Media" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #6f42c1;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📝 Catat Kegiatan</div>
            <div style="color:#666;font-size:0.9rem;">Dokumentasi & publikasi kegiatan</div>
        </a>
        <a href="upload_media.php" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #e83e8c;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📤 Upload Dokumentasi</div>
            <div style="color:#666;font-size:0.9rem;">Foto, video, & dokumen</div>
        </a>
        <a href="galeri.php" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #20c997;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">🖼️ Galeri Kegiatan</div>
            <div style="color:#666;font-size:0.9rem;">Lihat semua dokumentasi</div>
        </a>
        <a href="siaran_pers.php" style="background:#fff;padding:1.5rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.08);text-decoration:none;color:inherit;border-left:4px solid #fd7e14;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">📰 Siaran Pers</div>
            <div style="color:#666;font-size:0.9rem;">Kelola berita & press release</div>
        </a>
    </div>

    <!-- Kegiatan Terbaru -->
    <h3 style="margin:2rem 0 1rem 0;">📰 Kegiatan Terbaru</h3>
    <div style="background:#f8f9fa;padding:2rem;text-align:center;border-radius:8px;">
        <p style="color:#666;margin:0;">💡 Dokumentasikan setiap kegiatan Lazpersis untuk publikasi</p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>