<?php
session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') { 
    header('Location: login.php'); 
    exit; 
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infaq - Lazpersis Rajapolah</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8f9fa; color: #333; }
        .header { 
            background: #0b5345; 
            color: #fff; 
            padding: 1rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header h1 { font-size: 1.2rem; }
        .back-btn { 
            color: #fff; 
            text-decoration: none; 
            padding: 0.5rem 1rem; 
            border: 1px solid rgba(255,255,255,0.3); 
            border-radius: 6px; 
            font-size: 0.9rem; 
        }
        .container { 
            max-width: 500px; 
            margin: 3rem auto; 
            padding: 0 1rem; 
        }
        .card { 
            background: #fff; 
            padding: 2rem; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.06); 
        }
        .card h2 { 
            color: #0b5345; 
            margin-bottom: 0.5rem; 
            text-align: center; 
        }
        .card p { 
            text-align: center; 
            color: #666; 
            margin-bottom: 1.5rem; 
            font-size: 0.95rem; 
        }
        .form-group { 
            margin-bottom: 1.2rem; 
        }
        .form-group label { 
            display: block; 
            font-weight: 500; 
            margin-bottom: 0.4rem; 
            color: #444; 
        }
        .form-group input { 
            width: 100%; 
            padding: 0.8rem; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 1rem; 
        }
        .form-group input:focus { 
            border-color: #0b5345; 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(11,83,69,0.1); 
        }
        .quick-amounts { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 0.5rem; 
            margin-bottom: 1.2rem; 
        }
        .quick-btn { 
            padding: 0.6rem; 
            background: #e9ecef; 
            border: 1px solid #dee2e6; 
            border-radius: 6px; 
            cursor: pointer; 
            text-align: center; 
            font-weight: 500; 
            transition: all 0.3s;
        }
        .quick-btn:hover { 
            background: #0b5345; 
            color: #fff; 
            border-color: #0b5345; 
        }
        .btn { 
            width: 100%; 
            padding: 0.9rem; 
            background: #0b5345; 
            color: #fff; 
            border: none; 
            border-radius: 8px; 
            font-size: 1rem; 
            font-weight: 600; 
            cursor: pointer; 
        }
        .btn:hover { 
            background: #083d32; 
        }
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #28a745;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #155724;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>🤲 Infaq</h1>
        <a href="dashboard.php" class="back-btn">← Kembali</a>
    </header>
    
    <div class="container">
        <div class="card">
            <h2>Salurkan Infaq Anda</h2>
            <p>Infaq adalah pemberian sukarela untuk kebaikan di jalan Allah</p>
            
            <div class="info-box">
                💡 <strong>Keutamaan Infaq:</strong><br>
                "Perumpamaan orang yang menginfakkan hartanya di jalan Allah seperti sebutir biji yang menumbuhkan tujuh tangkai..." (QS. Al-Baqarah: 261)
            </div>
            
            <form action="process_payment.php" method="POST">
                <input type="hidden" name="type" value="infaq">
                
                <div class="quick-amounts">
                    <button type="button" class="quick-btn" onclick="setAmount(25000)">Rp 25.000</button>
                    <button type="button" class="quick-btn" onclick="setAmount(50000)">Rp 50.000</button>
                    <button type="button" class="quick-btn" onclick="setAmount(100000)">Rp 100.000</button>
                    <button type="button" class="quick-btn" onclick="setAmount(200000)">Rp 200.000</button>
                    <button type="button" class="quick-btn" onclick="setAmount(500000)">Rp 500.000</button>
                    <button type="button" class="quick-btn" onclick="setAmount(1000000)">Rp 1.000.000</button>
                </div>
                
                <div class="form-group">
                    <label>Nominal Infaq (Rp)</label>
                    <input type="number" name="amount" id="amount" min="1000" step="1000" required placeholder="Masukkan nominal atau pilih tombol di atas">
                </div>
                
                <div class="form-group">
                    <label>Catatan (Opsional)</label>
                    <input type="text" name="note" placeholder="Contoh: Untuk program beasiswa">
                </div>
                
                <button type="submit" class="btn">Lanjut ke Pembayaran QRIS</button>
            </form>
        </div>
    </div>
    
    <script>
        function setAmount(val) { 
            document.getElementById('amount').value = val; 
        }
    </script>
</body>
</html>