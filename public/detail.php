<?php
// detail.php - Halaman detail berita & program Lazpersis

// Ambil parameter dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Data simulasi yang DISESUAIKAN dengan 6 slide di index.php
$data = [
    // BERITA
    'berita' => [
        1 => [
            'judul' => 'Persiapan Ramadhan 1448 H',
            'tanggal' => '28 Mei 2026',
            'kategori' => 'Berita',
            'gambar' => 'images/ramadhan.jpg',
            'isi' => '<p>Lazpersis Kabupaten Tasikmalaya mulai mempersiapkan program Ramadhan 1448 H. Berbagai kegiatan akan dilaksanakan seperti:</p>
                      <ul>
                          <li>Pembagian takjil gratis</li>
                          <li>Santunan anak yatim</li>
                          <li>Program berbagi sembako</li>
                          <li>Itikaf bersama di bulan Ramadhan</li>
                      </ul>
                      <p>Program ini akan dilaksanakan di seluruh kecamatan di Kabupaten Tasikmalaya. Mari kita sukseskan program Ramadhan bersama Lazpersis.</p>'
        ],
        2 => [
            'judul' => 'Open Donasi Peduli Gempa',
            'tanggal' => '15 Mei 2026',
            'kategori' => 'Berita',
            'gambar' => 'images/donasi-gempa.jpg',
            'isi' => '<p>Lazpersis membuka donasi untuk saudara kita yang terdampak bencana gempa bumi. Bantuan yang terkumpul akan disalurkan dalam bentuk:</p>
                      <ul>
                          <li>Logistik dan makanan siap saji</li>
                          <li>Selimut dan perlengkapan darurat</li>
                          <li>Layanan kesehatan mobile</li>
                          <li>Perbaikan darurat fasilitas umum</li>
                      </ul>
                      <p>Salurkan bantuan Anda melalui rekening resmi Lazpersis atau langsung datang ke kantor kami.</p>'
        ]
    ],
    // PROGRAM
    'program' => [
        1 => [
            'judul' => 'Beasiswa Prestasi 2026',
            'tanggal' => '25 Mei 2026',
            'kategori' => 'Program',
            'gambar' => 'images/beasiswa.jpg',
            'isi' => '<p>Program beasiswa untuk santri berprestasi di Kabupaten Tasikmalaya. Beasiswa ini mencakup:</p>
                      <ul>
                          <li>Biaya pendidikan penuh</li>
                          <li>Bantuan alat belajar</li>
                          <li>Uang saku bulanan</li>
                          <li>Pembinaan karakter dan kepemimpinan</li>
                      </ul>
                      <p>Pendaftaran dibuka mulai tanggal 1 Juni 2026. Syarat dan ketentuan dapat dilihat di kantor Lazpersis.</p>'
        ],
        2 => [
            'judul' => 'Pelatihan Amil Zakat 2026',
            'tanggal' => '20 Mei 2026',
            'kategori' => 'Program',
            'gambar' => 'images/pelatihan-amil.jpg',
            'isi' => '<p>Pelatihan Amil Zakat 2026 diadakan untuk meningkatkan profesionalisme pengelolaan zakat. Materi yang diajarkan:</p>
                      <ul>
                          <li>Manajemen pengumpulan zakat</li>
                          <li>Distribusi zakat yang tepat sasaran</li>
                          <li>Pelaporan dan transparansi keuangan</li>
                          <li>Etika pengelolaan dana umat</li>
                      </ul>
                      <p>Pelatihan ini diikuti oleh perwakilan dari seluruh kecamatan se-Kabupaten Tasikmalaya.</p>'
        ],
        3 => [
            'judul' => 'Bedah Rumah Tidak Layak Huni',
            'tanggal' => '18 Mei 2026',
            'kategori' => 'Program',
            'gambar' => 'images/bedah-rumah.jpg',
            'isi' => '<p>Program bedah rumah untuk warga kurang mampu se-Kabupaten Tasikmalaya. Program ini meliputi:</p>
                      <ul>
                          <li>Renovasi rumah tidak layak huni</li>
                          <li>Perbaikan atap dan dinding</li>
                          <li>Pembangunan fasilitas MCK sederhana</li>
                          <li>Pemasangan listrik bagi yang belum</li>
                      </ul>
                      <p>Program ini bekerja sama dengan donatur dan relawan. Informasi lebih lanjut hubungi kantor Lazpersis.</p>'
        ],
        4 => [
            'judul' => 'Lazpersis Raih Penghargaan',
            'tanggal' => '10 Mei 2026',
            'kategori' => 'Program',
            'gambar' => 'images/penghargaan.jpg',
            'isi' => '<p>Lazpersis Kabupaten Tasikmalaya meraih penghargaan sebagai Lembaga Amil Zakat terbaik tingkat Provinsi Jawa Barat tahun 2026. Penghargaan ini diberikan atas:</p>
                      <ul>
                          <li>Transparansi pengelolaan dana</li>
                          <li>Program pendayagunaan zakat yang inovatif</li>
                          <li>Jangkauan layanan hingga pelosok desa</li>
                          <li>Pelaporan yang akurat dan tepat waktu</li>
                      </ul>
                      <p>Penghargaan ini menjadi motivasi untuk terus meningkatkan pelayanan kepada masyarakat.</p>'
        ]
    ]
];

// Validasi tipe dan id
if (($type !== 'berita' && $type !== 'program') || $id === 0) {
    header('Location: index.php');
    exit;
}

// Ambil data berdasarkan tipe dan id
$item = isset($data[$type][$id]) ? $data[$type][$id] : null;

