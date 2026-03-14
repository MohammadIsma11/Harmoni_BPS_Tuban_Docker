@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-4 mt-4">
        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success shadow-sm">
            <i class="fas fa-plane-departure fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0 text-dark">Laporan Perjalanan Dinas</h4>
            <p class="text-muted small mb-0 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.65rem;">Dokumentasi & Hasil Kegiatan Luar Kantor</p>
        </div>
    </div>

    <form id="formDinasLuar" action="{{ route('meeting.dinas.store', $meeting->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- SISI KIRI: INPUT UTAMA --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        {{-- Info Agenda --}}
                        <div class="mb-4 p-3 bg-light rounded-4 border-start border-4 border-success">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.6rem;">Nama Kegiatan Dasar:</small>
                            <h5 class="fw-bold text-dark mb-0">{{ $meeting->title }}</h5>
                        </div>

                        {{-- 1. Input Lokasi Spesifik --}}
                        <div class="mb-4">
                            <label class="small fw-bold text-dark mb-2 text-uppercase"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Lokasi Spesifik Kunjungan *</label>
                            <input type="text" name="lokasi_spesifik" class="form-control rounded-3 p-3 bg-light border-0 shadow-inner" 
                                   placeholder="Contoh: Kantor Desa Tambakboyo / Hotel Aston Madiun" required>
                            <small class="text-muted" style="font-size: 0.65rem;">* Masukkan alamat atau nama tempat tujuan dinas luar Anda.</small>
                        </div>

                        {{-- 2. Upload Dokumen Laporan / Materi --}}
                        <div class="col-12 text-center py-5 px-4 bg-light rounded-4 border border-dashed mb-0">
                            <div class="mb-3">
                                <i class="fas fa-file-invoice text-success opacity-25" style="font-size: 4.5rem;"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Upload Laporan / Risalah Kegiatan</h6>
                            <p class="text-muted small px-md-5 mb-4">Unggah file hasil dinas luar (Laporan Perjalanan, Risalah, atau SPD). <br>Sistem menerima format <b>PDF, Word (Docx), atau TXT</b>.</p>
                            
                            <div class="mx-auto" style="max-width: 400px;">
                                <input type="file" name="hasil_rapat_file" id="hasil_rapat_file" 
                                       class="form-control rounded-pill border-success border-dashed p-2" 
                                       accept=".pdf,.doc,.docx,.txt" required>
                            </div>
                            <div class="mt-3">
                                <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3">Batas Ukuran: 20MB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SISI KANAN: DOKUMENTASI & AKSI --}}
            <div class="col-lg-4">
                {{-- 3. Upload Foto Dokumentasi --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-dark text-uppercase small"><i class="fas fa-camera me-2 text-danger"></i>Foto Bukti Kegiatan *</h6>
                        <div class="mb-3">
                            <input type="file" name="foto_dokumentasi[]" id="foto_dokumentasi" 
                                   class="form-control rounded-3" accept="image/*" multiple required>
                        </div>
                        <div id="image-preview-container" class="row g-2">
                            {{-- Preview muncul di sini --}}
                        </div>
                        <div class="mt-3 p-2 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-20">
                            <small class="text-warning-emphasis" style="font-size: 0.65rem; line-height: 1.2; display: block;">
                                <i class="fas fa-info-circle me-1"></i> Unggah minimal 1 foto kegiatan lapangan sebagai bukti kehadiran. (Maksimal 20MB per foto)
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mb-3">
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg border-0 mb-3">
                        <i class="fas fa-cloud-upload-alt me-2"></i> Kirim Laporan Dinas
                    </button>
                    <a href="{{ route('meeting.index') }}" class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold border">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .preview-img-wrapper { height: 75px; width: 100%; overflow: hidden; border-radius: 10px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .preview-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .btn-success { background: linear-gradient(135deg, #15803d 0%, #22c55e 100%); transition: all 0.3s ease; }
    .btn-success:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(21, 128, 61, 0.3) !important; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
</style>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const form = document.querySelector('#formDinasLuar');
    
    // Fungsi Validasi Ukuran File
    function validateSize(input, maxSizeMB, typeName) {
        const files = Array.from(input.files);
        const maxSize = maxSizeMB * 1024 * 1024;
        let oversized = [];

        files.forEach(file => {
            if (file.size > maxSize) oversized.push(file.name);
        });

        if (oversized.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'File ' + typeName + ' Terlalu Besar',
                html: `Batas maksimal adalah ${maxSizeMB}MB.<br><small class="text-danger">${oversized.join(', ')}</small>`,
                confirmButtonColor: '#15803d'
            });
            input.value = ''; // Reset
            return false;
        }
        return true;
    }

    // Listener File Laporan (20MB)
    document.querySelector('#hasil_rapat_file').addEventListener('change', function() {
        validateSize(this, 20, 'Laporan');
    });

    // Listener Foto Dokumentasi (20MB) & Preview
    document.querySelector('#foto_dokumentasi').addEventListener('change', function() {
        if(validateSize(this, 20, 'Foto')) {
            const previewContainer = document.querySelector('#image-preview-container');
            previewContainer.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    col.innerHTML = `<div class="preview-img-wrapper"><img src="${e.target.result}"></div>`;
                    previewContainer.appendChild(col);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Loading saat submit
    form.addEventListener('submit', function() {
        if (this.checkValidity()) {
            Swal.fire({
                title: 'Mengirim Laporan Dinas...',
                text: 'Harap tunggu, data dan file sedang diunggah.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
</script>
@endsection