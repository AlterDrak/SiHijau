<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'penghimpunan')) { header('Location: ../../user/dashboard.php'); exit; }

$userId = $_SESSION['user_id'];
$msg = '';

// HANDLE CRUD - KINERJA STAFF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type'])) {
    $formType = $_POST['form_type'];
    
    if ($formType === 'kinerja_create' || $formType === 'kinerja_update') {
        $staff_id = (int)$_POST['staff_id'];
        $bulan = $_POST['bulan'];
        $jumlah_transaksi = (int)$_POST['jumlah_transaksi'];
        $total_dana = (float)$_POST['total_dana'];
        $catatan = $_POST['catatan'];
        $id = (int)($_POST['id'] ?? 0);
        
        if ($formType === 'kinerja_create') {
            $pdo->prepare("INSERT INTO kinerja_staff (staff_id, bulan, jumlah_transaksi, total_dana, catatan) VALUES (?, ?, ?, ?, ?)")
                ->execute([$staff_id, $bulan, $jumlah_transaksi, $total_dana, $catatan]);
            $msg = "✅ Laporan kinerja berhasil ditambahkan!";
        } else {
            $pdo->prepare("UPDATE kinerja_staff SET bulan=?, jumlah_transaksi=?, total_dana=?, catatan=? WHERE id=?")
                ->execute([$bulan, $jumlah_transaksi, $total_dana, $catatan, $id]);
            $msg = "✅ Laporan kinerja berhasil diupdate!";
        }
    }
    
    if ($formType === 'kinerja_delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM kinerja_staff WHERE id=?")->execute([$id]);
        $msg = "🗑️ Laporan kinerja berhasil dihapus!";
    }
    
    // CRUD LAPORAN BSD
    if ($formType === 'bsd_create' || $formType === 'bsd_update') {
        $bulan = $_POST['bulan'];
        $jenis = $_POST['jenis'];
        $jumlah_transaksi = (int)$_POST['jumlah_transaksi'];
        $total_dana = (float)$_POST['total_dana'];
        $id = (int)($_POST['id'] ?? 0);
        
        if ($formType === 'bsd_create') {
            $pdo->prepare("INSERT INTO laporan_bsd (bulan, jenis, jumlah_transaksi, total_dana) VALUES (?, ?, ?, ?)")
                ->execute([$bulan, $jenis, $jumlah_transaksi, $total_dana]);
            $msg = "✅ Laporan BSD berhasil ditambahkan!";
        } else {
            $pdo->prepare("UPDATE laporan_bsd SET bulan=?, jenis=?, jumlah_transaksi=?, total_dana=? WHERE id=?")
                ->execute([$bulan, $jenis, $jumlah_transaksi, $total_dana, $id]);
            $msg = "✅ Laporan BSD berhasil diupdate!";
        }
    }
    
    if ($formType === 'bsd_delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM laporan_bsd WHERE id=?")->execute([$id]);
        $msg = "🗑️ Laporan BSD berhasil dihapus!";
    }
}

