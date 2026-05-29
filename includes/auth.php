<?php
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../user/login.php');
        exit;
    }
}

function requireDivision($targetDivision) {
    requireLogin();
    $role = $_SESSION['role'] ?? '';
    $division = $_SESSION['division'] ?? '';
    
    // Super admin bebas akses semua divisi
    if ($role === 'super_admin') return true;
    
    // Staff hanya boleh masuk ke divisinya sendiri
    if ($role === 'staff' && $division === $targetDivision) return true;
    
    header('Location: ../user/login.php');
    exit;
}

function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function verifyCsrf($token) {
    return hash_equals($_SESSION['csrf'] ?? '', $token ?? '');
}
?>