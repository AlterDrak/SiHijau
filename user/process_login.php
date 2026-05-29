<?php
session_start();
require '../config/db.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=1');
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, password_hash, role, is_active, division FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.php?error=1');
    exit;
}

// Cek status aktif khusus untuk role user
if ($user['role'] === 'user' && $user['is_active'] == 0) {
    header('Location: login.php?error=nonaktif');
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    header('Location: login.php?error=1');
    exit;
}

// Login berhasil: regenerate session & set variabel
session_regenerate_id(true);
$_SESSION['user_id']   = $user['id'];
$_SESSION['username']  = $user['username'];
$_SESSION['role']      = $user['role'];
$_SESSION['division']  = $user['division'];

// Routing berdasarkan role
switch ($user['role']) {
    case 'super_admin':
        header('Location: ../dashboard.php');
        break;
    case 'staff':
        $div = $user['division'] ?? 'keuangan';
        header("Location: ../divisions/{$div}/index.php");
        break;
    default:
        header('Location: dashboard.php');
        break;
}
exit;
?>