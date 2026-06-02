<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'keuangan')) { header('Location: ../../user/dashboard.php'); exit; }

$userId = $_SESSION['user_id'];
$msg = '';

// HANDLE CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $bulan = $_POST['bulan'];
        $keterangan = $_POST['keterangan'];
        $dana_masuk = (float)($_POST['dana_masuk'] ?? 0);
        $dana_keluar = (float)($_POST['dana_keluar'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        
        // Debug: Log apa yang diterima
        error_log("ACTION: $action, ID: $id, BULAN: $bulan");
        
        if ($action === 'update' && $id > 0) {
            $stmt = $pdo->prepare("UPDATE laporan_keuangan SET bulan=?, keterangan=?, dana_masuk=?, dana_keluar=? WHERE id=? AND created_by=?");
            $stmt->execute([$bulan, $keterangan, $dana_masuk, $dana_keluar, $id, $userId]);
            $msg = "✅ Laporan ID $id berhasil diupdate!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO laporan_keuangan (bulan, keterangan, dana_masuk, dana_keluar, created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$bulan, $keterangan, $dana_masuk, $dana_keluar, $userId]);
            $msg = "✅ Laporan berhasil ditambahkan!";
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM laporan_keuangan WHERE id=? AND created_by=?")->execute([$id, $userId]);
        $msg = "🗑️ Laporan berhasil dihapus!";
    }
}

// AMBIL DATA
try {
    $reports = $pdo->prepare("SELECT * FROM laporan_keuangan WHERE created_by=? ORDER BY bulan DESC");
    $reports->execute([$userId]);
    $reports = $reports->fetchAll();
    
    $totalMasuk = array_sum(array_column($reports, 'dana_masuk'));
    $totalKeluar = array_sum(array_column($reports, 'dana_keluar'));
    $saldo = $totalMasuk - $totalKeluar;
} catch(PDOException $e) { $reports = []; $totalMasuk = $totalKeluar = $saldo = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Laporan Keuangan</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#0b5345;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1100px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#0b5345;margin-bottom:1rem;font-size:1.2rem}
.summary{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem}
.summary-item{background:#f8f9fa;padding:1rem;border-radius:8px;text-align:center}
.summary-item h3{font-size:0.9rem;color:#666}
.summary-item .value{font-size:1.5rem;font-weight:bold;color:#0b5345}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem}
.btn-primary{background:#0b5345;color:#fff}
.btn-success{background:#28a745;color:#fff}
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
.debug-info{background:#fff3cd;padding:0.5rem;margin-bottom:1rem;border-radius:4px;font-size:0.85rem}
</style></head>
<body>
<header class="header"><h1>📊 Laporan Keuangan</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary">
<div class="summary-item"><h3>Total Dana Masuk</h3><div class="value">Rp <?=number_format($totalMasuk,0,',','.')?></div></div>
<div class="summary-item"><h3>Total Dana Keluar</h3><div class="value">Rp <?=number_format($totalKeluar,0,',','.')?></div></div>
<div class="summary-item"><h3>Saldo</h3><div class="value">Rp <?=number_format($saldo,0,',','.')?></div></div>
</div>

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h2>Daftar Laporan</h2>
<button class="btn btn-primary" onclick="openModal('create')">+ Tambah Laporan</button>
</div>

<table>
<thead><tr><th>Bulan</th><th>Keterangan</th><th>Dana Masuk</th><th>Dana Keluar</th><th>Saldo</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($reports as $r): 
    $saldoBulan = $r['dana_masuk'] - $r['dana_keluar'];
    $bulanName = date('F Y', strtotime($r['bulan'].'-01'));
    // Escape data untuk JavaScript
    $jsonData = htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8');
?>
<tr>
<td><strong><?=ucfirst($bulanName)?></strong></td>
<td><?=htmlspecialchars($r['keterangan'])?></td>
<td style="color:#28a745">Rp <?=number_format($r['dana_masuk'],0,',','.')?></td>
<td style="color:#dc3545">Rp <?=number_format($r['dana_keluar'],0,',','.')?></td>
<td style="font-weight:600">Rp <?=number_format($saldoBulan,0,',','.')?></td>
<td>
<button class="btn btn-warning" onclick='openModal("edit", <?=$jsonData?>)'>✏️ Edit</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($reports)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Belum ada data</td></tr>"; ?>
</tbody>
</table>
</div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
<div class="modal-content">
<h2 id="modalTitle">Form Laporan</h2>
<div id="debugInfo" class="debug-info" style="display:none"></div>
<form method="POST" id="formLaporan">
<input type="hidden" name="action" id="formAction" value="create">
<input type="hidden" name="id" id="formId" value="">

<div class="form-group">
<label>Bulan</label>
<input type="month" name="bulan" id="inputBulan" value="<?=date('Y-m')?>" required>
</div>

<div class="form-group">
<label>Keterangan</label>
<textarea name="keterangan" id="inputKeterangan" rows="3" required></textarea>
</div>

<div class="form-group">
<label>Dana Masuk (Rp)</label>
<input type="number" name="dana_masuk" id="inputDanaMasuk" value="0" min="0" required>
</div>

<div class="form-group">
<label>Dana Keluar (Rp)</label>
<input type="number" name="dana_keluar" id="inputDanaKeluar" value="0" min="0" required>
</div>

<div style="display:flex;gap:1rem">
<button type="submit" class="btn btn-success" style="flex:1">💾 Simpan</button>
<button type="button" class="btn" style="flex:1;background:#6c757d;color:#fff" onclick="closeModal()">Batal</button>
</div>
</form>
</div>
</div>

<script>
function openModal(mode, data) {
    console.log('Mode:', mode);
    console.log('Data:', data);
    
    const modal = document.getElementById('modal');
    const formAction = document.getElementById('formAction');
    const formId = document.getElementById('formId');
    const modalTitle = document.getElementById('modalTitle');
    const debugInfo = document.getElementById('debugInfo');
    
    // Reset form dulu
    document.getElementById('formLaporan').reset();
    
    if (mode === 'edit' && data) {
        // MODE EDIT
        formAction.value = 'update';
        formId.value = data.id || '';
        modalTitle.innerText = 'Edit Laporan (ID: ' + data.id + ')';
        
        // Isi form dengan data
        document.getElementById('inputBulan').value = data.bulan || '';
        document.getElementById('inputKeterangan').value = data.keterangan || '';
        document.getElementById('inputDanaMasuk').value = data.dana_masuk || 0;
        document.getElementById('inputDanaKeluar').value = data.dana_keluar || 0;
        
        // Tampilkan debug info
        debugInfo.innerHTML = '<strong>DEBUG:</strong> Action=' + formAction.value + ', ID=' + formId.value;
        debugInfo.style.display = 'block';
        
        console.log('Form setelah diisi:', {
            action: formAction.value,
            id: formId.value,
            bulan: document.getElementById('inputBulan').value
        });
    } else {
        // MODE TAMBAH
        formAction.value = 'create';
        formId.value = '';
        modalTitle.innerText = 'Tambah Laporan Baru';
        debugInfo.style.display = 'none';
    }
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

// Tutup modal jika klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('modal');
    if (event.target == modal) {
        closeModal();
    }
}

// Debug saat form submit
document.getElementById('formLaporan').addEventListener('submit', function(e) {
    const action = document.getElementById('formAction').value;
    const id = document.getElementById('formId').value;
    console.log('FORM SUBMIT - Action:', action, 'ID:', id);
    alert('Akan ' + (action === 'update' ? 'MENGUPDATE' : 'MENAMBAH') + ' data' + (id ? ' ID=' + id : ''));
});
</script>
</body>
</html>