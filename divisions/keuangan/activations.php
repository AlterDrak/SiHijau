<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'keuangan')) { header('Location: ../../user/dashboard.php'); exit; }

$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $uid=(int)($_POST['user_id']??0); $act=$_POST['action']??'';
    if ($uid>0 && in_array($act,['activate','deactivate'])) {
        $newStatus = $act==='activate' ? 1 : 0;
        $pdo->prepare("UPDATE users SET is_active=? WHERE id=? AND role='user'")->execute([$newStatus,$uid]);
        $msg = $act==='activate' ? '✅ User berhasil diaktifkan.' : '⚠️ User dinonaktifkan.';
    }
}
$pending = $pdo->query("SELECT id,username,created_at FROM users WHERE role='user' AND is_active=0 ORDER BY created_at DESC")->fetchAll();
$active = $pdo->query("SELECT id,username,created_at FROM users WHERE role='user' AND is_active=1 ORDER BY created_at DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Aktivasi User - Keuangan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#0b5345;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:900px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#0b5345;margin-bottom:1rem;font-size:1.2rem}
table{width:100%;border-collapse:collapse}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600;color:#555}
.btn{padding:0.4rem 0.8rem;border:none;border-radius:4px;cursor:pointer;font-size:0.85rem;color:#fff}
.btn-ok{background:#28a745}.btn-no{background:#dc3545}
.alert{padding:0.8rem;border-radius:6px;margin-bottom:1rem;font-size:0.95rem}
.alert-ok{background:#d1e7dd;color:#0f5132}.alert-no{background:#f8d7da;color:#842029}
</style></head>
<body>
<header class="header"><h1>👥 Aktivasi User (Keuangan)</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">
<?php if($msg) echo "<div class='alert alert-ok'>$msg</div>"; ?>
<div class="card"><h2> Menunggu Aktivasi</h2>
<table><thead><tr><th>Username</th><th>Terdaftar</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($pending as $u): ?>
<tr><td><?=htmlspecialchars($u['username'])?></td><td><?=date('d/m/Y H:i',strtotime($u['created_at']))?></td>
<td><form method="POST" style="display:inline"><input type="hidden" name="user_id" value="<?=$u['id']?>"><input type="hidden" name="action" value="activate"><button type="submit" class="btn btn-ok">✓ Aktifkan</button></form></td></tr>
<?php endforeach; ?>
<?php if(empty($pending)) echo "<tr><td colspan='3' style='text-align:center;color:#888'>Tidak ada user pending</td></tr>"; ?>
</tbody></table></div>

<div class="card"><h2>✅ User Aktif (20 Terakhir)</h2>
<table><thead><tr><th>Username</th><th>Terdaftar</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($active as $u): ?>
<tr><td><?=htmlspecialchars($u['username'])?></td><td><?=date('d/m/Y H:i',strtotime($u['created_at']))?></td>
<td><form method="POST" style="display:inline"><input type="hidden" name="user_id" value="<?=$u['id']?>"><input type="hidden" name="action" value="deactivate"><button type="submit" class="btn btn-no">✗ Nonaktifkan</button></form></td></tr>
<?php endforeach; ?>
</tbody></table></div>
</div></body></html>