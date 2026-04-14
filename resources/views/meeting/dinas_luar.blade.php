@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    @php 
        // Logika untuk menentukan mode Edit atau Input Baru
        $isEdit = !empty($meeting->notulensi_hasil); 
    @endphp

    {{-- Header --}}
    <div class="d-flex align-items-center mb-4 mt-4">
        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success shadow-sm">
            <i class="fas {{ $isEdit ? 'fa-edit' : 'fa-plane-departure' }} fa-lg"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-0 text-dark">{{ $isEdit ? 'Edit Laporan Perjalanan Dinas' : 'Laporan Perjalanan Dinas' }}</h4>
            <p class="text-muted small mb-0 text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.65rem;">Dokumentasi & Hasil Kegiatan Luar Kantor</p>
        </div>
    </div>

    {{-- Route otomatis pilih Update jika sudah ada data, pilih Store jika baru --}}
<form id="formDinasLuar" 
      action="{{ $isEdit ? route('meeting.dinas.update', $meeting->id) : route('meeting.dinas.store', $meeting->id) }}" 
      method="POST" 
      enctype="multipart/form-data">
    @csrf
    
    {{-- WAJIB: Tambahkan method PUT jika sedang dalam mode Edit --}}
    @if($isEdit)
        @method('PUT')
    @endif
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
                                   placeholder="Contoh: Kantor Desa Tambakboyo / Hotel Aston Madiun" 
                                   value="{{ old('lokasi_spesifik', $meeting->location) }}" required>
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
                                       accept=".pdf,.doc,.docx,.txt" {{ $isEdit ? '' : 'required' }}>
                            </div>

                            {{-- TAMPILAN FILE LAMA (KHUSUS EDIT) --}}
                            @if($isEdit && $meeting->notulensi_hasil)
                                <div class="mt-4 p-3 bg-white rounded-4 d-inline-flex align-items-center border shadow-sm">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span class="small fw-bold text-muted">File Tersimpan: </span>
                                    <a href="{{ asset('storage/' . $meeting->notulensi_hasil) }}" target="_blank" class="ms-2 badge bg-success text-decoration-none">
                                        <i class="fas fa-external-link-alt me-1"></i> Lihat Laporan
                                    </a>
                                </div>
                            @endif

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
                                   class="form-control rounded-3" accept="image/*" multiple {{ $isEdit ? '' : 'required' }}>
                        </div>
                        
                        <div id="image-preview-container" class="row g-2">
                            {{-- PREVIEW FOTO LAMA (KHUSUS EDIT) --}}
                            @if($isEdit && $meeting->dokumentasi_path)
                                @php $fotos = json_decode($meeting->dokumentasi_path); @endphp
                                @if(is_array($fotos))
                                    @foreach($fotos as $foto)
                                        <div class="col-4 existing-photo">
                                            <div class="preview-img-wrapper border-success border-opacity-25">
                                                <img src="{{ asset('storage/' . $foto) }}" alt="Foto Lama">
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>

                        <div class="mt-3 p-2 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-20">
                            <small class="text-warning-emphasis" style="font-size: 0.65rem; line-height: 1.2; display: block;">
                                <i class="fas fa-info-circle me-1"></i> 
                                @if($isEdit)
                                    Unggah foto baru jika ingin mengganti dokumentasi yang sudah ada.
                                @else
                                    Unggah minimal 1 foto kegiatan lapangan sebagai bukti kehadiran.
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mb-3">
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-lg border-0 mb-3">
                        <i class="fas {{ $isEdit ? 'fa-save' : 'fa-cloud-upload-alt' }} me-2"></i> 
                        {{ $isEdit ? 'Simpan Perubahan' : 'Kirim Laporan Dinas' }}
                    </button>
                    <a href="{{ $isEdit ? route('meeting.history.dinas') : route('meeting.index') }}" class="btn btn-light w-100 rounded-pill py-2 text-muted fw-bold border">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Style tetap sama --}}
<link rel="stylesheet" href="{{ asset('css/pages/meeting-dinas-luar.css') }}">

{{-- Script SweetAlert dan Preview tetap sama --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/pages/meeting-dinas-luar.js') }}"></script>
@endsection