// AMBIL DATA
try {
    // Staff penghimpunan
    $staffList = $pdo->query("SELECT id, username FROM users WHERE role='staff' AND division='penghimpunan' AND is_active=1")->fetchAll();
    
    // Kinerja staff
    $kinerjaStmt = $pdo->query("SELECT k.*, u.username FROM kinerja_staff k JOIN users u ON k.staff_id=u.id ORDER BY k.bulan DESC");
    $kinerjaData = $kinerjaStmt->fetchAll();
    
    // Laporan BSD
    $bsdStmt = $pdo->query("SELECT * FROM laporan_bsd ORDER BY bulan DESC");
    $bsdData = $bsdStmt->fetchAll();
    
    // Total
    $totalKinerja = array_sum(array_column($kinerjaData, 'total_dana'));
    $totalBSD = array_sum(array_column($bsdData, 'total_dana'));
} catch(PDOException $e) { 
    $staffList = []; $kinerjaData = []; $bsdData = []; $totalKinerja = $totalBSD = 0; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Monitoring Penghimpunan - CRUD</title>
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
.summary-item{background:#f8f9fa;padding:1rem;border-radius:8px;text-align:center}
.summary-item h3{font-size:0.9rem;color:#666}
.summary-item .value{font-size:1.5rem;font-weight:bold;color:#28a745}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem;margin:0.2rem}
.btn-primary{background:#28a745;color:#fff}
.btn-success{background:#20c997;color:#fff}
.btn-danger{background:#dc3545;color:#fff}
.btn-warning{background:#ffc107;color:#000}
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-weight:600;margin-bottom:0.4rem}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:0.6rem;border:1px solid #ddd;border-radius:6px}
table{width:100%;border-collapse:collapse;margin-top:1rem}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600}
.alert{padding:1rem;border-radius:6px;margin-bottom:1rem}
.alert-success{background:#d1e7dd;color:#0f5132}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;overflow-y:auto}
.modal-content{background:#fff;padding:2rem;border-radius:12px;max-width:600px;margin:2rem auto}
.tab-buttons{display:flex;gap:0.5rem;margin-bottom:1rem}
.tab-btn{padding:0.6rem 1.2rem;border:none;background:#e9ecef;cursor:pointer;border-radius:6px 6px 0 0}
.tab-btn.active{background:#28a745;color:#fff}
.tab-content{display:none}
.tab-content.active{display:block}
</style></head>
<body>
<header class="header"><h1>📊 Monitoring Penghimpunan</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary">
<div class="summary-item"><h3>Total Kinerja Staff</h3><div class="value">Rp <?=number_format($totalKinerja,0,',','.')?></div></div>
<div class="summary-item"><h3>Total Laporan BSD</h3><div class="value">Rp <?=number_format($totalBSD,0,',','.')?></div></div>
</div>

<div class="card">
<div class="tab-buttons">
<button class="tab-btn active" onclick="switchTab('kinerja')">👥 Kinerja Staff</button>
<button class="tab-btn" onclick="switchTab('bsd')">💰 Laporan BSD</button>
</div>

<!-- TAB KINERJA STAFF -->
<div id="tab-kinerja" class="tab-content active">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h3>Laporan Kinerja Perorangan</h3>
<button class="btn btn-primary" onclick="openModal('kinerja')">+ Tambah Laporan</button>
</div>
<table>
<thead><tr><th>Staff</th><th>Bulan</th><th>Jml Transaksi</th><th>Total Dana</th><th>Catatan</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($kinerjaData as $k): 
    $bulanName = date('F Y', strtotime($k['bulan'].'-01'));
?>
<tr>
<td><?=htmlspecialchars($k['username'])?></td>
<td><?=ucfirst($bulanName)?></td>
<td><?=$k['jumlah_transaksi']?></td>
<td style="color:#28a745;font-weight:600">Rp <?=number_format($k['total_dana'],0,',','.')?></td>
<td><?=htmlspecialchars($k['catatan'])?></td>
<td>
<button class="btn btn-warning" onclick='editKinerja(<?=json_encode($k)?>)'>✏️</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
<input type="hidden" name="form_type" value="kinerja_delete"><input type="hidden" name="id" value="<?=$k['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($kinerjaData)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Belum ada data</td></tr>"; ?>
</tbody>
</table>
</div>

<!-- TAB BSD -->
<div id="tab-bsd" class="tab-content">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h3>Laporan BSD (Infaq & Shodaqoh)</h3>
<button class="btn btn-primary" onclick="openModal('bsd')">+ Tambah Laporan</button>
</div>
<table>
<thead><tr><th>Bulan</th><th>Jenis</th><th>Jml Transaksi</th><th>Total Dana</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($bsdData as $b): 
    $bulanName = date('F Y', strtotime($b['bulan'].'-01'));
    $jenisLabel = strtoupper($b['jenis']);
?>
<tr>
<td><?=ucfirst($bulanName)?></td>
<td><span style="background:#e9ecef;padding:0.3rem 0.6rem;border-radius:12px;font-size:0.85rem"><?=$jenisLabel?></span></td>
<td><?=$b['jumlah_transaksi']?></td>
<td style="color:#28a745;font-weight:600">Rp <?=number_format($b['total_dana'],0,',','.')?></td>
<td>
<button class="btn btn-warning" onclick='editBSD(<?=json_encode($b)?>)'>✏️</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
<input type="hidden" name="form_type" value="bsd_delete"><input type="hidden" name="id" value="<?=$b['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($bsdData)) echo "<tr><td colspan='5' style='text-align:center;color:#888'>Belum ada data</td></tr>"; ?>
</tbody>
</table>
</div>
</div>
</div>

<!-- MODAL KINERJA -->
<div id="modal-kinerja" class="modal">
<div class="modal-content">
<h2>Laporan Kinerja Staff</h2>
<form method="POST">
<input type="hidden" name="form_type" id="kinerja-action" value="kinerja_create">
<input type="hidden" name="id" id="kinerja-id" value="">

<div class="form-group">
<label>Staff</label>
<select name="staff_id" id="kinerja-staff" required>
<option value="">-- Pilih Staff --</option>
<?php foreach($staffList as $s): ?>
<option value="<?=$s['id']?>"><?=htmlspecialchars($s['username'])?></option>
<?php endforeach; ?>
</select>
</div>

<div class="form-group">
<label>Bulan</label>
<input type="month" name="bulan" id="kinerja-bulan" value="<?=date('Y-m')?>" required>
</div>

<div class="form-group">
<label>Jumlah Transaksi</label>
<input type="number" name="jumlah_transaksi" id="kinerja-jumlah" min="0" required>
</div>

<div class="form-group">
<label>Total Dana (Rp)</label>
<input type="number" name="total_dana" id="kinerja-total" min="0" step="0.01" required>
</div>

<div class="form-group">
<label>Catatan</label>
<textarea name="catatan" id="kinerja-catatan" rows="2"></textarea>
</div>

<div style="display:flex;gap:1rem">
<button type="submit" class="btn btn-success" style="flex:1">💾 Simpan</button>
<button type="button" class="btn" style="flex:1;background:#6c757d;color:#fff" onclick="closeModal('kinerja')">Batal</button>
</div>
</form>
</div>
</div>

<!-- MODAL BSD -->
<div id="modal-bsd" class="modal">
<div class="modal-content">
<h2>Laporan BSD</h2>
<form method="POST">
<input type="hidden" name="form_type" id="bsd-action" value="bsd_create">
<input type="hidden" name="id" id="bsd-id" value="">

<div class="form-group">
<label>Bulan</label>
<input type="month" name="bulan" id="bsd-bulan" value="<?=date('Y-m')?>" required>
</div>

<div class="form-group">
<label>Jenis</label>
<select name="jenis" id="bsd-jenis" required>
<option value="infaq">Infaq</option>
<option value="shodaqoh">Shodaqoh</option>
<option value="zakat">Zakat</option>
</select>
</div>

<div class="form-group">
<label>Jumlah Transaksi</label>
<input type="number" name="jumlah_transaksi" id="bsd-jumlah" min="0" required>
</div>

<div class="form-group">
<label>Total Dana (Rp)</label>
<input type="number" name="total_dana" id="bsd-total" min="0" step="0.01" required>
</div>

<div style="display:flex;gap:1rem">
<button type="submit" class="btn btn-success" style="flex:1">💾 Simpan</button>
<button type="button" class="btn" style="flex:1;background:#6c757d;color:#fff" onclick="closeModal('bsd')">Batal</button>
</div>
</form>
</div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('tab-'+tab).classList.add('active');
}
function openModal(type) { 
    document.getElementById('modal-'+type).style.display='block';
    if(type==='kinerja') {
        document.getElementById('kinerja-action').value='kinerja_create';
        document.getElementById('kinerja-id').value='';
    } else {
        document.getElementById('bsd-action').value='bsd_create';
        document.getElementById('bsd-id').value='';
    }
}
function closeModal(type) { document.getElementById('modal-'+type).style.display='none'; }
function editKinerja(data) {
    document.getElementById('kinerja-action').value='kinerja_update';
    document.getElementById('kinerja-id').value=data.id;
    document.getElementById('kinerja-staff').value=data.staff_id;
    document.getElementById('kinerja-bulan').value=data.bulan;
    document.getElementById('kinerja-jumlah').value=data.jumlah_transaksi;
    document.getElementById('kinerja-total').value=data.total_dana;
    document.getElementById('kinerja-catatan').value=data.catatan||'';
    openModal('kinerja');
}
function editBSD(data) {
    document.getElementById('bsd-action').value='bsd_update';
    document.getElementById('bsd-id').value=data.id;
    document.getElementById('bsd-bulan').value=data.bulan;
    document.getElementById('bsd-jenis').value=data.jenis;
    document.getElementById('bsd-jumlah').value=data.jumlah_transaksi;
    document.getElementById('bsd-total').value=data.total_dana;
    openModal('bsd');
}
</script>
</body>
</html>