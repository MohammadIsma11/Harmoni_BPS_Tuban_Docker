@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/task-create.css') }}">

<div class="container-fluid">
    @php
        // 1. Pecah string Lokasi
        $currentLocation = $agenda->location;
        $currentDesa = '';
        $currentKec = '';
        if (str_contains($currentLocation, ', Kec. ')) {
            $parts = explode(', Kec. ', $currentLocation);
            $currentKec = trim($parts[1] ?? '');
            $currentDesa = trim(str_replace('Desa ', '', $parts[0] ?? ''));
        }

        // 2. LOGIKA TANGGAL PELAKSANAAN
        $tanggalTerdeteksi = $agenda->tanggal_pelaksanaan 
                             ?? ($agenda->event_date 
                             ?? now()->format('Y-m-d'));

        $valTanggal = \Carbon\Carbon::parse($tanggalTerdeteksi)->format('Y-m-d');

        // 3. DATA VALIDASI UNTUK JS
        $userCuti = \App\Models\Absensi::where('user_id', Auth::id())
                ->whereIn('status', ['CT', 'CST1']) // Gunakan whereIn untuk menangkap semua jenis cuti
                ->get(['start_date', 'end_date', 'status']);
    
        $laporanTerpakai = \App\Models\Agenda::where('assigned_to', Auth::id())
                ->where('id', '!=', $agenda->id) 
                ->whereNotNull('tanggal_pelaksanaan')
                ->where('status_laporan', 'Selesai')
                ->pluck('tanggal_pelaksanaan')
                ->toArray();
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-11 mt-4">
            {{-- Header --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-bps-blue p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('task.index') }}" class="btn btn-outline-light btn-sm rounded-pill me-3 px-3">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-file-signature fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Input Laporan Pengawasan Lapangan</h5>
                            <small class="text-white text-opacity-75">Silakan lengkapi data hasil temuan Anda di lapangan</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-primary rounded-pill px-3 shadow-sm fw-bold">FORM LAPORAN</span>
                </div>
            </div>

            {{-- Pastikan Route-nya ke TASK STORE bukan HISTORY UPDATE jika ini input baru --}}
            <form id="formLaporan" action="{{ route('task.store', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- SISI KIRI --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 text-muted border-bottom pb-2"><i class="fas fa-lock me-2"></i>Informasi Baku</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Kegiatan</label>
                                <textarea class="form-control border-0 bg-white fw-bold rounded-3" rows="2" readonly style="resize: none;">{{ $agenda->title }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nomor Surat Tugas</label>
                                <input type="text" class="form-control border-0 bg-white fw-bold rounded-3 text-primary" value="{{ $agenda->nomor_surat_tugas ?? '-' }}" readonly>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-primary">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Lokasi Pengawasan</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan <span class="text-danger">*</span></label>
                                <select name="kecamatan" id="kecamatan" class="form-select rounded-3 border-0 bg-light p-3 fw-bold shadow-sm" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach(["BANCAR", "BANGILAN", "GRABAGAN", "JATIROGO", "JENU", "KENDURUAN", "KEREK", "MERAKURAK", "MONTONG", "PALANG", "PARENGAN", "PLUMPANG", "RENGEL", "SEMANDING", "SENORI", "SINGGAHAN", "SOKO", "TAMBAKBOYO", "TUBAN", "WIDANG"] as $kec)
                                        <option value="{{ $kec }}" {{ (old('kecamatan', $currentKec) == $kec) ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Desa / Kelurahan <span class="text-danger">*</span></label>
                                <select name="desa" id="desa" class="form-select rounded-3 border-0 bg-light p-3 fw-bold shadow-sm" required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-info">
                            <h6 class="fw-bold mb-3 text-info"><i class="fas fa-calendar-check me-2"></i>Waktu & Foto</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase">Tanggal Pelaksanaan Lapangan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" class="form-control rounded-3 shadow-sm border-info fw-bold" 
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" 
                                       value="{{ $valTanggal }}" required>
                            </div>
                            <div class="mb-0">
                                {{-- BINTANG MERAH DITAMBAHKAN DISINI --}}
                                <label class="form-label small fw-bold text-dark text-uppercase">Foto Dokumentasi <span class="text-danger">*</span></label>
                                <input type="file" name="fotos[]" id="foto_upload" class="form-control" accept="image/*" multiple required>
                                <div class="form-text text-danger fw-bold" style="font-size: 0.65rem;">
                                    <i class="fas fa-info-circle me-1"></i> Format: JPG/PNG. Maksimal 10MB per foto.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2 text-dark"><i class="fas fa-clipboard-check me-2 text-success"></i>Detail Hasil Pengawasan</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">RESPONDEN / PETUGAS DITEMUI <span class="text-danger">*</span></label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" placeholder="Contoh: Bapak Ahmad" required value="{{ old('responden', $agenda->responden) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS DILAKUKAN <span class="text-danger">*</span></label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" rows="6" placeholder="Jelaskan aktivitas Anda..." required>{{ old('aktivitas', $agenda->aktivitas) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN LAPANGAN <span class="text-danger">*</span></label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" rows="3" placeholder="Kendala di lapangan..." required>{{ old('permasalahan', $agenda->permasalahan) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success text-uppercase">Solusi / Tindak Lanjut <span class="text-danger">*</span></label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3 shadow-sm" rows="3" placeholder="Saran/Tindak lanjut..." required>{{ old('solusi_antisipasi', $agenda->solusi_antisipasi) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('task.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">Batal</a>
                                <button type="submit" class="btn btn-bps-primary px-5 rounded-pill fw-bold shadow-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.userCuti = @json($userCuti);
    window.laporanTerpakai = @json($laporanTerpakai);
    window.initialDesa = @json(old('desa', $currentDesa));
</script>
    <script src="{{ asset('js/pages/task-create.js') }}"></script>
@endsection