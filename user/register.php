<?php
session_start();
require '../config/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                $error = 'Username sudah digunakan. Silakan pilih username lain.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // role='user', is_active=0 (menunggu aktivasi staff)
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, is_active) VALUES (?, ?, 'user', 0)");
                $stmt->execute([$username, $hash]);
                $success = '✅ Pendaftaran berhasil! Akun Anda sedang menunggu aktivasi oleh staff Lazpersis.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Lazpersis Rajapolah</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f7f6; color: #333; line-height: 1.6; }
        .auth-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem; }
        .auth-box { background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); width: 100%; max-width: 420px; }
        .auth-box h2 { color: #0b5345; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 500; color: #444; }
        .form-group input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: 0.3s; }
        .form-group input:focus { border-color: #0b5345; outline: none; box-shadow: 0 0 0 3px rgba(11,83,69,0.1); }
        .btn-submit { width: 100%; padding: 0.9rem; background: #0b5345; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #083d32; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.2rem; text-align: center; font-size: 0.95rem; }
        .alert-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .alert-error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.95rem; color: #666; }
        .auth-footer a { color: #0b5345; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <h2>📝 Daftar Akun Donatur</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <div class="auth-footer"><a href="login.php">→ Ke Halaman Login</a></div>
            <?php elseif ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" <?= $success ? 'style="display:none;"' : '' ?>>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Contoh: ahmad_zaki">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="confirm_password" required placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn-submit">Daftar Sekarang</button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>