<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'keuangan')) { header('Location: ../../user/dashboard.php'); exit; }

$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $txid=(int)($_POST['tx_id']??0); $act=$_POST['action']??'';
    if ($txid>0 && in_array($act,['success','failed'])) {
        $pdo->prepare("UPDATE transactions SET status=? WHERE id=?")->execute([$act,$txid]);
        
        // Jika topup berhasil, update saldo user
        if ($act==='success') {
            $tx = $pdo->prepare("SELECT user_id, amount, type FROM transactions WHERE id=?");
            $tx->execute([$txid]);
            $data = $tx->fetch();
            if ($data && $data['type']==='topup') {
                $pdo->prepare("INSERT INTO user_balances (user_id, balance) VALUES (?,?) ON DUPLICATE KEY UPDATE balance=balance+?")
                    ->execute([$data['user_id'], $data['amount'], $data['amount']]);
            }
        }
        $msg = $act==='success' ? '✅ Transaksi diverifikasi.' : '❌ Transaksi ditolak.';
    }
}
$pendingTx = $pdo->query("SELECT t.id, t.qris_ref, t.type, t.category, t.amount, t.created_at, u.username FROM transactions t JOIN users u ON t.user_id=u.id WHERE t.status='pending' ORDER BY t.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verifikasi Transaksi - Keuangan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#0b5345;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1000px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#0b5345;margin-bottom:1rem;font-size:1.2rem}
table{width:100%;border-collapse:collapse}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600;color:#555}
.btn{padding:0.4rem 0.8rem;border:none;border-radius:4px;cursor:pointer;font-size:0.85rem;color:#fff}
.btn-ok{background:#28a745}.btn-no{background:#dc3545}
.alert{padding:0.8rem;border-radius:6px;margin-bottom:1rem;font-size:0.95rem}
.alert-ok{background:#d1e7dd;color:#0f5132}
</style></head>
<body>
<header class="header"><h1>✅ Verifikasi Transaksi</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">
<?php if($msg) echo "<div class='alert alert-ok'>$msg</div>"; ?>
<div class="card"><h2> Transaksi Menunggu Verifikasi</h2>
<table><thead><tr><th>Ref</th><th>User</th><th>Jenis</th><th>Nominal</th><th>Waktu</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($pendingTx as $t): ?>
<tr>
<td><code><?=htmlspecialchars($t['qris_ref'])?></code></td>
<td><?=htmlspecialchars($t['username'])?></td>
<td><?=strtoupper($t['type'])?> <?= $t['category'] ? '('.ucfirst($t['category']).')' : '' ?></td>
<td style="font-weight:600">Rp <?=number_format($t['amount'],0,',','.')?></td>
<td><?=date('d/m/Y H:i',strtotime($t['created_at']))?></td>
<td>
<form method="POST" style="display:inline"><input type="hidden" name="tx_id" value="<?=$t['id']?>"><input type="hidden" name="action" value="success"><button type="submit" class="btn btn-ok">✓ Terima</button></form>
<form method="POST" style="display:inline"><input type="hidden" name="tx_id" value="<?=$t['id']?>"><input type="hidden" name="action" value="failed"><button type="submit" class="btn btn-no">✗ Tolak</button></form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($pendingTx)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Tidak ada transaksi pending</td></tr>"; ?>
</tbody></table></div>
</div></body></html>