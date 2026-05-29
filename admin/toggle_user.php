<?php
session_start(); require '../config/db.php'; require '../includes/auth.php';
requireRole(['super_admin', 'admin']);
if ($_SERVER['REQUEST_METHOD']!=='POST' || !verifyCsrf($_POST['csrf'])) die('Invalid');
$uid = (int)$_POST['uid'];
$role = $_SESSION['role'];
$can = ($role==='super_admin') || ($role==='admin' && $pdo->query("SELECT is_active FROM users WHERE id=$uid")->fetchColumn() == 0);
if (!$can || $uid==$_SESSION['user_id']) { header('Location: users.php'); exit; }
$pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?")->execute([$uid]);
header('Location: users.php');
?>