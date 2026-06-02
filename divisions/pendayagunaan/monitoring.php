<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'pendayagunaan')) { header('Location: ../../user/dashboard.php'); exit; }

$msg = '';

// HANDLE CRUD LAYANAN AMBULAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $bulan = $_POST['bulan'];
        $tanggal_layanan = $_POST['tanggal_layanan'];
        $nama_pasien = $_POST['nama_pasien'];
        $tujuan = $_POST['tujuan'];
        $jenis_layanan = $_POST['jenis_layanan'];
        $id = (int)($_POST['id'] ?? 0);
        
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO layanan_ambulan (bulan, tanggal_layanan, nama_pasien, tujuan, jenis_layanan) VALUES (?, ?, ?, ?, ?)")
                ->execute([$bulan, $tanggal_layanan, $nama_pasien, $tujuan, $jenis_layanan]);
            $msg = "✅ Data layanan ambulan berhasil ditambahkan!";
        } else {
            $pdo->prepare("UPDATE layanan_ambulan SET bulan=?, tanggal_layanan=?, nama_pasien=?, tujuan=?, jenis_layanan=? WHERE id=?")
                ->execute([$bulan, $tanggal_layanan, $nama_pasien, $tujuan, $jenis_layanan, $id]);
            $msg = "✅ Data layanan ambulan berhasil diupdate!";
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM layanan_ambulan WHERE id=?")->execute([$id]);
        $msg = "🗑️ Data layanan ambulan berhasil dihapus!";
    }
}

// AMBIL DATA
try {
    $ambulanStmt = $pdo->query("SELECT * FROM layanan_ambulan ORDER BY bulan DESC, tanggal_layanan DESC");
    $ambulanData = $ambulanStmt->fetchAll();
    
    // Statistik
    $totalLayanan = count($ambulanData);
    $bulanIni = date('Y-m');
    $stmtBulanIni = $pdo->prepare("SELECT COUNT(*) FROM layanan_ambulan WHERE bulan=?");
    $stmtBulanIni->execute([$bulanIni]);
    $layananBulanIni = $stmtBulanIni->fetchColumn();
} catch(PDOException $e) { $ambulanData = []; $totalLayanan = $layananBulanIni = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Layanan Ambulan - CRUD</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#fd7e14;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1100px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#fd7e14;margin-bottom:1rem;font-size:1.2rem}
.summary{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem}
.summary-item{background:#fff3cd;padding:1rem;border-radius:8px;text-align:center;border-left:4px solid #fd7e14}
.summary-item h3{font-size:0.9rem;color:#856404}
.summary-item .value{font-size:1.8rem;font-weight:bold;color:#fd7e14}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem;margin:0.2rem}
.btn-primary{background:#fd7e14;color:#fff}
.btn-success{background:#28a745;color:#fff}
.btn-danger{background:#dc3545;color:#fff}
.btn-warning{background:#ffc107;color:#000}
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-weight:600;margin-bottom:0.4rem}
.form-group input,.form-group select{width:100%;padding:0.6rem;border:1px solid #ddd;border-radius:6px}
table{width:100%;border-collapse:collapse}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600}
.alert{padding:1rem;border-radius:6px;margin-bottom:1rem}
.alert-success{background:#d1e7dd;color:#0f5132}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;overflow-y:auto}
.modal-content{background:#fff;padding:2rem;border-radius:12px;max-width:600px;margin:2rem auto}
</style></head>
<body>
<header class="header"><h1>🚑 Layanan Ambulan - Monitoring</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary">
<div class="summary-item"><h3>Total Layanan (Semua Waktu)</h3><div class="value"><?=$totalLayanan?></div></div>
<div class="summary-item"><h3>Layanan Bulan Ini</h3><div class="value"><?=$layananBulanIni?></div></div>
</div>

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h2>📋 Rekap Layanan Ambulan Bulanan</h2>
<button class="btn btn-primary" onclick="openModal()">+ Tambah Data Layanan</button>
</div>

<table>
<thead><tr><th>Bulan</th><th>Tanggal</th><th>Nama Pasien</th><th>Tujuan</th><th>Jenis Layanan</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($ambulanData as $a): 
    $bulanName = date('F Y', strtotime($a['bulan'].'-01'));
    $tglFormat = date('d/m/Y', strtotime($a['tanggal_layanan']));
?>
<tr>
<td><?=ucfirst($bulanName)?></td>
<td><?=$tglFormat?></td>
<td><?=htmlspecialchars($a['nama_pasien'])?></td>
<td><?=htmlspecialchars($a['tujuan'])?></td>
<td><span style="background:#e9ecef;padding:0.3rem 0.6rem;border-radius:12px;font-size:0.85rem"><?=htmlspecialchars($a['jenis_layanan'])?></span></td>
<td>
<button class="btn btn-warning" onclick='editAmbulan(<?=json_encode($a)?>)'>✏️</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$a['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($ambulanData)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Belum ada data layanan</td></tr>"; ?>
</tbody>
</table>
</div>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-content">
<h2>🚑 Data Layanan Ambulan</h2>
<form method="POST">
<input type="hidden" name="action" id="action" value="create">
<input type="hidden" name="id" id="id" value="">

<div class="form-group">
<label>Bulan</label>
<input type="month" name="bulan" id="bulan" value="<?=date('Y-m')?>" required>
</div>

<div class="form-group">
<label>Tanggal Layanan</label>
<input type="date" name="tanggal_layanan" id="tanggal_layanan" required>
</div>

<div class="form-group">
<label>Nama Pasien</label>
<input type="text" name="nama_pasien" id="nama_pasien" placeholder="Nama pasien/keluarga" required>
</div>

<div class="form-group">
<label>Tujuan/Rumah Sakit</label>
<input type="text" name="tujuan" id="tujuan" placeholder="Contoh: RSUD Tasikmalaya" required>
</div>

<div class="form-group">
<label>Jenis Layanan</label>
<select name="jenis_layanan" id="jenis_layanan" required>
<option value="Rujukan Biasa">Rujukan Biasa</option>
<option value="Emergency">Emergency</option>
<option value="Antar Jemput">Antar Jemput</option>
<option value="Lainnya">Lainnya</option>
</select>
</div>

<div style="display:flex;gap:1rem">
<button type="submit" class="btn btn-success" style="flex:1">💾 Simpan</button>
<button type="button" class="btn" style="flex:1;background:#6c757d;color:#fff" onclick="closeModal()">Batal</button>
</div>
</form>
</div>
</div>

<script>
function openModal(){ document.getElementById('modal').style.display='block'; }
function closeModal(){ document.getElementById('modal').style.display='none'; }
function editAmbulan(data) {
    document.getElementById('action').value='update';
    document.getElementById('id').value=data.id;
    document.getElementById('bulan').value=data.bulan;
    document.getElementById('tanggal_layanan').value=data.tanggal_layanan;
    document.getElementById('nama_pasien').value=data.nama_pasien;
    document.getElementById('tujuan').value=data.tujuan;
    document.getElementById('jenis_layanan').value=data.jenis_layanan;
    openModal();
}
</script>
</body>
</html>