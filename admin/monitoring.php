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

// =================================================================
// 2. PROSES FORM SUBMIT (Tambah Activity)
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
            
            if (empty($division)) {
                $errors[] = 'Nama divisi wajib diisi';
            }
            if (empty($activity)) {
                $errors[] = 'Kegiatan wajib diisi';
            }
            if (!in_array($status, ['direncanakan', 'berjalan', 'selesai'])) {
                $status = 'direncanakan';
            }
            
            if (empty($errors)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO monitoring_logs (division, activity, status, created_by) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$division, $activity, $status, $_SESSION['user_id']]);
                    $success = '✅ Kegiatan berhasil ditambahkan!';
                } catch (PDOException $e) {
                    // Jika tabel belum ada, buat tabel dulu
                    try {
                        $pdo->exec("CREATE TABLE IF NOT EXISTS monitoring_logs (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            division VARCHAR(100) NOT NULL,
                            activity TEXT NOT NULL,
                            status ENUM('direncanakan', 'berjalan', 'selesai') DEFAULT 'direncanakan',
                            created_by INT NOT NULL,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
                        )");
                        
                        $stmt = $pdo->prepare("INSERT INTO monitoring_logs (division, activity, status, created_by) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$division, $activity, $status, $_SESSION['user_id']]);
                        $success = '✅ Kegiatan berhasil ditambahkan!';
                    } catch (PDOException $e2) {
                        $errors[] = '❌ Error: ' . $e2->getMessage();
                    }
                }
            }
        } elseif ($action === 'update_status') {
            $logId = (int)($_POST['log_id'] ?? 0);
            $newStatus = $_POST['new_status'] ?? '';
            
            if ($logId > 0 && in_array($newStatus, ['direncanakan', 'berjalan', 'selesai'])) {
                try {
                    $stmt = $pdo->prepare("UPDATE monitoring_logs SET status = ? WHERE id = ?");
                    $stmt->execute([$newStatus, $logId]);
                    $success = '✅ Status berhasil diupdate!';
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
                    $success = '✅ Data berhasil dihapus!';
                } catch (PDOException $e) {
                    $errors[] = '❌ Error: ' . $e->getMessage();
                }
            }
        }
    }
}

// =================================================================
// 3. AMBIL DATA MONITORING
// =================================================================

try {
    $stmt = $pdo->query("
        SELECT m.*, u.username 
        FROM monitoring_logs m 
        JOIN users u ON m.created_by = u.id 
        ORDER BY m.created_at DESC
    ");
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    $logs = [];
}

// Generate CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Judul halaman
$judul = 'Monitoring Divisi - Admin';

// Load header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- =================================================================
     4. TAMPILAN HTML
     ================================================================= -->

<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="dashboard.php" style="color: #0b5345; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                ← Kembali ke Dashboard
            </a>
            <h1 style="margin: 0;">📋 Monitoring Divisi Lazpersis</h1>
            <p style="color: #666; margin: 0.5rem 0 0 0;">Pantau aktivitas dan progress setiap divisi</p>
        </div>
    </div>

    <!-- Pesan Sukses -->
    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- Pesan Error -->
    <?php if (!empty($errors)): ?>
        <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form Tambah Activity -->
    <div style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h2 style="margin-top: 0; margin-bottom: 1.5rem; color: #0b5345;">➕ Tambah Kegiatan Baru</h2>
        
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="add_activity">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Nama Divisi <span style="color: #dc3545;">*</span></label>
                    <input 
                        type="text" 
                        name="division" 
                        placeholder="Contoh: Penyaluran, Pendidikan, Kesehatan"
                        required
                        style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; box-sizing: border-box;"
                    >
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Status <span style="color: #dc3545;">*</span></label>
                    <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; box-sizing: border-box;">
                        <option value="direncanakan">📅 Direncanakan</option>
                        <option value="berjalan">🔄 Berjalan</option>
                        <option value="selesai">✅ Selesai</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Kegiatan <span style="color: #dc3545;">*</span></label>
                <textarea 
                    name="activity" 
                    rows="3" 
                    placeholder="Deskripsi kegiatan..."
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; box-sizing: border-box; resize: vertical;"
                ></textarea>
            </div>
            
            <button type="submit" style="background: #0b5345; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 5px; font-weight: 600; cursor: pointer;">
                💾 Simpan Kegiatan
            </button>
        </form>
    </div>

    <!-- Daftar Monitoring -->
    <h2 style="margin-bottom: 1rem;">📊 Daftar Kegiatan</h2>
    
    <?php if (empty($logs)): ?>
        <div style="background: #f8f9fa; padding: 2rem; text-align: center; border-radius: 8px;">
            <p style="color: #666; margin: 0;">📭 Belum ada kegiatan yang dicatat</p>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 1rem;">
            <?php foreach ($logs as $log): 
                $statusColors = [
                    'direncanakan' => '#ffc107',
                    'berjalan' => '#17a2b8',
                    'selesai' => '#28a745'
                ];
                $statusLabels = [
                    'direncanakan' => '📅 Direncanakan',
                    'berjalan' => '🔄 Berjalan',
                    'selesai' => '✅ Selesai'
                ];
            ?>
                <div style="background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid <?= $statusColors[$log['status']] ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="margin: 0 0 0.5rem 0; color: #0b5345;">
                                <?= htmlspecialchars($log['division']) ?>
                            </h3>
                            <p style="margin: 0; color: #555; line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($log['activity'])) ?>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: inline-block; padding: 0.4rem 0.8rem; background: <?= $statusColors[$log['status']] ?>; color: white; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                <?= $statusLabels[$log['status']] ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid #dee2e6; font-size: 0.9rem; color: #666;">
                        <div>
                            <strong>Oleh:</strong> <?= htmlspecialchars($log['username']) ?> | 
                            <strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </div>
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <!-- Update Status -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                                <select name="new_status" onchange="this.form.submit()" style="padding: 0.4rem; border: 1px solid #ced4da; border-radius: 4px; font-size: 0.85rem;">
                                    <option value="direncanakan" <?= $log['status'] === 'direncanakan' ? 'selected' : '' ?>>📅 Direncanakan</option>
                                    <option value="berjalan" <?= $log['status'] === 'berjalan' ? 'selected' : '' ?>>🔄 Berjalan</option>
                                    <option value="selesai" <?= $log['status'] === 'selesai' ? 'selected' : '' ?>>✅ Selesai</option>
                                </select>
                            </form>
                            
                            <!-- Delete -->
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Hapus kegiatan ini?')">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                                <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php 
require_once __DIR__ . '/../includes/footer.php'; 
?>