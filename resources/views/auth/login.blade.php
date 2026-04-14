<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | HARMONI BPS Tuban</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
</head>
<body>

<div class="bg-decoration" style="top: -10%; right: -5%;"></div>
<div class="bg-decoration" style="bottom: -10%; left: -5%;"></div>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-logo-wrapper">
            <img src="{{ asset('img/logo_harmoni.png') }}" alt="Logo Harmoni" class="login-logo">
        </div>
        
        <h3 class="brand-title mb-1">HARMONI <span style="font-weight: 400; color: var(--bps-blue);">BPS</span></h3>
        <p class="text-muted small fw-medium">Harian Monitoring Instansi BPS Kabupaten Tuban</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 small mb-4 shadow-sm py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-circle-exclamation me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('login.action') }}" method="POST">
        @csrf <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-user-circle"></i>
                </span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-shield-halved"></i>
                </span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            Masuk ke Dashboard <i class="fas fa-arrow-right ms-2 small"></i>
        </button>
    </form>
    
    <div class="text-center footer-text">
        <p class="mb-0 text-uppercase">© 2026 BPS KABUPATEN TUBAN</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>