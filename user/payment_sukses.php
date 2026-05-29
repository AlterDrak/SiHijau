<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$ref = htmlspecialchars($_GET['ref'] ?? 'TX-' . time());
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - Lazpersis</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
        body{background:#f4f7f6;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:1rem}
        .success-box{background:#fff;padding:2.5rem;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.08);text-align:center;max-width:400px;width:100%}
        .icon{font-size:4rem;margin-bottom:1rem}
        h2{color:#28a745;margin-bottom:0.5rem}
        p{color:#666;margin-bottom:1.5rem;line-height:1.6}
        .ref{background:#f8f9fa;padding:0.8rem;border-radius:6px;font-family:monospace;font-size:1.1rem;color:#0b5345;margin-bottom:1.5rem}
        .btn{display:inline-block;padding:0.8rem 2rem;background:#0b5345;color:#fff;text-decoration:none;border-radius:8px;font-weight:600}
        .btn:hover{background:#083d32}
    </style>
</head>
<body>
    <div class="success-box">
        <div class="icon">✅</div>
        <h2>Pembayaran Berhasil!</h2>
        <p>Terima kasih telah menunaikan kewajiban & kebaikan Anda. Dana akan segera diproses oleh Lazpersis Rajapolah.</p>
        <div class="ref">Ref: <?= $ref ?></div>
        <a href="dashboard.php" class="btn">Kembali ke Dashboard</a>
    </div>
</body>
</html>