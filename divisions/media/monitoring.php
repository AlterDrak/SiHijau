<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'media')) { header('Location: ../../user/dashboard.php'); exit; }

$userId = $_SESSION['user_id'];
$msg = '';

// HANDLE CRUD JADWAL PUBLIKASI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $tanggal_publikasi = $_POST['tanggal_publikasi'];
        $judul_konten = $_POST['judul_konten'];
        $media = $_POST['media'];
        $status = $_POST['status'];
        $konten = $_POST['konten'];
        $id = (int)($_POST['id'] ?? 0);
        
        if ($action === 'create') {
            $pdo->prepare("INSERT INTO jadwal_publikasi (tanggal_publikasi, judul_konten, media, status, konten, created_by) VALUES (?, ?, ?, ?, ?, ?)")
                ->execute([$tanggal_publikasi, $judul_konten, $media, $status, $konten, $userId]);
            $msg = "✅ Jadwal publikasi berhasil ditambahkan!";
        } else {
            $pdo->prepare("UPDATE jadwal_publikasi SET tanggal_publikasi=?, judul_konten=?, media=?, status=?, konten=? WHERE id=? AND created_by=?")
                ->execute([$tanggal_publikasi, $judul_konten, $media, $status, $konten, $id, $userId]);
            $msg = "✅ Jadwal publikasi berhasil diupdate!";
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM jadwal_publikasi WHERE id=? AND created_by=?")->execute([$id, $userId]);
        $msg = "🗑️ Jadwal publikasi berhasil dihapus!";
    }
}

// AMBIL DATA
try {
    $publikasiStmt = $pdo->prepare("SELECT j.*, u.username FROM jadwal_publikasi j LEFT JOIN users u ON j.created_by=u.id ORDER BY j.tanggal_publikasi ASC");
    $publikasiStmt->execute();
    $publikasiData = $publikasiStmt->fetchAll();
    
    // Statistik
    $totalPublikasi = count($publikasiData);
    $terbit = count(array_filter($publikasiData, fn($p) => $p['status']==='terbit'));
    $draft = count(array_filter($publikasiData, fn($p) => $p['status']==='draft'));
} catch(PDOException $e) { $publikasiData = []; $totalPublikasi = $terbit = $draft = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Jadwal Publikasi - CRUD</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#6f42c1;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1200px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#6f42c1;margin-bottom:1rem;font-size:1.2rem}
.summary{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem}
.summary-item{background:#f8f9fa;padding:1rem;border-radius:8px;text-align:center}
.summary-item h3{font-size:0.9rem;color:#666}
.summary-item .value{font-size:1.5rem;font-weight:bold;color:#6f42c1}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem;margin:0.2rem}
.btn-primary{background:#6f42c1;color:#fff}
.btn-success{background:#28a745;color:#fff}
.btn-danger{background:#dc3545;color:#fff}
.btn-warning{background:#ffc107;color:#000}
.form-group{margin-bottom:1rem}
.form-group label{display:block;font-weight:600;margin-bottom:0.4rem}
.form-group input,.form-group textarea,.form-group select{width:100%;padding:0.6rem;border:1px solid #ddd;border-radius:6px}
table{width:100%;border-collapse:collapse}
th,td{padding:0.8rem;text-align:left;border-bottom:1px solid #eee}
th{background:#f8f9fa;font-weight:600}
.alert{padding:1rem;border-radius:6px;margin-bottom:1rem}
.alert-success{background:#d1e7dd;color:#0f5132}
.badge{padding:0.3rem 0.7rem;border-radius:12px;font-size:0.8rem;font-weight:600}
.badge-draft{background:#fff3cd;color:#856404}
.badge-terjadwal{background:#d1ecf1;color:#0c5460}
.badge-terbit{background:#d4edda;color:#155724}
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;overflow-y:auto}
.modal-content{background:#fff;padding:2rem;border-radius:12px;max-width:700px;margin:2rem auto}
</style></head>
<body>
<header class="header"><h1>📅 Jadwal Publikasi</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary">
<div class="summary-item"><h3>Total Jadwal</h3><div class="value"><?=$totalPublikasi?></div></div>
<div class="summary-item"><h3>Sudah Terbit</h3><div class="value" style="color:#28a745"><?=$terbit?></div></div>
<div class="summary-item"><h3>Draft</h3><div class="value" style="color:#ffc107"><?=$draft?></div></div>
</div>

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h2>📆 Kalender Publikasi Media</h2>
<button class="btn btn-primary" onclick="openModal()">+ Tambah Jadwal</button>
</div>

<table>
<thead><tr><th>Tanggal</th><th>Judul Konten</th><th>Media</th><th>Status</th><th>Dibuat Oleh</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($publikasiData as $p): 
    $tglFormat = date('d/m/Y', strtotime($p['tanggal_publikasi']));
    $badgeClass = 'badge-'.$p['status'];
    $statusLabel = ucfirst($p['status']);
?>
<tr>
<td><strong><?=$tglFormat?></strong></td>
<td><?=htmlspecialchars($p['judul_konten'])?></td>
<td><?=htmlspecialchars($p['media'])?></td>
<td><span class="badge <?=$badgeClass?>"><?=$statusLabel?></span></td>
<td><?=htmlspecialchars($p['username'] ?? 'Admin')?></td>
<td>
<button class="btn btn-warning" onclick='editPublikasi(<?=json_encode($p)?>)'>✏️</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus jadwal ini?')">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$p['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($publikasiData)) echo "<tr><td colspan='6' style='text-align:center;color:#888'>Belum ada jadwal publikasi</td></tr>"; ?>
</tbody>
</table>
</div>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
<div class="modal-content">
<h2>📱 Jadwal Publikasi</h2>
<form method="POST">
<input type="hidden" name="action" id="action" value="create">
<input type="hidden" name="id" id="id" value="">

<div class="form-group">
<label>Tanggal Publikasi</label>
<input type="date" name="tanggal_publikasi" id="tanggal_publikasi" value="<?=date('Y-m-d')?>" required>
</div>

<div class="form-group">
<label>Judul Konten</label>
<input type="text" name="judul_konten" id="judul_konten" placeholder="Contoh: Laporan Zakat Desember 2025" required>
</div>

<div class="form-group">
<label>Media Publikasi</label>
<select name="media" id="media" required>
<option value="Instagram">Instagram</option>
<option value="Facebook">Facebook</option>
<option value="Twitter">Twitter</option>
<option value="YouTube">YouTube</option>
<option value="Website">Website</option>
<option value="WhatsApp">WhatsApp Group</option>
<option value="Multiple">Multiple Platform</option>
</select>
</div>

<div class="form-group">
<label>Status</label>
<select name="status" id="status" required>
<option value="draft">Draft</option>
<option value="terjadwal">Terjadwal</option>
<option value="terbit">Sudah Terbit</option>
</select>
</div>

<div class="form-group">
<label>Konten/Caption</label>
<textarea name="konten" id="konten" rows="4" placeholder="Tulis konten atau caption di sini..."></textarea>
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
function editPublikasi(data) {
    document.getElementById('action').value='update';
    document.getElementById('id').value=data.id;
    document.getElementById('tanggal_publikasi').value=data.tanggal_publikasi;
    document.getElementById('judul_konten').value=data.judul_konten;
    document.getElementById('media').value=data.media;
    document.getElementById('status').value=data.status;
    document.getElementById('konten').value=data.konten||'';
    openModal();
}
</script>
</body>
</html>