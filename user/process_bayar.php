<?php
session_start(); require '../config/db.php'; require '../includes/auth.php';
if (!verifyCsrf($_POST['csrf'] ?? '')) die('CSRF Invalid');
$type = $_POST['type']; $zt = $type=='zakat' ? $_POST['zakat_type'] : null; $amt = (float)$_POST['amount'];
if ($amt < 1000) die('Nominal minimal Rp 1.000');
$ref = 'QRIS-' . strtoupper(bin2hex(random_bytes(4)));
$stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, zakat_type, amount, reference_code) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $type, $zt, $amt, $ref]);
$_SESSION['last_tx'] = $pdo->lastInsertId();
header('Location: riwayat.php?success=1');
?>