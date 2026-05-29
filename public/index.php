<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lazpersis Rajapolah - Lembaga Amil Zakat Terpercaya</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Navbar */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0b5345;
            text-decoration: none;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-menu a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-menu a:hover {
            color: #0b5345;
        }
        
        .btn-nav {
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login {
            color: #0b5345;
            border: 2px solid #0b5345;
        }
        
        .btn-login:hover {
            background: #0b5345;
            color: #fff;
        }
        
        .btn-daftar {
            background: #0b5345;
            color: #fff;
            border: 2px solid #0b5345;
        }
        
        .btn-daftar:hover {
            background: #083d32;
            border-color: #083d32;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #0b5345 0%, #198754 100%);
            padding: 4rem 2rem;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        .btn-hero {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: #fff;
            color: #0b5345;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        /* Main Cards Section */
        .main-cards {
            max-width: 1200px;
            margin: -3rem auto 3rem;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .card-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #0b5345, #198754);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
        }
        
        .card h3 {
            color: #0b5345;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        
        .card p {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .card-btn {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            background: #0b5345;
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .card-btn:hover {
            background: #083d32;
            transform: translateX(5px);
        }
        
        /* Zakat Categories */
        .zakat-section {
            background: #fff;
            padding: 4rem 2rem;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            color: #0b5345;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .section-title p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .zakat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }
        
        .zakat-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            border-left: 4px solid #0b5345;
            transition: all 0.3s;
        }
        
        .zakat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .zakat-item h4 {
            color: #0b5345;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        /* Info Section */
        .info-section {
            background: linear-gradient(135deg, #0b5345 0%, #198754 100%);
            color: #fff;
            padding: 4rem 2rem;
            text-align: center;
        }
        
        .info-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .info-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }
        
        /* Footer */
        footer {
            background: #1a1a1a;
            color: #fff;
            padding: 3rem 2rem 1rem;
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
        }
        
        .footer-section p, .footer-section a {
            color: #aaa;
            text-decoration: none;
            line-height: 1.8;
        }
        
        .footer-section a:hover {
            color: #198754;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #333;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🕌 Lazpersis Rajapolah</a>
            <ul class="nav-menu">
                <li><a href="#berita">Berita & Acara</a></li>
                <li><a href="#zakat">Zakat</a></li>
                <li><a href="#infaq">Infaq</a></li>
                <li><a href="#shodaqoh">Shodaqoh</a></li>
                <li><a href="#profil">Profil</a></li>
                <li><a href="user/login.php" class="btn-nav btn-login">Masuk</a></li>
                <li><a href="user/register.php" class="btn-nav btn-daftar">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>💚 Zakat, Infaq, Shodaqoh</h1>
            <p>Salurkan harta Anda untuk keberkahan hidup dan kebahagiaan sesama</p>
            <a href="user/register.php" class="btn-hero">Mulai Berdonasi Sekarang</a>
        </div>
    </section>

    <!-- Main Cards -->
    <div class="main-cards">
        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">📰</div>
                <h3>Berita & Acara</h3>
                <p>Update kegiatan, program, dan agenda Lazpersis Rajapolah</p>
                <a href="#berita" class="card-btn">Lihat Selengkapnya</a>
            </div>
            
            <div class="card">
                <div class="card-icon">💰</div>
                <h3>Zakat</h3>
                <p>Tunaikan zakat Anda dengan 5 kategori: Maal, Perdagangan, Perhiasan, Fitrah, dan Pertanian</p>
                <a href="user/register.php" class="card-btn">Bayar Zakat</a>
            </div>
            
            <div class="card">
                <div class="card-icon">🤲</div>
                <h3>Infaq</h3>
                <p>Sisihkan rezeki untuk kebaikan dan keberkahan hidup Anda</p>
                <a href="user/register.php" class="card-btn">Salurkan Infaq</a>
            </div>
            
            <div class="card">
                <div class="card-icon">🌟</div>
                <h3>Shodaqoh</h3>
                <p>Wujudkan kepedulian sosial melalui shodaqoh untuk sesama</p>
                <a href="user/register.php" class="card-btn">Berikan Shodaqoh</a>
            </div>
        </div>
    </div>

    <!-- Zakat Categories Section -->
    <section class="zakat-section" id="zakat">
        <div class="container">
            <div class="section-title">
                <h2>Jenis-Jenis Zakat</h2>
                <p>Pilih kategori zakat yang ingin Anda tunaikan</p>
            </div>
            
            <div class="zakat-grid">
                <div class="zakat-item">
                    <h4>💎 Zakat Maal</h4>
                    <p>Zakat atas harta kekayaan yang telah mencapai nisab</p>
                </div>
                
                <div class="zakat-item">
                    <h4> Zakat Perdagangan</h4>
                    <p>Zakat dari hasil perdagangan dan bisnis</p>
                </div>
                
                <div class="zakat-item">
                    <h4>💍 Zakat Perhiasan</h4>
                    <p>Zakat emas, perak, dan perhiasan lainnya</p>
                </div>
                
                <div class="zakat-item">
                    <h4>🌾 Zakat Pertanian</h4>
                    <p>Zakat hasil pertanian dan perkebunan</p>
                </div>
                
                <div class="zakat-item">
                    <h4>👨‍👩‍‍👦 Zakat Fitrah</h4>
                    <p>Zakat wajib di bulan Ramadan untuk setiap jiwa</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <h2>Siap Menunaikan Kewajiban?</h2>
            <p>Daftar sekarang dan mulai salurkan zakat, infaq, dan shodaqoh Anda melalui Lazpersis Rajapolah</p>
            <a href="user/register.php" class="btn-hero">Daftar Akun Sekarang</a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="profil">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Lazpersis Rajapolah</h3>
                <p>Lembaga Amil Zakat resmi di bawah naungan Persatuan Islam (Persis) Cabang Rajapolah, Kabupaten Tasikmalaya, Jawa Barat.</p>
            </div>
            
            <div class="footer-section">
                <h3>Kontak Kami</h3>
                <p>
                    📍 Jalan Rajapolah, Tasikmalaya, Jawa Barat<br>
                    📞 (0265) XXXXXX<br>
                    📧 info@lazpersis-rajapolah.or.id
                </p>
            </div>
            
            <div class="footer-section">
                <h3>Tautan Cepat</h3>
                <p>
                    <a href="user/login.php">Login Donatur</a><br>
                    <a href="user/register.php">Daftar Akun</a><br>
                    <a href="#zakat">Kalkulator Zakat</a>
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 Lazpersis Cabang Rajapolah. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>