<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'pendayagunaan')) { header('Location: ../../user/dashboard.php'); exit; }

$msg = '';

// HANDLE CRUD PENYALURAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $bulan = $_POST['bulan'];
        $nama_mustahik = $_POST['nama_mustahik'];
        $jenis_bantuan = $_POST['jenis_bantuan'];
        $jumlah = (float)($_POST['jumlah'] ?? 0);
        $keterangan = $_POST['keterangan'];
        $id = (int)($_POST['id'] ?? 0);
        
        if ($action === 'update' && $id > 0) {
            $pdo->prepare("UPDATE distributions SET bulan=?, mustahik_name=?, jenis_bantuan=?, amount=?, keterangan=? WHERE id=?")
                ->execute([$bulan, $nama_mustahik, $jenis_bantuan, $jumlah, $keterangan, $id]);
            $msg = "✅ Data penyaluran berhasil diupdate!";
        } else {
            $pdo->prepare("INSERT INTO distributions (bulan, mustahik_name, jenis_bantuan, amount, keterangan) VALUES (?, ?, ?, ?, ?)")
                ->execute([$bulan, $nama_mustahik, $jenis_bantuan, $jumlah, $keterangan]);
            $msg = "✅ Penyaluran berhasil dicatat!";
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM distributions WHERE id=?")->execute([$id]);
        $msg = "🗑️ Data penyaluran berhasil dihapus!";
    }
}

// AMBIL DATA
try {
    $distStmt = $pdo->query("SELECT * FROM distributions ORDER BY bulan DESC, created_at DESC");
    $distData = $distStmt->fetchAll();
    
    $totalPenyaluran = array_sum(array_column($distData, 'amount'));
    $totalMustahik = count(array_unique(array_column($distData, 'mustahik_name')));
} catch(PDOException $e) { $distData = []; $totalPenyaluran = 0; $totalMustahik = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Data Penyaluran - Pendayagunaan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#fd7e14;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1200px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#fd7e14;margin-bottom:1rem;font-size:1.2rem}
.summary{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem}
.summary-item{background:#fff3cd;padding:1rem;border-radius:8px;text-align:center;border-left:4px solid #fd7e14}
.summary-item h3{font-size:0.9rem;color:#856404}
.summary-item .value{font-size:1.8rem;font-weight:bold;color:#fd7e14}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem}
.btn-primary{background:#fd7e14;color:#fff}
.btn-success{background:#28a745;color:#fff}
.btn-danger{background:#dc3545;color:#fff}
.btn-warning{background:#ffc107;color:#000}
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-weight:600;margin-bottom:0.4rem}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:0.6rem;border:1px solid #ddd;border-radius:6px}
table{width:100%;border-collapse:collapse}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600}
.alert{padding:1rem;border-radius:6px;margin-bottom:1rem}
.alert-success{background:#d1e7dd;color:#0f5132}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000}
.modal-content{background:#fff;padding:2rem;border-radius:12px;max-width:600px;margin:2rem auto}
</style></head>
<body>
<header class="header"><h1>📋 Data Penyaluran</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary">
<div class="summary-item"><h3>Total Mustahik</h3><div class="value"><?=$totalMustahik?></div></div>
<div class="summary-item"><h3>Total Disalurkan</h3><div class="value">Rp <?=number_format($totalPenyaluran,0,',','.')?></div></div>
</div>

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h2>Riwayat Penyaluran</h2>
<button class="btn btn-primary" onclick="openModal()">+ Catat Penyaluran</button>
</div>

<table>
<thead><tr><th>Bulan</th><th>Nama Mustahik</th><th>Jenis Bantuan</th><th>Jumlah</th><th>Keterangan</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($distData as $d): 
    $bulanName = date('F Y', strtotime($d['bulan'].'-01'));
?>
<tr>
<td><?=ucfirst($bulanName)?></td>
<td><?=htmlspecialchars($d['mustahik_name'])?></td>
<td><span style="background:#e9ecef;padding:0.3rem 0.6rem;border-radius:12px;font-size:0.85rem"><?=htmlspecialchars($d['jenis_bantuan'])?></span></td>
<td style="color:#fd7e14;font-weight:600">Rp <?=number_format($d['amount'],0,',','.')?></td>
<td><?=htmlspecialchars($d['keterangan'])?></td>
<td>
<button class="btn btn-warning" onclick='editDist(<?=json_encode($d)?>)'>✏️</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$d['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($distData)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Belum ada data</td></tr>"; ?>
</tbody>
</table>
</div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
<div class="modal-content">
<h2 id="modalTitle">Catat Penyaluran</h2>
<form method="POST">
<input type="hidden" name="action" id="action" value="create">
<input type="hidden" name="id" id="id" value="">

<div class="form-group">
<label>Bulan</label>
<input type="month" name="bulan" id="bulan" value="<?=date('Y-m')?>" required>
</div>

<div class="form-group">
<label>Nama Mustahik</label>
<input type="text" name="nama_mustahik" id="nama_mustahik" required>
</div>

<div class="form-group">
<label>Jenis Bantuan</label>
<select name="jenis_bantuan" id="jenis_bantuan" required>
<option value="Uang Tunai">Uang Tunai</option>
<option value="Sembako">Sembako</option>
<option value="Pendidikan">Pendidikan</option>
<option value="Kesehatan">Kesehatan</option>
<option value="Lainnya">Lainnya</option>
</select>
</div>

<div class="form-group">
<label>Jumlah (Rp)</label>
<input type="number" name="jumlah" id="jumlah" min="0" required>
</div>

<div class="form-group">
<label>Keterangan</label>
<textarea name="keterangan" id="keterangan" rows="2"></textarea>
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
    document.getElementById('modalTitle').innerText='Catat Penyaluran';
}
function closeModal(){ document.getElementById('modal').style.display='none'; }
function editDist(data) {
    document.getElementById('action').value='update';
    document.getElementById('id').value=data.id;
    document.getElementById('bulan').value=data.bulan;
    document.getElementById('nama_mustahik').value=data.mustahik_name;
    document.getElementById('jenis_bantuan').value=data.jenis_bantuan;
    document.getElementById('jumlah').value=data.amount;
    document.getElementById('keterangan').value=data.keterangan||'';
    document.getElementById('modalTitle').innerText='Edit Penyaluran';
    openModal();
}
</script>
</body>
</html>