@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    @php 
        $isEdit = !empty($meeting->notulensi_hasil); 
    @endphp

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4 mt-4">
        <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning">
            <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-file-signature' }} fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0">{{ $isEdit ? 'Edit Hasil Rapat' : 'Input Hasil Rapat (Upload File)' }}</h4>
            <p class="text-muted small mb-0">Unggah dokumen notulensi, materi pendukung, dan foto dokumentasi.</p>
        </div>
    </div>

    <form id="formNotulensi" action="{{ $isEdit ? route('meeting.notulensi.update', $meeting->id) : route('meeting.notulensi.store', $meeting->id) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row">
            {{-- KIRI: UPLOAD NOTULENSI UTAMA --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-file-invoice text-primary opacity-25" style="font-size: 5rem;"></i>
                        </div>
                        <h5 class="fw-bold text-dark">Dokumen Notulensi Rapat</h5>
                        <p class="text-muted small px-md-5">Unggah file hasil rapat yang telah disusun. Sistem menerima format PDF, Word, atau Text.</p>
                        
                        <div class="mx-auto" style="max-width: 400px;">
                            <input type="file" name="hasil_rapat_file" id="hasil_rapat_file" 
                                   class="form-control rounded-pill border-primary border-dashed p-3 @error('hasil_rapat_file') is-invalid @enderror" 
                                   accept=".pdf,.doc,.docx,.txt" {{ $isEdit ? '' : 'required' }}>
                            @error('hasil_rapat_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

<<<<<<< Updated upstream
                        @if($isEdit && $meeting->notulensi_hasil)
                            <div class="mt-4 p-3 bg-light rounded-4 d-inline-flex align-items-center border">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small fw-bold text-muted">File Notulensi Tersimpan: </span>
                                <a href="{{ asset('storage/' . $meeting->notulensi_hasil) }}" target="_blank" class="ms-2 badge bg-primary text-decoration-none">Lihat Dokumen</a>
=======
                        {{-- TAMPILAN FILE NOTULENSI LAMA --}}
                        @if($isEdit && $meeting->notulensi_hasil)
                            <div class="mt-4 p-3 bg-light rounded-4 d-inline-flex align-items-center border">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="small fw-bold text-muted">File Tersimpan: </span>
                                <a href="{{ asset('storage/' . $meeting->notulensi_hasil) }}" target="_blank" class="ms-2 badge bg-primary text-decoration-none">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Dokumen
                                </a>
>>>>>>> Stashed changes
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <small class="text-muted fw-bold">Batas ukuran file: 20MB</small>
                        </div>
                    </div>
                </div>

                {{-- INFO PESERTA --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-success">
                    <div class="card-body p-4 text-dark">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 small text-uppercase" style="letter-spacing: 1px;">Status Kehadiran Peserta</h6>
                            <span class="badge bg-success rounded-pill px-3">{{ count($userSudahHadir) }} Hadir</span>
                        </div>
                        <div class="row g-2">
                            @foreach($semuaPeserta as $p)
                                @php $isHadir = in_array($p->assigned_to, $userSudahHadir); @endphp
                                <div class="col-md-4">
                                    <div class="p-2 border rounded-3 d-flex align-items-center {{ $isHadir ? 'bg-white' : 'bg-light opacity-50' }}">
                                        <i class="fas {{ $isHadir ? 'fa-check-circle text-success' : 'fa-clock text-muted' }} me-2"></i>
                                        <span class="small text-truncate fw-bold" title="{{ $p->assignee->nama_lengkap }}">{{ $p->assignee->nama_lengkap }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: DOKUMEN LAIN --}}
            <div class="col-lg-4">
                {{-- MATERI --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-dark">
                        <h6 class="fw-bold mb-3"><i class="fas fa-file-powerpoint me-2 text-warning"></i>Materi Rapat</h6>
                        <input type="file" name="materi_path" id="materi_path" class="form-control rounded-3" accept=".pdf,.pptx,.ppt">
<<<<<<< Updated upstream
=======
                        
                        {{-- TAMPILAN MATERI LAMA --}}
                        @if($isEdit && $meeting->materi_path)
                            <div class="mt-2 p-2 bg-light rounded-3 border">
                                <small class="d-block text-muted mb-1" style="font-size: 0.7rem;">Materi Terunggah:</small>
                                <a href="{{ asset('storage/' . $meeting->materi_path) }}" target="_blank" class="text-decoration-none small fw-bold">
                                    <i class="fas fa-file-download me-1"></i> Download Materi
                                </a>
                            </div>
                        @endif
>>>>>>> Stashed changes
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">Max: 20MB (PDF/PPTX)</small>
                    </div>
                </div>

                {{-- FOTO DOKUMENTASI --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 text-dark">
                        <h6 class="fw-bold mb-3"><i class="fas fa-images me-2 text-danger"></i>Foto Dokumentasi</h6>
                        <input type="file" name="foto_dokumentasi[]" id="foto_dokumentasi" class="form-control rounded-3" accept="image/*" multiple {{ $isEdit ? '' : 'required' }}>
<<<<<<< Updated upstream
                        <div id="image-preview-container" class="row g-2 mt-2"></div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.65rem;">Maksimal 20MB per foto.</small>
=======
                        
                        <div id="image-preview-container" class="row g-2 mt-2">
                            {{-- TAMPILAN FOTO LAMA (KHUSUS EDIT) --}}
                            @if($isEdit && $meeting->dokumentasi_path)
                                @php $fotos = json_decode($meeting->dokumentasi_path); @endphp
                                @if(is_array($fotos))
                                    @foreach($fotos as $foto)
                                        <div class="col-3 existing-photo">
                                            <div class="preview-img-wrapper border-success border-opacity-25">
                                                <img src="{{ asset('storage/' . $foto) }}" alt="Existing photo">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 0.65rem;">Maksimal 20MB per foto. @if($isEdit) <span class="text-warning">Upload baru akan mengganti foto lama.</span> @endif</small>
>>>>>>> Stashed changes
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg border-0 mb-3">
                    <i class="fas {{ $isEdit ? 'fa-save' : 'fa-cloud-upload-alt' }} me-2"></i> 
                    {{ $isEdit ? 'Simpan Perubahan' : 'Upload Notulensi' }}
                </button>

<<<<<<< Updated upstream
                {{-- TOMBOL BATAL (DINAMIS) --}}
                <a href="{{ $isEdit ? route('meeting.history') : route('meeting.index') }}" 
                class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold">
=======
                {{-- TOMBOL BATAL --}}
                <a href="{{ $isEdit ? route('meeting.history') : route('meeting.index') }}" 
                class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold border">
>>>>>>> Stashed changes
                    <i class="fas fa-times me-1"></i> Batal
                </a>
            </div>
        </div>
    </form>
</div>

<style>
<<<<<<< Updated upstream
    .preview-img-wrapper { height: 70px; width: 100%; overflow: hidden; border-radius: 10px; border: 2px solid #f1f5f9; }
=======
    .preview-img-wrapper { height: 70px; width: 100%; overflow: hidden; border-radius: 10px; border: 2px solid #f1f5f9; background: #eee; }
>>>>>>> Stashed changes
    .preview-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    .btn-primary { background: linear-gradient(135deg, #0058a8 0%, #007bff 100%); transition: all 0.3s ease; }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0, 88, 168, 0.3) !important; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const form = document.querySelector('#formNotulensi');
    
    // Fungsi Validasi Ukuran File (Sekarang Global 20MB)
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
                title: 'File ' + typeName + ' Kebesaran',
                html: `Batas maksimal adalah ${maxSizeMB}MB.<br><small class="text-danger">${oversized.join(', ')}</small>`,
                confirmButtonColor: '#0058a8'
            });
            input.value = ''; // Reset
            return false;
        }
        return true;
    }

    // Listener Notulensi Utama (Diubah ke 20MB)
    document.querySelector('#hasil_rapat_file').addEventListener('change', function() {
        validateSize(this, 20, 'Notulensi');
    });

    // Listener Materi (20MB)
    document.querySelector('#materi_path').addEventListener('change', function() {
        validateSize(this, 20, 'Materi');
    });

    // Listener Foto Dokumentasi (Diubah ke 20MB) & Preview
    document.querySelector('#foto_dokumentasi').addEventListener('change', function() {
        if(validateSize(this, 20, 'Foto')) {
            const previewContainer = document.querySelector('#image-preview-container');
            previewContainer.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const col = document.createElement('div');
                    col.className = 'col-3';
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
                title: 'Mengunggah Laporan...',
                text: 'Mohon tunggu sebentar, file sedang diproses.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
        }
    });
</script>
@endsection