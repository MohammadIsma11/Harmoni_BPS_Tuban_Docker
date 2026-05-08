@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/history-edit.css') }}">

<div class="container-fluid">
    @php
        // 1. Pecah string Lokasi dari Report
        $currentLocation = $report->lokasi_tujuan;
        $currentDesa = '';
        $currentKec = '';
        if (str_contains($currentLocation, ', Kec. ')) {
            $parts = explode(', Kec. ', $currentLocation);
            $currentKec = trim($parts[1] ?? '');
            $currentDesa = trim(str_replace('Desa ', '', $parts[0] ?? ''));
        }

        // 2. LOGIKA TANGGAL PELAKSANAAN
        $valTanggal = \Carbon\Carbon::parse($report->tanggal_lapor)->format('Y-m-d');

        // 3. DATA VALIDASI UNTUK JS
        $userCuti = \App\Models\Absensi::where('user_id', $agenda->assigned_to)
                ->whereIn('status', ['CT', 'CST1']) 
                ->get(['start_date', 'end_date', 'status']);

        $laporanTerpakai = \App\Models\Agenda::where('assigned_to', $agenda->assigned_to)
                ->where('id', '!=', $agenda->id) 
                ->whereNotNull('tanggal_pelaksanaan')
                ->where('status_laporan', 'Selesai')
                ->pluck('tanggal_pelaksanaan')
                ->toArray();
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-11 mt-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-warning p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-edit fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Perbarui Laporan Translok</h5>
                            <small class="text-white text-opacity-75">Sesuaikan data laporan translok yang sudah dikirim</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-warning rounded-pill px-3 shadow-sm fw-bold">MODE EDIT</span>
                </div>
            </div>

            <form id="formLaporan" action="{{ route('history.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Perbarui Lokasi</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan <span class="text-danger">*</span></label>
                                <select name="kecamatan" id="kecamatan" class="form-select rounded-3 border-0 bg-light p-3 fw-bold" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @foreach(["BANCAR", "BANGILAN", "GRABAGAN", "JATIROGO", "JENU", "KENDURUAN", "KEREK", "MERAKURAK", "MONTONG", "PALANG", "PARENGAN", "PLUMPANG", "RENGEL", "SEMANDING", "SENORI", "SINGGAHAN", "SOKO", "TAMBAKBOYO", "TUBAN", "WIDANG"] as $kec)
                                        <option value="{{ $kec }}" {{ (old('kecamatan', $currentKec) == $kec) ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Desa / Kelurahan <span class="text-danger">*</span></label>
                                <select name="desa" id="desa" class="form-select rounded-3 border-0 bg-light p-3 fw-bold" required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-warning">
                            <h6 class="fw-bold mb-3 text-warning"><i class="fas fa-calendar-check me-2"></i>Waktu Pelaksanaan</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase">Tanggal Pelaksanaan Lapangan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pelaksanaan" id="tanggal_pelaksanaan" class="form-control rounded-3 shadow-sm border-warning fw-bold" 
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" 
                                       value="{{ $valTanggal }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark text-uppercase">Ganti Foto Dokumentasi</label>
                                <input type="file" name="fotos[]" id="foto_upload" class="form-control" accept="image/*" multiple>
                                <div class="form-text text-danger fw-bold" style="font-size: 0.65rem;">
                                    * Upload foto baru akan mengganti semua foto lama.
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-3 p-2 bg-light rounded-3 border border-dashed">
                                    @forelse($agenda->photos as $photo)
                                        <div class="position-relative border rounded-2 overflow-hidden shadow-sm" style="width: 55px; height: 55px;">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @empty
                                        <small class="text-muted">Tidak ada foto lama.</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2 text-dark"><i class="fas fa-clipboard-check me-2 text-success"></i>Detail Hasil Pengawasan</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">RESPONDEN / PETUGAS DITEMUI <span class="text-danger">*</span></label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3" required value="{{ old('responden', $details['responden'] ?? ($agenda->responden ?? '')) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS DILAKUKAN <span class="text-danger">*</span></label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3" rows="6" required>{{ old('aktivitas', $details['aktivitas'] ?? ($agenda->aktivitas ?? '')) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN LAPANGAN <span class="text-danger">*</span></label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3" rows="3" required>{{ old('permasalahan', $details['permasalahan'] ?? ($agenda->permasalahan ?? '')) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success text-uppercase">Solusi / Tindak Lanjut <span class="text-danger">*</span></label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3" rows="3" required>{{ old('solusi_antisipasi', $details['solusi_antisipasi'] ?? ($agenda->solusi_antisipasi ?? '')) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('history.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">Batal</a>
                                <button type="submit" id="btnSubmit" class="btn btn-warning px-5 rounded-pill fw-bold shadow-lg text-white">
                                    <i class="fas fa-save me-2"></i> Update Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.daftarCuti = @json($userCuti);
    window.laporanTerpakai = @json($laporanTerpakai);
    window.tanggalAwal = "{{ $valTanggal }}";
    window.initialDesa = @json(old('desa', $currentDesa));
</script>
<script src="{{ asset('js/pages/history-edit.js') }}"></script>
@endsection