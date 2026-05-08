@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h3 class="fw-bold text-dark mb-1">Realisasi & Pembayaran Honor</h3>
        <p class="text-muted small">Konfirmasi pembayaran mitra yang sudah ditransfer ke rekening masing-masing.</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('honorarium.bulk-confirm') }}" method="POST" id="bulkConfirmForm">
        @csrf
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label small fw-bold text-muted ms-1" for="selectAll">PILIH SEMUA</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fas fa-money-check-alt me-2"></i>Konfirmasi Lunas Terpilih
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 40px;"></th>
                            <th class="text-muted small fw-bold py-3">MITRA & REKENING</th>
                            <th class="text-muted small fw-bold py-3 text-center">BULAN BAYAR</th>
                            <th class="text-muted small fw-bold py-3 text-center">NOMINAL</th>
                            <th class="text-muted small fw-bold py-3">STATUS AKTIVITAS</th>
                            <th class="pe-4 text-muted small fw-bold py-3 text-end">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $pay)
                        @php
                            $isReady = ($pay->penugasan->status_tugas == 'Selesai' && $pay->penugasan->status_dokumen == 'Lengkap');
                        @endphp
                        <tr class="{{ !$isReady ? 'bg-light opacity-75' : '' }}">
                            <td class="ps-4">
                                @if($isReady)
                                    <input class="form-check-input payment-checkbox" type="checkbox" name="ids[]" value="{{ $pay->id }}" 
                                           data-over-sbml="{{ $pay->nominal_cair > ($pay->penugasan->mitra->max_honor_bulanan ?? 3200000) ? 'true' : 'false' }}"
                                           data-mitra-name="{{ $pay->penugasan->mitra->nama_lengkap }}">
                                @else
                                    <i class="fas fa-lock text-muted small" title="Syarat Belum Terpenuhi: Tugas harus 'Selesai' dan Dokumen harus 'Lengkap'"></i>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pay->penugasan->mitra->nama_lengkap }}</div>
                                <div class="small text-muted font-monospace text-uppercase">
                                    Mitra SOBAT: {{ $pay->penugasan->mitra_id }}
                                </div>
                                <div class="small text-primary fw-bold" style="font-size: 0.65rem;">
                                    <i class="fas fa-tag me-1"></i> {{ $pay->penugasan->nama_kegiatan_full }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2">
                                    {{ Carbon\Carbon::parse($pay->bulan_bayar)->format('F Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <h6 class="fw-bold mb-0 text-primary">Rp {{ number_format($pay->nominal_cair, 0, ',', '.') }}</h6>
                                @if($pay->nominal_cair > ($pay->penugasan->mitra->max_honor_bulanan ?? 3200000))
                                    <span class="badge bg-danger rounded-pill mt-1" style="font-size: 0.55rem;">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Over SBML
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($pay->penugasan->status_tugas == 'Selesai')
                                    <span class="text-success small d-block"><i class="fas fa-check-double me-1"></i> Tugas Selesai</span>
                                @else
                                    <span class="text-warning small d-block"><i class="fas fa-clock me-1"></i> Tugas Progres</span>
                                @endif

                                @if($pay->penugasan->status_dokumen == 'Lengkap')
                                    <span class="text-success small d-block"><i class="fas fa-file-signature me-1"></i> Dokumen OK</span>
                                @else
                                    <span class="text-danger small d-block"><i class="fas fa-file-excel me-1"></i> Dokumen Pending</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <small class="text-muted italic">{{ $pay->keterangan }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-coins fs-1 d-block mb-3 opacity-25"></i>
                                Tidak ada antrean pembayaran saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

$('#bulkConfirmForm').on('submit', function(e) {
    e.preventDefault();
    const checked = $('.payment-checkbox:checked');
    if (checked.length === 0) {
        Swal.fire('Oops!', 'Pilih minimal satu data.', 'warning');
        return;
    }

    let overSBML = [];
    checked.each(function() {
        if ($(this).data('over-sbml') === true) {
            overSBML.push($(this).data('mitra-name'));
        }
    });

    const proceed = () => {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: `Konfirmasi Lunas untuk ${checked.length} data yang dipilih?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0058a8',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    };

    if (overSBML.length > 0) {
        Swal.fire({
            title: 'Peringatan SBML!',
            html: `Beberapa pembayaran berikut melebihi SBML (Rp 3.200.000):<br><br>
                   <ul class="text-start small text-danger">
                       ${[...new Set(overSBML)].map(name => `<li>${name}</li>`).join('')}
                   </ul>
                   <p class="mb-0 small text-muted">Apakah Anda tetap ingin mengonfirmasi pembayaran ini?</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Tetap Konfirmasi',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                proceed();
            }
        });
    } else {
        proceed();
    }
});
</script>
@endsection
