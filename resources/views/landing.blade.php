<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEpintu | Portal Layanan BPS Kabupaten Tuban</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0058a8;
            --secondary: #00aaff;
            --dark: #1e293b;
            --light: #f8fbff;
            --success: #16a34a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(0, 88, 168, 0.05);
            padding: 1rem 0;
            transition: all 0.3s;
        }

        .navbar-brand img { height: 45px; }
        
        .nav-link {
            font-weight: 600;
            color: var(--dark) !important;
            padding: 0.5rem 1.2rem !important;
            transition: all 0.3s;
        }

        .nav-link:hover { color: var(--primary) !important; }

        .btn-login-nav {
            background: var(--primary);
            color: white !important;
            border-radius: 12px;
            padding: 8px 25px !important;
            box-shadow: 0 4px 15px rgba(0, 88, 168, 0.2);
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px;
            background: radial-gradient(circle at 90% 10%, rgba(0, 170, 255, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 10% 90%, rgba(0, 88, 168, 0.03) 0%, transparent 40%);
            position: relative;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 2.5rem;
            max-width: 600px;
        }

        /* Cards */
        .service-card {
            border: none;
            border-radius: 30px;
            padding: 40px;
            transition: all 0.4s;
            height: 100%;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 88, 168, 0.05);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 88, 168, 0.1);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .bg-ticket { background: #eef6ff; color: var(--primary); }
        .bg-kms { background: #f0fdf4; color: var(--success); }

        .btn-action {
            border-radius: 15px;
            padding: 12px 25px;
            font-weight: 700;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .btn-ticket-action { background: var(--primary); color: white; border: none; }
        .btn-kms-action { background: var(--success); color: white; border: none; }

        /* KMS Section */
        .kms-section { padding: 100px 0; background-color: var(--light); }
        
        .section-tag {
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            margin-bottom: 10px;
            display: block;
        }

        .section-title { font-weight: 800; font-size: 2.2rem; margin-bottom: 50px; }

        .article-card {
            border: none;
            border-radius: 24px;
            transition: all 0.3s;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }

        .article-card:hover { transform: scale(1.02); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }

        /* Footer */
        .footer {
            padding: 80px 0 30px;
            background: var(--dark);
            color: rgba(255, 255, 255, 0.7);
        }

        .footer-logo img { height: 50px; margin-bottom: 20px; }
        .footer-title { color: white; font-weight: 700; margin-bottom: 25px; }
        
        .footer-link {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .footer-link:hover { color: var(--secondary); padding-left: 5px; }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('img/logo_harmoni.png') }}" alt="Logo Harmoni" class="me-2">
                <div>
                    <div class="fw-bold text-primary fs-5 lh-1">SEpintu</div>
                    <small class="text-muted" style="font-size: 0.6rem; letter-spacing: 1px;">BPS KABUPATEN TUBAN</small>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kms">Knowledge Base</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link btn-login-nav" href="{{ route('login') }}">
                            <i class="fas fa-user-circle me-2"></i> Login Pegawai
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="section-tag">Portal Layanan Terpadu</span>
                    <h1 class="hero-title">Solusi Cepat & Tepat Untuk Anda</h1>
                    <p class="hero-subtitle">
                        Sistem Ekosistem Pelayanan Instansi Terpadu (SEpintu) BPS Kabupaten Tuban. 
                        Sampaikan kendala Anda atau temukan informasi bantuan secara mandiri.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#layanan" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Mulai Sekarang</a>
                        <a href="#kms" class="btn btn-outline-primary rounded-pill px-5 py-3 fw-bold">Cari Panduan</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <img src="https://illustrations.popsy.co/blue/manager.svg" alt="Hero Illustration" class="img-fluid animate-up">
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section class="padding-y" id="layanan" style="padding: 100px 0;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-tag">Layanan Kami</span>
                <h2 class="section-title">Apa yang Anda Butuhkan?</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div>
                            <div class="icon-box bg-ticket">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Lapor Kendala</h4>
                            <p class="text-muted">Laporkan masalah teknis atau pertanyaan Anda kepada tim kami untuk penanganan cepat.</p>
                        </div>
                        <a href="{{ route('ticket.public.create') }}" class="btn btn-action btn-ticket-action">Buat Tiket Baru</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div>
                            <div class="icon-box bg-kms">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4 class="fw-bold mb-3">E-Library (KMS)</h4>
                            <p class="text-muted">Pelajari panduan penggunaan aplikasi dan solusi mandiri melalui basis pengetahuan kami.</p>
                        </div>
                        <a href="{{ route('kms.public.index') }}" class="btn btn-action btn-kms-action">Buka Knowledge Base</a>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div>
                            <div class="icon-box" style="background: #fff8eb; color: #f59e0b;">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4 class="fw-bold mb-3">Lacak Tiket</h4>
                            <p class="text-muted">Sudah punya laporan? Pantau perkembangan tiket Anda cukup dengan memasukkan Tracking ID.</p>
                        </div>
                        <a href="{{ route('ticket.public.track.form') }}" class="btn btn-action" style="background: #f59e0b; color: white; border: none;">Cek Progres Laporan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KMS Preview Section -->
    <section class="kms-section" id="kms">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <span class="section-tag">Knowledge Base</span>
                    <h2 class="fw-bold mb-0">Informasi & Panduan Terbaru</h2>
                </div>
                <a href="{{ route('kms.public.index') }}" class="btn btn-link text-primary fw-bold text-decoration-none">
                    Lihat Semua <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            
            <div class="row g-4">
                @forelse($latestArticles as $article)
                <div class="col-md-6 col-lg-3">
                    <div class="card article-card h-100 border-0">
                        <div class="card-body p-4">
                            <small class="text-primary fw-bold mb-2 d-block">{{ $article->category->name }}</small>
                            <h6 class="fw-bold mb-3">{{ $article->title }}</h6>
                            <p class="text-muted small mb-4">{{ Str::limit(strip_tags($article->content), 80) }}</p>
                            <a href="{{ route('kms.public.show', $article->slug) }}" class="btn btn-link text-primary p-0 small text-decoration-none fw-bold">
                                Baca Detail <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center text-muted">
                    Belum ada informasi publik saat ini.
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="footer-logo d-flex align-items-center">
                        <img src="{{ asset('img/logo_harmoni.png') }}" alt="Logo">
                        <div class="ms-3">
                            <h4 class="text-white fw-bold mb-0">SEpintu</h4>
                            <p class="small mb-0">BPS Kabupaten Tuban</p>
                        </div>
                    </div>
                    <p class="mt-4" style="max-width: 400px;">
                        Ekosistem pelayanan digital terintegrasi untuk mendukung keterbukaan informasi dan efisiensi birokrasi di lingkungan Badan Pusat Statistik Kabupaten Tuban.
                    </p>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <h5 class="footer-title">Tautan Cepat</h5>
                    <a href="#" class="footer-link">Beranda</a>
                    <a href="#layanan" class="footer-link">Layanan Publik</a>
                    <a href="{{ route('kms.public.index') }}" class="footer-link">E-Library</a>
                    <a href="{{ route('login') }}" class="footer-link text-primary fw-bold">Login Internal</a>
                </div>
                <div class="col-lg-3">
                    <h5 class="footer-title">Hubungi Kami</h5>
                    <p class="small"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Jalan Raya Manunggal No. 8 Sukolilo, Panyuran, Kec. Tuban, Kabupaten Tuban, Jawa Timur 62318</p>
                    <p class="small"><i class="fas fa-envelope me-2 text-primary"></i> bps3523@bps.go.id</p>
                    <p class="small"><i class="fas fa-phone me-2 text-primary"></i> 085755461223</p>
                </div>
            </div>
            <div class="text-center mt-5 pt-4 border-top border-secondary opacity-50">
                <p class="small mb-0">© 2026 Badan Pusat Statistik Kabupaten Tuban. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WA Trigger -->
    <button type="button" class="floating-wa shadow-lg border-0" data-bs-toggle="modal" data-bs-target="#waModal">
        <i class="fab fa-whatsapp me-2"></i> Chat WA Cepat
    </button>

    <!-- WA Modal -->
    <div class="modal fade" id="waModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold"><i class="fab fa-whatsapp me-2"></i> Konsultasi Cepat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 p-md-5">
                    <p class="text-muted mb-4 small fw-bold text-uppercase tracking-widest">Pilih kategori bantuan agar kami dapat membantu Anda lebih cepat.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Anda</label>
                        <input type="text" id="wa_name" class="form-control rounded-4 p-3 bg-light border-0" placeholder="Masukkan nama...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Kategori Bantuan</label>
                        <select id="wa_category" class="form-select rounded-4 p-3 bg-light border-0">
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="Rekrutmen SE">Rekrutmen SE</option>
                            <option value="Lapangan SE">Lapangan SE</option>
                            <option value="Aplikasi FASIH">Aplikasi FASIH</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <button type="button" onclick="startWaChat()" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase shadow-sm">
                        Mulai Chat Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .floating-wa {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #25d366;
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            z-index: 9999;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        .floating-wa:hover {
            background: #128c7e;
            color: white;
            transform: scale(1.05);
        }
        .modal-content {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function startWaChat() {
            const name = document.getElementById('wa_name').value || 'User';
            const category = document.getElementById('wa_category').value;
            
            if (!category) {
                alert('Silakan pilih kategori terlebih dahulu.');
                return;
            }

            const text = `Halo Call Center BPS Tuban, saya ${name} ingin berkonsultasi mengenai *${category}*.`;
            const encodedText = encodeURIComponent(text);
            window.open(`https://wa.me/6285755461223?text=${encodedText}`, '_blank');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('waModal'));
            modal.hide();
        }

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').style.padding = '0.7rem 0';
                document.querySelector('.navbar').style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
            } else {
                document.querySelector('.navbar').style.padding = '1rem 0';
                document.querySelector('.navbar').style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>
