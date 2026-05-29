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
// 2. PROSES FORM SUBMIT
// =================================================================

$errors = [];
$success = '';
$formData = [
    'username' => '',
    'role' => 'user'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    
    if (!verifyCsrf($csrf)) {
        $errors[] = '⚠️ Token keamanan tidak valid';
    } else {
        // Ambil data dari form
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Tentukan role yang diizinkan
        if ($currentRole === 'super_admin') {
            $allowedRoles = ['super_admin', 'admin', 'user'];
            $selectedRole = $_POST['role'] ?? 'user';
        } else {
            // Admin biasa hanya bisa membuat user
            $allowedRoles = ['user'];
            $selectedRole = 'user';
        }
        
        $role = in_array($selectedRole, $allowedRoles) ? $selectedRole : 'user';
        
        // Validasi
        if (empty($username)) {
            $errors[] = 'Username wajib diisi';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username hanya boleh mengandung huruf, angka, dan underscore';
        }
        
        if (empty($password)) {
            $errors[] = 'Password wajib diisi';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Password konfirmasi tidak cocok';
        }
        
        // Jika tidak ada error, simpan ke database
        if (empty($errors)) {
            try {
                // Cek apakah username sudah ada
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $checkStmt->execute([$username]);
                
                if ($checkStmt->rowCount() > 0) {
                    $errors[] = 'Username sudah digunakan, silakan pilih username lain';
                } else {
                    // Hash password dan insert
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, is_active) VALUES (?, ?, ?, 1)");
                    $stmt->execute([$username, $passwordHash, $role]);
                    
                    $success = "✅ User <strong>" . htmlspecialchars($username) . "</strong> berhasil ditambahkan!";
                    
                    // Reset form data
                    $formData = ['username' => '', 'role' => 'user'];
                }
            } catch (PDOException $e) {
                $errors[] = '❌ Error database: ' . $e->getMessage();
            }
        } else {
            // Simpan data form untuk ditampilkan kembali
            $formData = [
                'username' => $username,
                'role' => $role
            ];
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Judul halaman
$judul = 'Tambah Pengguna - Admin';

// Load header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- =================================================================
     3. TAMPILAN HTML FORM
     ================================================================= -->

<div class="container" style="max-width: 600px; margin: 2rem auto; padding: 0 1rem;">
    
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <a href="users.php" style="color: #0b5345; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
            ← Kembali ke Daftar User
        </a>
        <h1 style="margin: 0;">➕ Tambah Pengguna Baru</h1>
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
            <strong>⚠️ Terdapat kesalahan:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Info Role -->
    <div style="background: #e9ecef; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem; font-size: 0.95rem;">
        <strong>ℹ️ Info:</strong> 
        <?php if ($currentRole === 'super_admin'): ?>
            Anda dapat membuat user dengan role: <strong>User, Admin, atau Super Admin</strong>
        <?php else: ?>
            Sebagai Admin, Anda hanya dapat membuat akun dengan role <strong>User</strong>
        <?php endif; ?>
    </div>

    <!-- Form Tambah User -->
    <form method="POST" style="background: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        
        <!-- Username -->
        <div style="margin-bottom: 1.5rem;">
            <label for="username" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                Username <span style="color: #dc3545;">*</span>
            </label>
            <input 
                type="text" 
                id="username" 
                name="username" 
                value="<?= htmlspecialchars($formData['username']) ?>"
                placeholder="Contoh: johndoe atau user123"
                required
                style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; font-size: 1rem; box-sizing: border-box;"
            >
            <small style="color: #666; font-size: 0.85rem;">Huruf, angka, dan underscore saja. Minimal 3 karakter.</small>
        </div>

        <!-- Password -->
        <div style="margin-bottom: 1.5rem;">
            <label for="password" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                Password <span style="color: #dc3545;">*</span>
            </label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Minimal 6 karakter"
                required
                style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; font-size: 1rem; box-sizing: border-box;"
            >
        </div>

        <!-- Konfirmasi Password -->
        <div style="margin-bottom: 1.5rem;">
            <label for="confirm_password" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                Konfirmasi Password <span style="color: #dc3545;">*</span>
            </label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                placeholder="Ketik ulang password"
                required
                style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; font-size: 1rem; box-sizing: border-box;"
            >
        </div>

        <!-- Role (Hanya untuk Super Admin) -->
        <?php if ($currentRole === 'super_admin'): ?>
        <div style="margin-bottom: 1.5rem;">
            <label for="role" style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333;">
                Role Pengguna <span style="color: #dc3545;">*</span>
            </label>
            <select 
                id="role" 
                name="role" 
                required
                style="width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 5px; font-size: 1rem; box-sizing: border-box; background: white;"
            >
                <option value="user" <?= $formData['role'] === 'user' ? 'selected' : '' ?>>👤 User (Pengguna Biasa)</option>
                <option value="admin" <?= $formData['role'] === 'admin' ? 'selected' : '' ?>>🔐 Admin (Pengelola)</option>
                <option value="super_admin" <?= $formData['role'] === 'super_admin' ? 'selected' : '' ?>>👑 Super Admin (Full Access)</option>
            </select>
            <small style="color: #666; font-size: 0.85rem;">Pilih tingkat akses untuk user ini</small>
        </div>
        <?php else: ?>
            <!-- Hidden field untuk admin biasa -->
            <input type="hidden" name="role" value="user">
        <?php endif; ?>

        <!-- Tombol Submit -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button 
                type="submit" 
                style="flex: 1; background: #0b5345; color: white; border: none; padding: 0.875rem; border-radius: 5px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.3s;"
                onmouseover="this.style.background='#083d32'"
                onmouseout="this.style.background='#0b5345'"
            >
                💾 Simpan User
            </button>
            <a 
                href="users.php" 
                style="flex: 1; background: #6c757d; color: white; text-align: center; text-decoration: none; padding: 0.875rem; border-radius: 5px; font-size: 1rem; font-weight: 600; display: inline-block;"
            >
                Batal
            </a>
        </div>
    </form>

</div>

<?php 
require_once __DIR__ . '/../includes/footer.php'; 
?>