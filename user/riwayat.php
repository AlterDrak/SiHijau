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
// 2. PROSES VERIFY TRANSACTION (Hanya Super Admin)
// =================================================================

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $currentRole === 'super_admin') {
    $csrf = $_POST['csrf'] ?? '';
    
    if (!verifyCsrf($csrf)) {
        $errors[] = '⚠️ Token keamanan tidak valid';
    } else {
        $action = $_POST['action'] ?? '';
        $txId = (int)($_POST['tx_id'] ?? 0);
        
        if ($txId > 0) {
            try {
                if ($action === 'verify') {
                    $stmt = $pdo->prepare("UPDATE transactions SET status = 'verified', verified_at = NOW() WHERE id = ?");
                    $stmt->execute([$txId]);
                    $success = '✅ Transaksi berhasil diverifikasi!';
                } elseif ($action === 'reject') {
                    $stmt = $pdo->prepare("UPDATE transactions SET status = 'failed' WHERE id = ?");
                    $stmt->execute([$txId]);
                    $success = '❌ Transaksi ditolak.';
                }
            } catch (PDOException $e) {
                // Jika tabel belum ada
                $errors[] = '⚠️ Tabel transaksi belum ada';
            }
        }
    }
}

// =================================================================
// 3. AMBIL DATA TRANSAKSI
// =================================================================

// Filter
$filterStatus = $_GET['status'] ?? 'all';
$filterType = $_GET['type'] ?? 'all';

try {
    $sql = "SELECT t.*, u.username 
            FROM transactions t 
            JOIN users u ON t.user_id = u.id 
            WHERE 1=1";
    
    $params = [];
    
    if ($filterStatus !== 'all') {
        $sql .= " AND t.status = ?";
        $params[] = $filterStatus;
    }
    
    if ($filterType !== 'all') {
        $sql .= " AND t.type = ?";
        $params[] = $filterType;
    }
    
    $sql .= " ORDER BY t.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    $transactions = [];
}

// Generate CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Judul halaman
$judul = 'Cek Transaksi - Admin';

// Load header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- =================================================================
     4. TAMPILAN HTML
     ================================================================= -->

