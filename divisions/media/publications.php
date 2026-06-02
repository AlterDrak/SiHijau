<?php
session_start();
require '../../config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../../user/login.php'); exit; }
$role = $_SESSION['role']; $div = $_SESSION['division'] ?? '';
if ($role !== 'super_admin' && ($role !== 'staff' || $div !== 'media')) { header('Location: ../../user/dashboard.php'); exit; }

$userId = $_SESSION['user_id'];
$msg = '';

// HANDLE CRUD PUBLIKASI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $judul = $_POST['judul'];
        $kategori = $_POST['kategori'];
        $tanggal = $_POST['tanggal'];
        $konten = $_POST['konten'];
        $id = (int)($_POST['id'] ?? 0);
        
        if ($action === 'update' && $id > 0) {
            $pdo->prepare("UPDATE publications SET title=?, category=?, published_date=?, content=? WHERE id=?")
                ->execute([$judul, $kategori, $tanggal, $konten, $id]);
            $msg = "✅ Publikasi berhasil diupdate!";
        } else {
            $pdo->prepare("INSERT INTO publications (title, category, published_date, content) VALUES (?, ?, ?, ?)")
                ->execute([$judul, $kategori, $tanggal, $konten]);
            $msg = "✅ Publikasi berhasil ditambahkan!";
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM publications WHERE id=?")->execute([$id]);
        $msg = "🗑️ Publikasi berhasil dihapus!";
    }
}

// AMBIL DATA
try {
    $pubStmt = $pdo->query("SELECT * FROM publications ORDER BY published_date DESC");
    $pubData = $pubStmt->fetchAll();
    $totalPublikasi = count($pubData);
} catch(PDOException $e) { $pubData = []; $totalPublikasi = 0; }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Publikasi & Berita - Media</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:#f8f9fa;color:#333}
.header{background:#6f42c1;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
.header h1{font-size:1.2rem}
.back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px}
.container{max-width:1100px;margin:2rem auto;padding:0 1rem}
.card{background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:1.5rem}
.card h2{color:#6f42c1;margin-bottom:1rem;font-size:1.2rem}
.summary{background:#f8f9fa;padding:1rem;border-radius:8px;margin-bottom:1.5rem;text-align:center}
.summary h3{color:#6f42c1;font-size:1.5rem}
.btn{padding:0.5rem 1rem;border:none;border-radius:6px;cursor:pointer;font-size:0.9rem}
.btn-primary{background:#6f42c1;color:#fff}
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
.modal-content{background:#fff;padding:2rem;border-radius:12px;max-width:700px;margin:2rem auto}
</style></head>
<body>
<header class="header"><h1>📰 Publikasi & Berita</h1><a href="index.php" class="back-btn">← Kembali</a></header>
<div class="container">

<?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

<div class="summary"><h3>Total Publikasi: <?=$totalPublikasi?></h3></div>

<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
<h2>Daftar Publikasi</h2>
<button class="btn btn-primary" onclick="openModal()">+ Tambah Publikasi</button>
</div>

<table>
<thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($pubData as $p): 
    $tglFormat = date('d/m/Y', strtotime($p['published_date']));
?>
<tr>
<td><?=$tglFormat?></td>
<td><strong><?=htmlspecialchars($p['title'])?></strong></td>
<td><span style="background:#e9ecef;padding:0.3rem 0.6rem;border-radius:12px;font-size:0.85rem"><?=htmlspecialchars($p['category'])?></span></td>
<td>
<button class="btn btn-warning" onclick='editPub(<?=json_encode($p)?>)'>✏️ Edit</button>
<form method="POST" style="display:inline" onsubmit="return confirm('Hapus?')">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$p['id']?>">
<button type="submit" class="btn btn-danger">🗑️</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if(empty($pubData)) echo "<tr><td colspan='4' style='text-align:center;color:#888'>Belum ada publikasi</td></tr>"; ?>
</tbody>
</table>
</div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
<div class="modal-content">
<h2 id="modalTitle">Tambah Publikasi</h2>
<form method="POST">
<input type="hidden" name="action" id="action" value="create">
<input type="hidden" name="id" id="id" value="">

<div class="form-group">
<label>Judul Berita/Publikasi</label>
<input type="text" name="judul" id="judul" required>
</div>

<div class="form-group">
<label>Kategori</label>
<select name="kategori" id="kategori" required>
<option value="Berita">Berita</option>
<option value="Kegiatan">Kegiatan</option>
<option value="Laporan">Laporan</option>
<option value="Pengumuman">Pengumuman</option>
</select>
</div>

<div class="form-group">
<label>Tanggal Publikasi</label>
<input type="date" name="tanggal" id="tanggal" value="<?=date('Y-m-d')?>" required>
</div>

<div class="form-group">
<label>Konten</label>
<textarea name="konten" id="konten" rows="5" required></textarea>
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
    document.getElementById('modalTitle').innerText='Tambah Publikasi';
}
function closeModal(){ document.getElementById('modal').style.display='none'; }
function editPub(data) {
    document.getElementById('action').value='update';
    document.getElementById('id').value=data.id;
    document.getElementById('judul').value=data.title;
    document.getElementById('kategori').value=data.category;
    document.getElementById('tanggal').value=data.published_date;
    document.getElementById('konten').value=data.content||'';
    document.getElementById('modalTitle').innerText='Edit Publikasi';
    openModal();
}
</script>
</body>
</html>