<?php
// =================================================================
// 1. INISIALISASI & KEAMANAN
// =================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Cek keamanan
$currentRole = $_SESSION['role'] ?? '';
if (!isset($_SESSION['user_id']) || !in_array($currentRole, ['super_admin', 'admin'])) {
    header('Location: ../user/login.php');
    exit;
}

// Daftar divisi resmi Lazpersis
$officialDivisions = ['Keuangan', 'Penghimpunan', 'Pendayagunaan', 'Media'];
$divisionColors = [
    'Keuangan'        => '#0d6efd',
    'Penghimpunan'    => '#198754',
    'Pendayagunaan'   => '#fd7e14',
    'Media'           => '#6f42c1'
];

// =================================================================
// 2. PROSES FORM SUBMIT
// =================================================================

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf = $_POST['csrf'] ?? '';
    
    if (!verifyCsrf($csrf)) {
        $errors[] = '⚠️ Token keamanan tidak valid';
    } else {
        $action = $_POST['action'];
        
        if ($action === 'add_activity') {
            $division = trim($_POST['division'] ?? '');
            $activity = trim($_POST['activity'] ?? '');
            $status = $_POST['status'] ?? 'direncanakan';
            
            // Validasi divisi resmi
            if (!in_array($division, $officialDivisions)) {
                $errors[] = 'Divisi tidak valid. Silakan pilih dari daftar yang tersedia.';
            }
            if (empty($activity)) {
                $errors[] = 'Deskripsi kegiatan wajib diisi.';
            }
            if (!in_array($status, ['direncanakan', 'berjalan', 'selesai'])) {
                $status = 'direncanakan';
            }
            
            if (empty($errors)) {
                try {
                    // Pastikan tabel ada
                    $pdo->exec("CREATE TABLE IF NOT EXISTS monitoring_logs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        division VARCHAR(50) NOT NULL,
                        activity TEXT NOT NULL,
                        status ENUM('direncanakan', 'berjalan', 'selesai') DEFAULT 'direncanakan',
                        created_by INT NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
                    )");
                    
                    $stmt = $pdo->prepare("INSERT INTO monitoring_logs (division, activity, status, created_by) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$division, $activity, $status, $_SESSION['user_id']]);
                    $success = '✅ Kegiatan divisi <strong>' . htmlspecialchars($division) . '</strong> berhasil dicatat!';
                } catch (PDOException $e) {
                    $errors[] = '❌ Gagal menyimpan: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'update_status') {
            $logId = (int)($_POST['log_id'] ?? 0);
            $newStatus = $_POST['new_status'] ?? '';
            
            if ($logId > 0 && in_array($newStatus, ['direncanakan', 'berjalan', 'selesai'])) {
                try {
                    $stmt = $pdo->prepare("UPDATE monitoring_logs SET status = ? WHERE id = ?");
                    $stmt->execute([$newStatus, $logId]);
                    $success = '✅ Status kegiatan berhasil diperbarui.';
                } catch (PDOException $e) {
                    $errors[] = '❌ Error: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'delete') {
            $logId = (int)($_POST['log_id'] ?? 0);
            if ($logId > 0) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM monitoring_logs WHERE id = ?");
                    $stmt->execute([$logId]);
                    $success = '️ Data kegiatan berhasil dihapus.';
                } catch (PDOException $e) {
                    $errors[] = '❌ Error: ' . $e->getMessage();
                }
            }
        }
    }
}

// =================================================================
// 3. AMBIL DATA & FILTER
// =================================================================

$filterDivision = $_GET['division'] ?? 'all';
$filterStatus   = $_GET['status'] ?? 'all';

try {
    $sql = "SELECT m.*, u.username FROM monitoring_logs m 
            JOIN users u ON m.created_by = u.id WHERE 1=1";
    $params = [];
    
    if ($filterDivision !== 'all') {
        $sql .= " AND m.division = ?";
        $params[] = $filterDivision;
    }
    if ($filterStatus !== 'all') {
        $sql .= " AND m.status = ?";
        $params[] = $filterStatus;
    }
    
    $sql .= " ORDER BY m.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = [];
}

// Generate CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$judul = 'Monitoring Divisi - Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- =================================================================
     4. TAMPILAN HTML
     ================================================================= -->

<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <a href="dashboard.php" style="color: #0b5345; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                ← Kembali ke Dashboard
            </a>
            <h1 style="margin: 0;">📋 Monitoring Divisi Lazpersis</h1>
            <p style="color: #666; margin: 0.5rem 0 0 0;">Pantau progress & aktivitas 4 divisi utama</p>
        </div>
    </div>

    <!-- Notifikasi -->
    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php foreach ($errors as $err): ?><div><?= htmlspecialchars($err) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form Tambah Kegiatan -->
    <div style="background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem;">
        <h2 style="margin-top: 0; margin-bottom: 1rem; color: #0b5345; font-size: 1.25rem;">➕ Catat Kegiatan Divisi</h2>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="add_activity">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem; font-size: 0.9rem;">Divisi <span style="color:#dc3545">*</span></label>
                    <select name="division" required style="width:100%; padding:0.6rem; border:1px solid #ced4da; border-radius:5px;">
                        <option value="">-- Pilih Divisi --</option>
                        <?php foreach ($officialDivisions as $div): ?>
                            <option value="<?= $div ?>" <?= $filterDivision === $div ? 'selected' : '' ?>><?= $div ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.4rem; font-size: 0.9rem;">Status <span style="color:#dc3545">*</span></label>
                    <select name="status" required style="width:100%; padding:0.6rem; border:1px solid #ced4da; border-radius:5px;">
                        <option value="direncanakan">📅 Direncanakan</option>
                        <option value="berjalan">🔄 Berjalan</option>
                        <option value="selesai">✅ Selesai</option>
                    </select>
                </div>
                <div style="display:flex; align-items:end;">
                    <button type="submit" style="width:100%; background:#0b5345; color:#fff; border:none; padding:0.65rem; border-radius:5px; font-weight:600; cursor:pointer;">
                        💾 Simpan
                    </button>
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.4rem; font-size: 0.9rem;">Deskripsi Kegiatan <span style="color:#dc3545">*</span></label>
                <textarea name="activity" rows="2" placeholder="Contoh: Rekapitulasi dana zakat bulan Mei, Sosialisasi program ke masjid, dll." required style="width:100%; padding:0.6rem; border:1px solid #ced4da; border-radius:5px; resize:vertical;"></textarea>
            </div>
        </form>
    </div>

    <!-- Filter & Daftar -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:1rem;">
        <h2 style="margin:0;">📊 Riwayat Kegiatan</h2>
        <form method="GET" style="display:flex; gap:0.5rem;">
            <select name="division" onchange="this.form.submit()" style="padding:0.4rem; border:1px solid #ced4da; border-radius:4px;">
                <option value="all">Semua Divisi</option>
                <?php foreach ($officialDivisions as $d): ?>
                    <option value="<?= $d ?>" <?= $filterDivision === $d ? 'selected' : '' ?>><?= $d ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" onchange="this.form.submit()" style="padding:0.4rem; border:1px solid #ced4da; border-radius:4px;">
                <option value="all">Semua Status</option>
                <option value="direncanakan" <?= $filterStatus === 'direncanakan' ? 'selected' : '' ?>>📅 Direncanakan</option>
                <option value="berjalan" <?= $filterStatus === 'berjalan' ? 'selected' : '' ?>>🔄 Berjalan</option>
                <option value="selesai" <?= $filterStatus === 'selesai' ? 'selected' : '' ?>>✅ Selesai</option>
            </select>
            <?php if ($filterDivision !== 'all' || $filterStatus !== 'all'): ?>
                <a href="monitoring.php" style="background:#6c757d; color:#fff; text-decoration:none; padding:0.4rem 0.8rem; border-radius:4px; font-size:0.85rem;">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($logs)): ?>
        <div style="background:#f8f9fa; padding:2rem; text-align:center; border-radius:8px;">
            <p style="color:#666; margin:0;">📭 Belum ada kegiatan yang dicatat untuk filter ini.</p>
        </div>
    <?php else: ?>
        <div style="display:grid; gap:1rem;">
            <?php foreach ($logs as $log): 
                $color = $divisionColors[$log['division']] ?? '#6c757d';
            ?>
                <div style="background:#fff; padding:1.25rem; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.06); border-left:5px solid <?= $color ?>;">
                    <div style="display:flex; justify-content:space-between; align-items:start; gap:1rem; flex-wrap:wrap;">
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem;">
                                <span style="background:<?= $color ?>; color:#fff; padding:0.2rem 0.6rem; border-radius:12px; font-size:0.8rem; font-weight:600;">
                                    <?= htmlspecialchars($log['division']) ?>
                                </span>
                                <span style="font-size:0.85rem; color:#666;">
                                    <?= $log['status'] === 'selesai' ? '✅' : ($log['status'] === 'berjalan' ? '🔄' : '📅') ?> 
                                    <?= ucfirst($log['status']) ?>
                                </span>
                            </div>
                            <p style="margin:0; color:#333; line-height:1.5;"><?= nl2br(htmlspecialchars($log['activity'])) ?></p>
                        </div>
                        <div style="text-align:right; font-size:0.85rem; color:#666; min-width:120px;">
                            <div>👤 <?= htmlspecialchars($log['username']) ?></div>
                            <div>📅 <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></div>
                            
                            <div style="margin-top:0.5rem; display:flex; gap:0.4rem; justify-content:flex-end;">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                                    <select name="new_status" onchange="this.form.submit()" style="padding:0.3rem; font-size:0.8rem; border:1px solid #ced4da; border-radius:4px;">
                                        <option value="direncanakan" <?= $log['status']=='direncanakan'?'selected':'' ?>></option>
                                        <option value="berjalan" <?= $log['status']=='berjalan'?'selected':'' ?>>🔄</option>
                                        <option value="selesai" <?= $log['status']=='selesai'?'selected':'' ?>>✅</option>
                                    </select>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus catatan ini?')">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                                    <button type="submit" style="background:#dc3545; color:#fff; border:none; padding:0.3rem 0.5rem; border-radius:4px; cursor:pointer; font-size:0.8rem;">🗑️</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>