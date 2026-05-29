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

// Judul halaman
$judul = 'Kelola Pengguna - Admin';

// =================================================================
// 2. PROSES AKSI (Tambah, Aktifkan, Nonaktifkan)
// =================================================================

$message = '';
$messageType = '';

// Proses toggle status user (hanya untuk super_admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf = $_POST['csrf'] ?? '';
    
    // Verify CSRF (optional, untuk keamanan lebih)
    if (!verifyCsrf($csrf)) {
        $message = '⚠️ Token keamanan tidak valid';
        $messageType = 'error';
    } else {
        $action = $_POST['action'];
        $userId = (int)($_POST['user_id'] ?? 0);
        
        if ($userId > 0 && $userId !== $_SESSION['user_id']) {
            try {
                if ($action === 'toggle' && $currentRole === 'super_admin') {
                    // Super admin bisa aktifkan/nonaktifkan
                    $stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$userId]);
                    $message = '✅ Status user berhasil diubah';
                    $messageType = 'success';
                } elseif ($action === 'activate' && $userId !== $_SESSION['user_id']) {
                    // Admin biasa hanya bisa mengaktifkan
                    $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ? AND is_active = 0");
                    $stmt->execute([$userId]);
                    $message = '✅ User berhasil diaktifkan';
                    $messageType = 'success';
                }
            } catch (PDOException $e) {
                $message = '❌ Error: ' . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message = '❌ Tidak dapat mengubah status akun Anda sendiri';
            $messageType = 'error';
        }
    }
}

// =================================================================
// 3. AMBIL DATA USER DARI DATABASE
// =================================================================

try {
    $stmt = $pdo->query("
        SELECT id, username, role, is_active, created_at 
        FROM users 
        ORDER BY 
            FIELD(role, 'super_admin', 'admin', 'user'),
            username ASC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $message = '⚠️ Tabel users belum ada atau error: ' . $e->getMessage();
    $messageType = 'error';
}

// Generate CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Load header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- =================================================================
     4. TAMPILAN HTML
     ================================================================= -->

<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="margin: 0;">👥 Manajemen Pengguna</h1>
        <a href="add_user.php" class="btn" style="background: #0b5345; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 1.2rem;">+</span> Tambah User
        </a>
    </div>

    <!-- Pesan Notifikasi -->
    <?php if ($message): ?>
        <div style="padding: 1rem; margin-bottom: 1.5rem; border-radius: 5px; <?= $messageType === 'error' ? 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Info Role -->
    <div style="background: #e9ecef; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
        <strong>ℹ️ Info:</strong> 
        <?php if ($currentRole === 'super_admin'): ?>
            Anda memiliki akses penuh untuk menambah, mengaktifkan, dan menonaktifkan semua user.
        <?php else: ?>
            Anda hanya dapat menambah user dan mengaktifkan user yang nonaktif.
        <?php endif; ?>
    </div>

    <!-- Tabel User -->
    <?php if (empty($users)): ?>
        <div style="background: #fff; padding: 2rem; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p style="color: #666; font-size: 1.1rem;">📭 Belum ada data pengguna</p>
            <a href="add_user.php" class="btn" style="margin-top: 1rem; display: inline-block;">Tambah User Pertama</a>
        </div>
    <?php else: ?>
        <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #0b5345; color: white;">
                    <tr>
                        <th style="padding: 1rem; text-align: left;">Username</th>
                        <th style="padding: 1rem; text-align: left;">Role</th>
                        <th style="padding: 1rem; text-align: left;">Status</th>
                        <th style="padding: 1rem; text-align: left;">Terdaftar</th>
                        <th style="padding: 1rem; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $roleLabels = [
                        'super_admin' => '👑 Super Admin',
                        'admin' => '🔐 Admin',
                        'user' => '👤 User'
                    ];
                    
                    foreach ($users as $user): 
                        $isSelf = ($user['id'] == $_SESSION['user_id']);
                        $canToggle = ($currentRole === 'super_admin' && !$isSelf);
                        $canActivate = ($currentRole === 'admin' && $user['is_active'] == 0 && !$isSelf);
                    ?>
                        <tr style="border-bottom: 1px solid #dee2e6; <?= $isSelf ? 'background: #fff3cd;' : '' ?>">
                            <td style="padding: 1rem;">
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                                <?php if ($isSelf): ?>
                                    <span style="font-size: 0.8em; color: #856404;">(Anda)</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: #e9ecef; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.9em;">
                                    <?= $roleLabels[$user['role']] ?? ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($user['is_active']): ?>
                                    <span style="color: #28a745; font-weight: bold;">✓ Aktif</span>
                                <?php else: ?>
                                    <span style="color: #dc3545; font-weight: bold;">✗ Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; color: #666;">
                                <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <?php if ($canToggle): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                        <input type="hidden" name="action" value="toggle">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" 
                                                style="padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; <?= $user['is_active'] ? 'background: #dc3545; color: white;' : 'background: #28a745; color: white;' ?>"
                                                onclick="return confirm('<?= $user['is_active'] ? 'Nonaktifkan user ini?' : 'Aktifkan user ini?' ?>')">
                                            <?= $user['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>
                                <?php elseif ($canActivate): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <button type="submit" 
                                                style="padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; background: #28a745; color: white;">
                                            Aktifkan
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #adb5bd; font-size: 0.9em;">-</span>
                                <?php endif; ?>
                            </td>
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