@extends('layouts.app')

@section('content')
<div class="row mb-4 animate-up">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-primary text-white position-relative">
            <div class="card-body p-4 p-md-5 d-flex align-items-center position-relative" style="z-index: 2;">
                <div class="me-4 d-none d-md-block">
                    <div class="bg-white p-3 rounded-circle shadow-lg" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hand-holding-usd text-primary fs-1"></i>
                    </div>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Halo, {{ $user->nama_lengkap }}!</h2>
                    <p class="mb-0 opacity-75">Selamat datang di portal honorarium Mitra BPS Kabupaten Tuban. Pantau penugasan dan pembayaran Anda di sini.</p>
                </div>
            </div>
            {{-- Subtle decoration --}}
            <div class="position-absolute end-0 top-0 h-100 p-5 opacity-10 d-none d-lg-block">
                <i class="fas fa-coins" style="font-size: 150px; transform: rotate(-15deg);"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4 animate-up" style="animation-delay: 0.1s;">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
            <div class="text-muted small fw-bold mb-1">Total Pendapatan</div>
            <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($stats['total_honor'], 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
            <div class="text-muted small fw-bold mb-1 text-success">Sudah Diterima</div>
            <h4 class="fw-bold text-success mb-0">Rp {{ number_format($stats['lunas'], 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
            <div class="text-muted small fw-bold mb-1 text-warning">Menunggu (Antre)</div>
            <h4 class="fw-bold text-warning mb-0">Rp {{ number_format($stats['antre'], 0, ',', '.') }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
            <div class="text-muted small fw-bold mb-1">Total Penugasan</div>
            <h4 class="fw-bold text-dark mb-0">{{ $stats['penugasan'] }} Tugas</h4>
        </div>
    </div>
</div>

{{-- Honor Matrix Section (Adaptation from Malowopati) --}}
<div class="row mb-4 animate-up" style="animation-delay: 0.15s;">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex justify-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fas fa-calendar-alt text-primary me-2"></i>Matrix Honor Tahunan ({{ $currentYear }})</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Ringkasan Pendapatan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered border-light text-center mb-0" style="min-width: 800px;">
                        <thead class="bg-light small text-uppercase">
                            <tr>
                                @foreach($honorMatrix as $month => $total)
                                <th class="py-2">{{ substr($month, 0, 3) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($honorMatrix as $month => $total)
                                <td class="py-3">
                                    <div class="small fw-bold {{ $total > 3200000 ? 'text-danger' : ($total > 0 ? 'text-dark' : 'text-muted opacity-50') }}">
                                        {{ $total > 0 ? number_format($total/1000, 0, ',', '.') . 'k' : '-' }}
                                    </div>
                                    @if($total > 3200000)
                                        <div style="font-size: 0.5rem;" class="text-danger fw-bold text-uppercase mt-1">Max</div>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up" style="animation-delay: 0.2s;">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Detail Pembayaran & Penugasan</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">Kegiatan</th>
                                <th class="border-0">Bulan Bayar</th>
                                <th class="border-0">Nominal</th>
                                <th class="border-0 text-center">Status Pembayaran</th>
                                <th class="border-0 text-center">Status Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pembayaran as $pay)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $pay->penugasan->nama_kegiatan_full }}</div>
                                    <small class="text-muted">Target Selesai: {{ \Carbon\Carbon::parse($pay->penugasan->tgl_selesai_target)->translatedFormat('d F Y') }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $pay->bulan_bayar }}</span>
                                </td>
                                <td>
                                    <span class="text-primary fw-bold">Rp {{ number_format($pay->nominal_cair, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($pay->status_bayar == 'Lunas')
                                        <span class="badge bg-success rounded-pill px-3 py-2" style="font-size: 0.7rem;">
                                            <i class="fas fa-check-circle me-1"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2" style="font-size: 0.7rem;">
                                            <i class="fas fa-clock me-1"></i> Menunggu Transfer
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php $sd = $pay->penugasan->status_dokumen; @endphp
                                    <span class="badge {{ $sd == 'Lengkap' ? 'bg-info' : ($sd == 'Revisi' ? 'bg-danger' : 'bg-light text-muted') }} rounded-pill" style="font-size: 0.6rem;">
                                        {{ $sd }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="mb-2">
                                        <i class="fas fa-file-invoice fs-1 opacity-25"></i>
                                    </div>
                                    Belum ada catatan penugasan atau pembayaran untuk Anda.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-up {
        animation: fadeInUp 0.6s ease-out backwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
