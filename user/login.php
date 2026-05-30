<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Lazpersis Kabupaten Tasikmalaya</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f4f7f6; color: #333; line-height: 1.6; }
        .auth-wrapper { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem; }
        .auth-box { background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); width: 100%; max-width: 400px; }
        .auth-box h2 { color: #0b5345; margin-bottom: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 500; color: #444; }
        .form-group input { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: 0.3s; }
        .form-group input:focus { border-color: #0b5345; outline: none; box-shadow: 0 0 0 3px rgba(11,83,69,0.1); }
        .btn-submit { width: 100%; padding: 0.9rem; background: #0b5345; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #083d32; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.2rem; text-align: center; font-size: 0.95rem; }
        .alert-error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .alert-warning { background: #fff3cd; color: #664d03; border: 1px solid #ffe69c; }
        .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.95rem; color: #666; }
        .auth-footer a { color: #0b5345; text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <h2>Masuk ke Akun</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] == 'nonaktif'): ?>
                    <div class="alert alert-warning">⏳ Akun Anda belum diaktifkan. Silakan hubungi staff Lazpersis untuk proses aktivasi.</div>
                <?php else: ?>
                    <div class="alert alert-error">❌ Username atau password salah.</div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="process_login.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-submit">Masuk</button>
            </form>

            <div class="auth-footer">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>
</body>
</html>