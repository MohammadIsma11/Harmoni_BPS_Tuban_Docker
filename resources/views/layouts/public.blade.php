<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SEpintu - BPS Kabupaten Tuban' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0058a8;
            --secondary-color: #00aaff;
            --bg-light: #f8fbff;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #2d3436;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }
        .navbar-brand img { height: 40px; }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,88,168,0.05);
        }
        .footer {
            padding: 3rem 0;
            background: white;
            margin-top: 5rem;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('img/logo-bps.png') }}" alt="Logo" class="me-2" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/2/28/Lambang_Badan_Pusat_Statistik_%28BPS%29_Indonesia.svg'">
                <div class="lh-1">
                    <div class="fw-bold text-primary fs-5">SEpintu</div>
                    <small class="text-muted" style="font-size: 0.7rem;">BPS KABUPATEN TUBAN</small>
                </div>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link px-3 fw-medium" href="{{ route('ticket.public.create') }}">Buat Tiket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 fw-medium" href="{{ route('ticket.public.index') }}">Tiket Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 fw-medium" href="{{ route('ticket.public.track.form') }}">Lacak Tiket</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 fw-medium" href="{{ route('kms.public.index') }}">Knowledge Base</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Pegawai
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-5">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0 text-muted small">&copy; {{ date('Y') }} Badan Pusat Statistik Kabupaten Tuban. Hak Cipta Dilindungi.</p>
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
                        <input type="text" id="wa_name_global" class="form-control rounded-4 p-3 bg-light border-0" placeholder="Masukkan nama...">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Kategori Bantuan</label>
                        <select id="wa_category_global" class="form-select rounded-4 p-3 bg-light border-0">
                            <option value="" disabled selected>Pilih Kategori</option>
                            <option value="Rekrutmen SE">Rekrutmen SE</option>
                            <option value="Lapangan SE">Lapangan SE</option>
                            <option value="Aplikasi FASIH">Aplikasi FASIH</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <button type="button" onclick="startGlobalWaChat()" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase shadow-sm">
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function startGlobalWaChat() {
            const name = document.getElementById('wa_name_global').value || 'User';
            const category = document.getElementById('wa_category_global').value;
            
            if (!category) {
                alert('Silakan pilih kategori terlebih dahulu.');
                return;
            }

            const text = `Halo Call Center BPS Tuban, saya ${name} ingin berkonsultasi mengenai *${category}*.`;
            const encodedText = encodeURIComponent(text);
            window.open(`https://wa.me/6285755461223?text=${encodedText}`, '_blank');
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('waModal'));
            modal.hide();
        }
    </script>
    @yield('scripts')
</body>
</html>
