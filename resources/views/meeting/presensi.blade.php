@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/meeting-presensi.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-presensi">
                <div class="header-presensi">
                    <a href="{{ route('meeting.index') }}" class="btn btn-link text-white text-decoration-none p-0 mb-3 small d-flex align-items-center justify-content-center">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Rapat
                    </a>
                    <i class="fas fa-file-signature fa-3x mb-3"></i>
                    <h3 class="fw-bold mb-0">Daftar Hadir Digital</h3>
                    <p class="small opacity-75 mb-0">BPS Kabupaten Tuban</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(session('success'))
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                            </div>
                            <h4 class="fw-bold">Berhasil!</h4>
                            <p class="text-muted">{{ session('success') }}</p>
                            <p class="small text-muted mt-3">Mengalihkan ke halaman agenda dalam <span id="timer">3</span> detik...</p>
                            <a href="{{ route('meeting.index') }}" class="btn btn-primary rounded-pill px-5">Kembali Sekarang</a>
                        </div>
                    @elseif($alreadySigned)
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-user-check text-primary" style="font-size: 5rem;"></i>
                            </div>
                            <h4 class="fw-bold">Sudah Mengisi</h4>
                            <p class="text-muted">Kehadiran Anda pada rapat <strong>{{ $agenda->title }}</strong> sudah tercatat.</p>
                            <a href="{{ route('meeting.index') }}" class="btn btn-outline-primary rounded-pill px-5">Kembali</a>
                        </div>
                    @else
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1">{{ $agenda->title }}</h5>
                            <p class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i> {{ $agenda->event_date->format('d M Y') }} 
                                <i class="fas fa-clock ms-2 me-1"></i> {{ $agenda->start_time ?? 'WIB' }}
                            </p>
                        </div>

                        <div class="user-info-box">
                            <div class="row small">
                                <div class="col-4 text-muted">Nama Lengkap</div>
                                <div class="col-8 fw-bold text-dark">: {{ auth()->user()->nama_lengkap }}</div>
                                <div class="col-4 text-muted mt-2">NIP</div>
                                <div class="col-8 fw-bold text-dark mt-2">: {{ auth()->user()->nip }}</div>
                            </div>
                        </div>

                        <form action="{{ route('meeting.presensi.store') }}" method="POST" id="signature-form">
                            @csrf
                            <input type="hidden" name="agenda_id" value="{{ $agenda->id }}">
                            <input type="hidden" name="signature" id="signature-value">

                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <label class="small fw-bold text-secondary">Goreskan Tanda Tangan:</label>
                                <button type="button" id="clear-btn" class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                                    <i class="fas fa-eraser me-1"></i> Hapus & Ulangi
                                </button>
                            </div>

                            <div class="signature-wrapper">
                                <canvas id="signature-pad"></canvas>
                            </div>

                            <p class="small text-muted mb-4 text-center">
                                <i class="fas fa-info-circle me-1 text-primary"></i> 
                                Gunakan jari atau stylus untuk menandatangani layar.
                            </p>

                            <button type="submit" class="btn btn-primary btn-simpan w-100 rounded-pill shadow-lg">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Kehadiran
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Library Signature Pad JS --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.showSuccessMessage = @json(session('success') ? true : false);
    window.redirectRoute = @json(route('meeting.index'));
</script>
    <script src="{{ asset('js/pages/meeting-presensi.js') }}"></script>
@endsection