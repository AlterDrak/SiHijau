<?php
session_start(); require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar Zakat - Lazpersis</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
        body{background:#f8f9fa;color:#333}
        .header{background:#0b5345;color:#fff;padding:1rem 2rem;display:flex;justify-content:space-between;align-items:center}
        .header h1{font-size:1.2rem}
        .back-btn{color:#fff;text-decoration:none;padding:0.5rem 1rem;border:1px solid rgba(255,255,255,0.3);border-radius:6px;font-size:0.9rem}
        .container{max-width:500px;margin:3rem auto;padding:0 1rem}
        .card{background:#fff;padding:2rem;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.06)}
        .card h2{color:#0b5345;margin-bottom:0.5rem;text-align:center}
        .card p{text-align:center;color:#666;margin-bottom:1.5rem;font-size:0.95rem}
        .form-group{margin-bottom:1.2rem}
        .form-group label{display:block;font-weight:500;margin-bottom:0.4rem;color:#444}
        .form-group input,.form-group select{width:100%;padding:0.8rem;border:1px solid #ddd;border-radius:8px;font-size:1rem}
        .form-group input:focus,.form-group select:focus{border-color:#0b5345;outline:none;box-shadow:0 0 0 3px rgba(11,83,69,0.1)}
        .btn{width:100%;padding:0.9rem;background:#0b5345;color:#fff;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer}
        .btn:hover{background:#083d32}
    </style>
</head>
<body>
    <header class="header">
        <h1>💰 Bayar Zakat</h1>
        <a href="dashboard.php" class="back-btn">← Kembali</a>
    </header>
    <div class="container">
        <div class="card">
            <h2>Tunaikan Zakat Anda</h2>
            <p>Pilih kategori & masukkan nominal</p>
            <form action="process_payment.php" method="POST">
                <input type="hidden" name="type" value="zakat">
                <div class="form-group">
                    <label>Kategori Zakat</label>
                    <select name="category" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="maal">Zakat Maal</option>
                        <option value="perdagangan">Zakat Perdagangan</option>
                        <option value="perhiasan">Zakat Perhiasan</option>
                        <option value="fitrah">Zakat Fitrah</option>
                        <option value="pertanian">Zakat Pertanian</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nominal Zakat (Rp)</label>
                    <input type="number" name="amount" min="1000" step="1000" required placeholder="Contoh: 150000">
                </div>
                <button type="submit" class="btn">Lanjut ke Pembayaran QRIS</button>
            </form>
        </div>
    </div>
</body>
</html>