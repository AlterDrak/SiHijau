<?php 
$judul = 'Masuk - Lazpersis'; 
require '../config/db.php';
require '../includes/header.php'; 

if (isset($_SESSION['user_id'])) { 
    // Jika sudah login, redirect sesuai role
    if (in_array($_SESSION['role'], ['super_admin', 'admin'])) {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit; 
}
?>

<div class="login-container">
    <h2>Masuk ke Akun</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] == 'nonaktif'): ?>
            <p class="error">⚠️ Akun Anda tidak aktif. Hubungi administrator.</p>
        <?php else: ?>
            <p class="error">Username atau password salah.</p>
        <?php endif; ?>
    <?php endif; ?>
    
    <form action="process_login.php" method="POST">
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        
        <label>Password</label>
        <input type="password" name="password" required>
        
        <button type="submit" class="btn" style="width:100%;margin-top:1rem;">Masuk</button>
    </form>
    
    <p style="margin-top:1rem;font-size:0.9rem;color:#666;">
        💡 <strong>Info Login:</strong><br>
        • Super Admin & Admin → Otomatis ke Panel Admin<br>
        • User → Otomatis ke Dashboard User
    </p>
</div>

<?php require '../includes/footer.php'; ?>