<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    
    <!-- Header -->
    <div>
        <a href="dashboard.php" style="color: #0b5345; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            ← Kembali ke Dashboard
        </a>
        <h1 style="margin: 0;">💰 Daftar Transaksi</h1>
        <p style="color: #666; margin: 0.5rem 0 0 0;">Monitoring semua transaksi zakat & shadaqah</p>
    </div>

    <!-- Pesan -->
    <?php if ($success): ?>
        <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 1rem; border-radius: 5px; margin: 1rem 0;">
            <?= $success ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 5px; margin: 1rem 0;">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Filter -->
    <form method="GET" style="background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 1.5rem 0; display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Status</label>
            <select name="status" style="padding: 0.6rem; border: 1px solid #ced4da; border-radius: 4px; min-width: 150px;">
                <option value="all" <?= $filterStatus === 'all' ? 'selected' : '' ?>>Semua Status</option>
                <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                <option value="verified" <?= $filterStatus === 'verified' ? 'selected' : '' ?>>✅ Verified</option>
                <option value="failed" <?= $filterStatus === 'failed' ? 'selected' : '' ?>>❌ Failed</option>
            </select>
        </div>
        
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">Jenis</label>
            <select name="type" style="padding: 0.6rem; border: 1px solid #ced4da; border-radius: 4px; min-width: 150px;">
                <option value="all" <?= $filterType === 'all' ? 'selected' : '' ?>>Semua Jenis</option>
                <option value="zakat" <?= $filterType === 'zakat' ? 'selected' : '' ?>>Zakat</option>
                <option value="shadaqah" <?= $filterType === 'shadaqah' ? 'selected' : '' ?>>Shadaqah</option>
            </select>
        </div>
        
        <button type="submit" style="background: #0b5345; color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: 600;">
            🔍 Filter
        </button>
        
        <a href="transactions.php" style="background: #6c757d; color: white; text-decoration: none; padding: 0.6rem 1.5rem; border-radius: 4px; font-weight: 600;">
            Reset
        </a>
    </form>

    <!-- Statistik -->
    <?php if (!empty($transactions)): 
        $totalPending = count(array_filter($transactions, fn($t) => $t['status'] === 'pending'));
        $totalVerified = count(array_filter($transactions, fn($t) => $t['status'] === 'verified'));
        $totalAmount = array_sum(array_map(fn($t) => $t['status'] === 'verified' ? $t['amount'] : 0, $transactions));
    ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1.5rem 0;">
            <div style="background: #fff3cd; padding: 1rem; border-radius: 8px; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #856404;"><?= $totalPending ?></div>
                <div style="color: #856404; font-size: 0.9rem;">Pending</div>
            </div>
            <div style="background: #d4edda; padding: 1rem; border-radius: 8px; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #155724;"><?= $totalVerified ?></div>
                <div style="color: #155724; font-size: 0.9rem;">Verified</div>
            </div>
            <div style="background: #d1ecf1; padding: 1rem; border-radius: 8px; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #0c5460;">Rp <?= number_format($totalAmount, 0, ',', '.') ?></div>
                <div style="color: #0c5460; font-size: 0.9rem;">Total Terverifikasi</div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabel Transaksi -->
    <?php if (empty($transactions)): ?>
        <div style="background: #f8f9fa; padding: 3rem; text-align: center; border-radius: 8px;">
            <p style="color: #666; font-size: 1.1rem; margin: 0;">📭 Belum ada transaksi</p>
        </div>
    <?php else: ?>
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #0b5345; color: white;">
                    <tr>
                        <th style="padding: 1rem; text-align: left;">Kode</th>
                        <th style="padding: 1rem; text-align: left;">User</th>
                        <th style="padding: 1rem; text-align: left;">Jenis</th>
                        <th style="padding: 1rem; text-align: right;">Nominal</th>
                        <th style="padding: 1rem; text-align: left;">Status</th>
                        <th style="padding: 1rem; text-align: left;">Tanggal</th>
                        <?php if ($currentRole === 'super_admin'): ?>
                            <th style="padding: 1rem; text-align: center;">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): 
                        $statusColors = [
                            'pending' => '#ffc107',
                            'verified' => '#28a745',
                            'failed' => '#dc3545'
                        ];
                        $statusLabels = [
                            'pending' => '⏳ Pending',
                            'verified' => '✅ Verified',
                            'failed' => '❌ Failed'
                        ];
                    ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 1rem; font-family: monospace; font-size: 0.9rem;">
                                <?= htmlspecialchars($tx['reference_code'] ?? 'N/A') ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?= htmlspecialchars($tx['username']) ?>
                            </td>
                            <td style="padding: 1rem;">
                                <strong><?= ucfirst($tx['type']) ?></strong>
                                <?php if ($tx['zakat_type']): ?>
                                    <br><small style="color: #666;">(<?= ucfirst($tx['zakat_type']) ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: 600;">
                                Rp <?= number_format($tx['amount'], 0, ',', '.') ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="display: inline-block; padding: 0.3rem 0.7rem; background: <?= $statusColors[$tx['status']] ?>; color: white; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <?= $statusLabels[$tx['status']] ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #666;">
                                <?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?>
                            </td>
                            <?php if ($currentRole === 'super_admin'): ?>
                                <td style="padding: 1rem; text-align: center;">
                                    <?php if ($tx['status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                            <input type="hidden" name="action" value="verify">
                                            <input type="hidden" name="tx_id" value="<?= $tx['id'] ?>">
                                            <button type="submit" style="background: #28a745; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" onclick="return confirm('Verifikasi transaksi ini?')">
                                                ✓ Verifikasi
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline; margin-top: 0.3rem;">
                                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="tx_id" value="<?= $tx['id'] ?>">
                                            <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" onclick="return confirm('Tolak transaksi ini?')">
                                                ✗ Tolak
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #adb5bd; font-size: 0.85rem;">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

<?php 
require_once __DIR__ . '/../includes/footer.php'; 
?>