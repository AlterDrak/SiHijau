<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') { header('Location: login.php'); exit; }

$userId = $_SESSION['user_id'];
$type   = $_POST['type'] ?? '';
$cat    = $_POST['category'] ?? null;
$amount = (float)($_POST['amount'] ?? 0);
$action = $_POST['action'] ?? 'create';

// Validasi dasar
if ($amount < 1000 || !in_array($type, ['topup','zakat','infaq','shodaqoh'])) {
    die('<div style="text-align:center;padding:3rem;font-family:sans-serif;">❌ Nominal atau tipe pembayaran tidak valid.<br><a href="dashboard.php">Kembali</a></div>');
}

// STEP 1: Buat Transaksi Pending & Tampilkan QRIS
if ($action === 'create') {
    $ref = 'QRIS-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time();
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, category, amount, status, qris_ref) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$userId, $type, $cat, $amount, $ref]);
    $txId = $pdo->lastInsertId();
    
    // QRIS Mock (gunakan API publik untuk generate gambar QR)
    $qrData = "LAZPERSIS|{$ref}|{$amount}|{$type}";
    $qrUrl  = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($qrData);
    
    $typeLabel = match($type) {
        'topup' => 'Top Up Saldo',
        'zakat' => 'Pembayaran Zakat',
        'infaq' => 'Pembayaran Infaq',
        'shodaqoh' => 'Pembayaran Shodaqoh'
    };
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pembayaran QRIS - Lazpersis</title>
        <style>
            *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
            body{background:#f4f7f6;color:#333;display:flex;justify-content:center;align-items:center;min-height:100vh;padding:1rem}
            .qris-box{background:#fff;padding:2rem;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.08);width:100%;max-width:380px;text-align:center}
            .qris-box h2{color:#0b5345;margin-bottom:0.5rem}
            .qris-box .type{color:#666;font-size:0.95rem;margin-bottom:1.5rem}
            .qr-img{margin:1rem 0;border:1px solid #eee;padding:0.5rem;border-radius:8px}
            .details{background:#f8f9fa;padding:1rem;border-radius:8px;margin:1rem 0;text-align:left;font-size:0.95rem}
            .details div{display:flex;justify-content:space-between;margin-bottom:0.4rem}
            .details strong{color:#0b5345}
            .btn-confirm{width:100%;padding:0.9rem;background:#28a745;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;margin-top:1rem}
            .btn-confirm:hover{background:#218838}
            .cancel-btn{display:block;margin-top:0.8rem;color:#dc3545;text-decoration:none;font-size:0.9rem}
            .timer{color:#666;font-size:0.85rem;margin-top:0.5rem}
        </style>
    </head>
    <body>
        <div class="qris-box">
            <h2>📱 Pembayaran QRIS</h2>
            <p class="type"><?= htmlspecialchars($typeLabel) ?></p>
            <img src="<?= $qrUrl ?>" alt="QRIS Code" class="qr-img">
            <div class="details">
                <div><span>Kode Referensi</span><strong><?= htmlspecialchars($ref) ?></strong></div>
                <div><span>Nominal</span><strong>Rp <?= number_format($amount,0,',','.') ?></strong></div>
                <div><span>Status</span><strong style="color:#ffc107">Menunggu Pembayaran</strong></div>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="confirm">
                <input type="hidden" name="tx_id" value="<?= $txId ?>">
                <input type="hidden" name="amount" value="<?= $amount ?>">
                <input type="hidden" name="type" value="<?= $type ?>">
                <button type="submit" class="btn-confirm" onclick="return confirm('Sudah scan & bayar via e-wallet/bank?')">✅ Saya Sudah Membayar</button>
            </form>
            <a href="dashboard.php" class="cancel-btn">← Batalkan & Kembali</a>
            <p class="timer">⏳ QRIS berlaku selama 15 menit</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// STEP 2: Konfirmasi Pembayaran Berhasil (Simulasi)
if ($action === 'confirm') {
    $txId   = (int)($_POST['tx_id'] ?? 0);
    $type   = $_POST['type'];
    $amount = (float)($_POST['amount']);
    
    // Update transaksi jadi success
    $pdo->prepare("UPDATE transactions SET status='success' WHERE id=? AND user_id=?")->execute([$txId, $userId]);
    
    // Jika topup, tambahkan ke saldo
    if ($type === 'topup') {
        $pdo->prepare("INSERT INTO user_balances (user_id, balance) VALUES (?, ?) ON DUPLICATE KEY UPDATE balance = balance + ?")
            ->execute([$userId, $amount, $amount]);
    }
    
    header('Location: payment_success.php?ref=' . urlencode($_POST['qris_ref'] ?? 'success'));
    exit;
}
?>