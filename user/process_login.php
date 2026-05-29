<?php
session_start(); 
require '../config/db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=1'); 
    exit;
}

$stmt = $pdo->prepare("SELECT id, password_hash, role, is_active FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Cek apakah user ada
if (!$user) {
    header('Location: login.php?error=1'); 
    exit;
}

// Cek apakah akun aktif
if ($user['is_active'] == 0) {
    header('Location: login.php?error=nonaktif'); 
    exit;
}

// Verifikasi password
if (password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['role']      = $user['role'];
    
    // Redirect berdasarkan role
    if (in_array($user['role'], ['super_admin', 'admin'])) {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
} else {
    header('Location: login.php?error=1');
    exit;
}
?>