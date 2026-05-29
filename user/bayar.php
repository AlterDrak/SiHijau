<?php
require '../includes/auth.php'; requireRole(['user']);
$judul = 'Pembayaran'; include '../includes/header.php';
?>
<h2>Bayar Zakat / Shadaqah</h2>
<form action="process_bayar.php" method="POST">
    <input type="hidden" name="csrf" value="<?= csrf() ?>">
    <label>Jenis</label>
    <select name="type" id="type" required onchange="document.getElementById('zt').style.display=this.value=='zakat'?'block':'none'">
        <option value="shadaqah">Shadaqah / Infaq</option>
        <option value="zakat">Zakat</option>
    </select>
    <div id="zt" style="display:none;">
        <label>Jenis Zakat</label>
        <select name="zakat_type">
            <option value="maal">Maal</option><option value="profesi">Profesi</option><option value="fitrah">Fitrah</option>
        </select>
    </div>
    <label>Nominal (Rp)</label>
    <input type="number" name="amount" min="1000" step="1000" required>
    <button type="submit">Lanjut ke QRIS</button>
</form>
<?php include '../includes/footer.php'; ?>