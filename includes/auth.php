<?php
function requireLogin() {
    if (!isset($_SESSION['user_id'])) { header('Location: ../user/login.php'); exit; }
}
function requireRole($allowed) {
    requireLogin();
    if (!in_array($_SESSION['role'] ?? '', $allowed)) {
        header('Location: ../public/index.php'); exit;
    }
}
function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verifyCsrf($t) { return hash_equals($_SESSION['csrf'] ?? '', $t ?? ''); }
?>