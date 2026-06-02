<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'penghimpunan')) { header('Location: ../../user/dashboard.php'); exit; }

$userId = $_SESSION['user_id'];
$msg = '';

// HANDLE CRUD DONATUR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $alamat = $_POST['alamat'];
        $total_donasi = (float)($_POST['total_donasi'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        
        if ($action === 'update' && $id > 0) {
            $pdo->prepare("UPDATE users SET username=?, email=?, telepon=?, alamat=? WHERE id=? AND role='user'")
                ->execute([$nama, $email, $telepon, $alamat, $id]);
            $msg = "✅ Data donatur berhasil diupdate!";
        } else {
            $password = password_hash('123456', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (username, password_hash, role, division, is_active) VALUES (?, ?, 'user', 'penghimpunan', 1)")
                ->execute([$nama, $password]);
            $msg = "✅ Donatur berhasil ditambahkan!";
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM users WHERE id=? AND role='user'")->execute([$id]);
        $msg = "🗑️ Donatur berhasil dihapus!";
    }
}

// AMBIL DATA DONATUR
try {
    $donaturStmt = $pdo->query("SELECT u.*, 
        COALESCE(SUM(t.amount), 0) as total_donasi,
        COUNT(t.id) as jumlah_transaksi
        FROM users u
        LEFT JOIN transactions t ON u.id = t.user_id AND t.status='success'
        WHERE u.role='user'
        GROUP BY u.id
        ORDER BY total_donasi DESC");
    $donaturData = $donaturStmt->fetchAll();
    
    $totalDonatur = count($donaturData);
    $totalDonasiSemua = array_sum(array_column($donaturData, 'total_donasi'));
} catch(PDOException $e) { $donaturData = []; $totalDonatur = 0; $totalDonasiSemua = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Data Donatur - Penghimpunan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#28a745;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1200px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#28a745;margin-bottom:1rem;font-size:1.2rem}
.summary{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem}
.summary-item{background:#e8f5e9;padding:1rem;border-radius:8px;text-align:center}
.summary-item h3{font-size:0.9rem;color:#0b5345}
.summary-item .value{font-size:1.8rem;font-weight:bold;color:#28a745}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem}
.btn-primary{background:#28a745;color:#fff}
.btn-success{background:#20c997;color:#fff}
.btn-danger{background:#dc3545;color:#fff}
.btn-warning{background:#ffc107;color:#000}
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-weight:600;margin-bottom:0.4rem}
.form-group input,.form-group textarea{width:100%;padding:0.6rem;border:1px solid #ddd;border-radius:6px}
table{width:100%;border-collapse:collapse}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600}
.alert{padding:1rem;border-radius:6px;margin-bottom:1rem}
.alert-success{background:#d1e7dd;color:#0f5132}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000}
.modal-content{background:#fff;padding:2rem;border-radius:12px;max-width:600px;margin:2rem auto}
</style></head>
<body>
<header class="header"><h1>👥 Data Donatur</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary">
<div class="summary-item"><h3>Total Donatur</h3><div class="value"><?=$totalDonatur?></div></div>
<div class="summary-item"><h3>Total Donasi</h3><div class="value">Rp <?=number_format($totalDonasiSemua,0,',','.')?></div></div>
</div>

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h2>Daftar Donatur</h2>
<button class="btn btn-primary" onclick="openModal()">+ Tambah Donatur</button>
</div>

<table>
<thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Jml Transaksi</th><th>Total Donasi</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($donaturData as $d): ?>
<tr>
<td><strong><?=htmlspecialchars($d['username'])?></strong></td>
<td><?=htmlspecialchars($d['email'] ?? '-')?></td>
<td><?=htmlspecialchars($d['telepon'] ?? '-')?></td>
<td><?=$d['jumlah_transaksi']?></td>
<td style="color:#28a745;font-weight:600">Rp <?=number_format($d['total_donasi'],0,',','.')?></td>
<td>
<button class="btn btn-warning" onclick='editDonatur(<?=json_encode($d)?>)'>✏️ Edit</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus donatur ini?')">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$d['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($donaturData)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Belum ada donatur</td></tr>"; ?>
</tbody>
</table>
</div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
<div class="modal-content">
<h2 id="modalTitle">Tambah Donatur</h2>
<form method="POST">
<input type="hidden" name="action" id="action" value="create">
<input type="hidden" name="id" id="id" value="">

<div class="form-group">
<label>Nama Lengkap</label>
<input type="text" name="nama" id="nama" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" id="email">
</div>

<div class="form-group">
<label>Telepon</label>
<input type="text" name="telepon" id="telepon">
</div>

<div class="form-group">
<label>Alamat</label>
<textarea name="alamat" id="alamat" rows="2"></textarea>
</div>

<div style="display:flex;gap:1rem">
<button type="submit" class="btn btn-success" style="flex:1">💾 Simpan</button>
<button type="button" class="btn" style="flex:1;background:#6c757d;color:#fff" onclick="closeModal()">Batal</button>
</div>
</form>
</div>
</div>

<script>
function openModal(){ 
    document.getElementById('modal').style.display='block';
    document.getElementById('action').value='create';
    document.getElementById('id').value='';
    document.getElementById('modalTitle').innerText='Tambah Donatur';
}
function closeModal(){ document.getElementById('modal').style.display='none'; }
function editDonatur(data) {
    document.getElementById('action').value='update';
    document.getElementById('id').value=data.id;
    document.getElementById('nama').value=data.username;
    document.getElementById('email').value=data.email||'';
    document.getElementById('telepon').value=data.telepon||'';
    document.getElementById('alamat').value=data.alamat||'';
    document.getElementById('modalTitle').innerText='Edit Donatur';
    openModal();
}
</script>
</body>
</html>