// Jika data tidak ditemukan
if (!$item) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($item['judul']); ?> - Lazpersis Kabupaten Tasikmalaya</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #1e293b;
            line-height: 1.6;
        }
        
        /* Navbar */
        .navbar {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            font-size: 1.3rem;
            font-weight: bold;
            color: #0b5345;
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .nav-menu a {
            text-decoration: none;
            color: #334155;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-menu a:hover {
            color: #0b5345;
        }
        
        .btn-nav {
            padding: 0.5rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login {
            color: #0b5345;
            border: 2px solid #0b5345;
            background: transparent;
        }
        
        .btn-login:hover {
            background: #0b5345;
            color: #fff;
        }
        
        .btn-daftar {
            background: #fff;
            color: #fff;
            border: 2px solid #0b5345;
        }
        
        .btn-daftar:hover {
            background: #083d32;
            border-color: #083d32;
        }
        
        /* Detail Container */
        .detail-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .detail-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .detail-image {
            width: 100%;
            height: 350px;
            overflow: hidden;
            background: linear-gradient(135deg, #0b5345, #198754);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .detail-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .detail-image .no-image {
            font-size: 5rem;
            color: white;
        }
        
        .detail-content {
            padding: 2rem;
        }
        
        .detail-category {
            display: inline-block;
            background: #e2e8f0;
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0b5345;
            margin-bottom: 1rem;
        }
        
        .detail-content h1 {
            font-size: 2rem;
            color: #0b5345;
            margin-bottom: 0.5rem;
        }
        
        .detail-date {
            color: #b45309;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-body {
            color: #334155;
            line-height: 1.8;
        }
        
        .detail-body p {
            margin-bottom: 1rem;
        }
        
        .detail-body ul {
            margin: 1rem 0 1rem 2rem;
        }
        
        .detail-body li {
            margin-bottom: 0.5rem;
        }
        
        .btn-back {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.7rem 1.8rem;
            background: #0b5345;
            color: #fff;
            text-decoration: none;
            border-radius: 40px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-back:hover {
            background: #083d32;
            transform: translateX(-5px);
        }
        
        .btn-donasi {
            display: inline-block;
            margin-top: 2rem;
            margin-left: 1rem;
            padding: 0.7rem 1.8rem;
            background: #fff;
            color: #0b5345;
            text-decoration: none;
            border-radius: 40px;
            font-weight: 600;
            border: 2px solid #0b5345;
            transition: all 0.3s;
        }
        
        .btn-donasi:hover {
            background: #0b5345;
            color: #fff;
        }
        
        /* Footer */
        footer {
            background: #0f172a;
            color: #cbd5e1;
            padding: 3rem 2rem 1rem;
            margin-top: 3rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            color: #198754;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .footer-section p, .footer-section a {
            color: #94a3b8;
            text-decoration: none;
            line-height: 1.7;
        }
        
        .footer-section a:hover {
            color: #198754;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #1e293b;
            color: #64748b;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-menu {
                justify-content: center;
                gap: 1rem;
            }
            
            .detail-image {
                height: 200px;
            }
            
            .detail-content h1 {
                font-size: 1.5rem;
            }
            
            .btn-donasi {
                margin-left: 0;
                margin-top: 1rem;
                display: block;
                text-align: center;
            }
            
            .btn-back {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Lazpersis Kabupaten Tasikmalaya</a>
            <ul class="nav-menu">
                <li><a href="index.php#berita">Berita & Acara</a></li>
                <li><a href="index.php#zakat">Zakat</a></li>
                <li><a href="zakat.php">Infaq</a></li>
                <li><a href="shadaqah.php">Shodaqoh</a></li>
                <li><a href="index.php#profil">Profil</a></li>
                <li><a href="../user/login.php" class="btn-nav btn-login">Masuk</a></li>
                <li><a href="../user/register.php" class="btn-nav btn-daftar">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Detail Content -->
    <div class="detail-container">
        <div class="detail-card">
            <div class="detail-image">
                <?php 
                $imagePath = $item['gambar'];
                if (file_exists($imagePath)) {
                    echo '<img src="' . htmlspecialchars($item['gambar']) . '" alt="' . htmlspecialchars($item['judul']) . '">';
                } else {
                    $emoji = ($type == 'berita') ? '📰' : '📋';
                    echo '<div class="no-image">' . $emoji . '</div>';
                }
                ?>
            </div>
            <div class="detail-content">
                <span class="detail-category"><?php echo htmlspecialchars($item['kategori']); ?></span>
                <h1><?php echo htmlspecialchars($item['judul']); ?></h1>
                <div class="detail-date">📅 <?php echo htmlspecialchars($item['tanggal']); ?></div>
                <div class="detail-body">
                    <?php echo $item['isi']; ?>
                </div>
                
                <a href="index.php" class="btn-back">← Kembali ke Beranda</a>
                <a href="../user/register.php" class="btn-donasi">🤲 Donasi Sekarang</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="profil">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Lazpersis Kabupaten Tasikmalaya</h3>
                <p>Lembaga Amil Zakat resmi di bawah naungan Persatuan Islam (Persis) Kabupaten Tasikmalaya, Jawa Barat.</p>
            </div>
            
            <div class="footer-section">
                <h3>Kontak Kami</h3>
                <p>
                    📍 Jln Raya Rajapolah Komplek Cibarani No. 11 Desa Manggungjaya Kec.Rajapolah Kab.Tasikmalaya Jawa Barat<br>
                    📞 +62 896-0416-4333<br>
                    📧 lazpersis.or.id
                </p>
            </div>
            
            <div class="footer-section">
                <h3>Tautan Cepat</h3>
                <p>
                    <a href="../user/login.php">Login Donatur</a><br>
                    <a href="../user/register.php">Daftar Akun</a><br>
                    <a href="index.php#zakat">Kalkulator Zakat</a>
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 Lazpersis Kabupaten Tasikmalaya. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>