<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Lazpersis Kabupaten Tasikmalaya - Lembaga Amil Zakat Terpercaya</title>
    
    <!-- SwiperJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
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
            color: #ffffff;
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
            opacity: 0.2;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
            color: #ffffff;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #fef9e6;
        }
        
        .btn-hero {
            display: inline-block;
            padding: 0.9rem 2.2rem;
            background: #fff;
            color: #0b5345;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            background: #f8fafc;
        }
        
        /* ========= TOMBOL ZIS ========= */
        .zis-buttons-section {
            max-width: 1200px;
            margin: -2rem auto 2rem;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }
        
        .zis-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        
        .zis-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            background: #fff;
            padding: 1rem 2rem;
            border-radius: 60px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            min-width: 110px;
        }
        
        .zis-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            background: #0b5345;
        }
        
        .zis-btn:hover .zis-icon,
        .zis-btn:hover .zis-text {
            color: #fff;
        }
        
        .zis-icon {
            font-size: 2rem;
            color: #0b5345;
        }
        
        .zis-text {
            font-size: 1rem;
            font-weight: 700;
            color: #0b5345;
        }
        
        /* ========= SLIDER DENGAN GAMBAR ========= */
        .berita-slider-section {
            background: #f1f5f9;
            padding: 3rem 2rem;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .section-title h2 {
            color: #0b5345;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .section-title p {
            color: #475569;
        }
        
        .swiper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 0 2rem 0;
        }
        
        .swiper-slide {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .swiper-slide:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        }
        
        /* GAMBAR DI SLIDER - style untuk img */
        .berita-image {
            width: 100%;
            height: 180px;
            overflow: hidden;
            background: linear-gradient(135deg, #0b5345, #198754);
        }
        
        .berita-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        /* fallback kalau gambar tidak ada */
        .berita-image .no-image {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }
        
        .berita-card {
            padding: 1.2rem;
        }
        
        .berita-card h4 {
            font-size: 1.1rem;
            color: #0b5345;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .berita-category {
            display: inline-block;
            font-size: 0.65rem;
            background: #e2e8f0;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            color: #0b5345;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .berita-date {
            font-size: 0.7rem;
            color: #b45309;
            margin-bottom: 0.5rem;
        }
        
        .berita-excerpt {
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        
        .readmore {
            color: #0b5345;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.8rem;
            cursor: pointer;
        }
        
        .readmore:hover {
            text-decoration: underline;
        }
        
        .swiper-pagination-bullet-active {
            background: #0b5345 !important;
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
        
        .zakat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
        }
        
        .zakat-item {
            background: #f8fafc;
            padding: 1.8rem;
            border-radius: 16px;
            text-align: center;
            border-left: 4px solid #0b5345;
            transition: all 0.3s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        
        .zakat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        
        .zakat-item h4 {
            color: #0b5345;
            margin-bottom: 0.75rem;
            font-size: 1.2rem;
        }
        
        .zakat-item p {
            color: #475569;
            font-size: 0.9rem;
        }
        
        /* Info Section */
        .info-section {
            background: linear-gradient(135deg, #0b5345 0%, #198754 100%);
            color: #fff;
            padding: 4rem 2rem;
            text-align: center;
        }
        
        .info-section h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #ffffff;
        }
        
        .info-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #fef9e6;
        }
        
        /* Footer */
        footer {
            background: #0f172a;
            color: #cbd5e1;
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
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-menu {
                justify-content: center;
                gap: 1rem;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .zis-buttons {
                gap: 1rem;
            }
            
            .zis-btn {
                padding: 0.7rem 1.2rem;
                min-width: 90px;
            }
            
            .zis-icon {
                font-size: 1.5rem;
            }
            
            .zis-text {
                font-size: 0.85rem;
            }
            
            .section-title h2 {
                font-size: 1.5rem;
            }
            
            .berita-image {
                height: 150px;
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
                <li><a href="#berita">Berita & Acara</a></li>
                <li><a href="#zakat">Zakat</a></li>
                <li><a href="#infaq">Infaq</a></li>
                <li><a href="#shodaqoh">Shodaqoh</a></li>
                <li><a href="#profil">Profil</a></li>
                <li><a href="../user/login.php" class="btn-nav btn-login">Masuk</a></li>
                <li><a href="../user/register.php" class="btn-nav btn-daftar">Daftar</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Zakat, Infaq, Shodaqoh</h1>
            <p>Salurkan harta Anda untuk keberkahan hidup dan kebahagiaan sesama</p>
            <a href="../user/register.php" class="btn-hero">Mulai Berdonasi Sekarang</a>
        </div>
    </section>

    <!-- TOMBOL ZAKAT, INFAQ, SHODAQOH -->
    <div class="zis-buttons-section">
        <div class="zis-buttons">
            <a href="../user/register.php" class="zis-btn">
                <span class="zis-icon">💰</span>
                <span class="zis-text">Zakat</span>
            </a>
            <a href="../user/register.php" class="zis-btn">
                <span class="zis-icon">🤲</span>
                <span class="zis-text">Infaq</span>
            </a>
            <a href="../user/register.php" class="zis-btn">
                <span class="zis-icon">⭐</span>
                <span class="zis-text">Shodaqoh</span>
            </a>
        </div>
    </div>

    <!-- ========= SLIDER BERITA, ACARA & PROGRAM ========= -->
    <section class="berita-slider-section" id="berita">
        <div class="container">
            <div class="section-title">
                <h2>📰 Berita, Acara & Program</h2>
                <p>Update kegiatan, program, dan agenda Lazpersis Kabupaten Tasikmalaya</p>
            </div>
            
            <div class="swiper beritaSwiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1 - Berita id=1 -->
                    <div class="swiper-slide">
                        <div class="berita-image">
                            <img src="images/ramadhan.jpg" alt="Persiapan Ramadhan" onerror="this.parentElement.innerHTML='<div class=\'no-image\'>🌙</div>'">
                        </div>
                        <div class="berita-card">
                            <span class="berita-category">Berita</span>
                            <h4>Persiapan Ramadhan 1448 H</h4>
                            <div class="berita-date">28 Mei 2026</div>
                            <div class="berita-excerpt">Lazpersis siapkan program berbagi takjil dan santunan yatim di seluruh kecamatan.</div>
                            <a href="./detail.php?id=1&type=berita" class="readmore">Baca selengkapnya →</a>
                        </div>
                    </div>
                    
                    <!-- Slide 2 - Program id=1 -->
                    <div class="swiper-slide">
                        <div class="berita-image">
                            <img src="images/beasiswa.jpg" alt="Beasiswa Prestasi" onerror="this.parentElement.innerHTML='<div class=\'no-image\'>🎓</div>'">
                        </div>
                        <div class="berita-card">
                            <span class="berita-category">Program</span>
                            <h4>Beasiswa Prestasi 2026</h4>
                            <div class="berita-date">25 Mei 2026</div>
                            <div class="berita-excerpt">Program beasiswa untuk santri berprestasi di Kabupaten Tasikmalaya.</div>
                            <a href="./detail.php?id=1&type=program" class="readmore">Baca selengkapnya →</a>
                        </div>
                    </div>
                    
                    <!-- Slide 3 - Program id=2 -->
                    <div class="swiper-slide">
                        <div class="berita-image">
                            <img src="images/pelatihan-amil.jpg" alt="Pelatihan Amil Zakat" onerror="this.parentElement.innerHTML='<div class=\'no-image\'>📢</div>'">
                        </div>
                        <div class="berita-card">
                            <span class="berita-category">Program</span>
                            <h4>Pelatihan Amil Zakat 2026</h4>
                            <div class="berita-date">20 Mei 2026</div>
                            <div class="berita-excerpt">Meningkatkan profesionalisme pengelolaan zakat di tingkat kecamatan se-Kabupaten Tasikmalaya.</div>
                            <a href="./detail.php?id=2&type=program" class="readmore">Baca selengkapnya →</a>
                        </div>
                    </div>
                    
                    <!-- Slide 4 - Program id=3 -->
                    <div class="swiper-slide">
                        <div class="berita-image">
                            <img src="images/bedah-rumah.jpg" alt="Bedah Rumah" onerror="this.parentElement.innerHTML='<div class=\'no-image\'>🏠</div>'">
                        </div>
                        <div class="berita-card">
                            <span class="berita-category">Program</span>
                            <h4>Bedah Rumah Tidak Layak Huni</h4>
                            <div class="berita-date">18 Mei 2026</div>
                            <div class="berita-excerpt">Program bedah rumah untuk warga kurang mampu se-Kabupaten Tasikmalaya.</div>
                            <a href="./detail.php?id=3&type=program" class="readmore">Baca selengkapnya →</a>
                        </div>
                    </div>
                    
                    <!-- Slide 5 - Berita id=2 -->
                    <div class="swiper-slide">
                        <div class="berita-image">
                            <img src="images/donasi-gempa.jpg" alt="Donasi Peduli Gempa" onerror="this.parentElement.innerHTML='<div class=\'no-image\'>🤝</div>'">
                        </div>
                        <div class="berita-card">
                            <span class="berita-category">Berita</span>
                            <h4>Open Donasi Peduli Gempa</h4>
                            <div class="berita-date">15 Mei 2026</div>
                            <div class="berita-excerpt">Salurkan bantuan untuk saudara kita yang terdampak bencana.</div>
                            <a href="./detail.php?id=2&type=berita" class="readmore">Baca selengkapnya →</a>
                        </div>
                    </div>
                    
                    <!-- Slide 6 - Program id=4 -->
                    <div class="swiper-slide">
                        <div class="berita-image">
                            <img src="images/penghargaan.jpg" alt="Lazpersis Raih Penghargaan" onerror="this.parentElement.innerHTML='<div class=\'no-image\'>🏆</div>'">
                        </div>
                        <div class="berita-card">
                            <span class="berita-category">Program</span>
                            <h4>Lazpersis Raih Penghargaan</h4>
                            <div class="berita-date">10 Mei 2026</div>
                            <div class="berita-excerpt">Lembaga Amil Zakat terbaik tingkat Provinsi Jawa Barat tahun 2026.</div>
                            <a href="./detail.php?id=4&type=program" class="readmore">Baca selengkapnya →</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

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
                    <h4>📦 Zakat Perdagangan</h4>
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
                    <h4>👨‍👩‍👧‍👦 Zakat Fitrah</h4>
                    <p>Zakat wajib di bulan Ramadan untuk setiap jiwa</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <h2>Siap Menunaikan Kewajiban?</h2>
            <p>Daftar sekarang dan mulai salurkan zakat, infaq, dan shodaqoh Anda melalui Lazpersis Kabupaten Tasikmalaya</p>
            <a href="../user/register.php" class="btn-hero">Daftar Akun Sekarang</a>
        </div>
    </section>

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
                    <a href="#zakat">Kalkulator Zakat</a>
                </p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 Lazpersis Kabupaten Tasikmalaya. All rights reserved.</p>
        </div>
    </footer>

    <!-- SwiperJS Script -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Inisialisasi slider berita, acara & program auto-slide dengan gambar
        var swiper = new Swiper('.beritaSwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
            }
        });
    </script>
</body>
</html>