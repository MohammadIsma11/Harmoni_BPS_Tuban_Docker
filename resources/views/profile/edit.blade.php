@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pb-5">
    {{-- Header Sederhana --}}
    <div class="mb-4 mt-4">
        <h4 class="fw-bold text-dark mb-1">Pengaturan Profil</h4>
        <p class="text-muted small">Kelola informasi data diri dan peran akses Anda dalam sistem Harmoni.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    {{-- DITAMBAHKAN enctype UNTUK UPLOAD FILE --}}
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- SISI KIRI: DATA DIRI & KEAMANAN --}}
                            <div class="col-md-6 border-end pe-md-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fas fa-user-circle me-2 text-primary"></i>Informasi Akun
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control rounded-3 bg-light border-0 @error('nama_lengkap') is-invalid @enderror" 
                                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="small fw-bold mb-1">Username</label>
                                    <input type="text" name="username" class="form-control rounded-3 bg-light border-0 @error('username') is-invalid @enderror" 
                                           value="{{ old('username', $user->username) }}" required>
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- FITUR BARU: UPLOAD TTD DIGITAL (KHUSUS KEPALA/KATIM) --}}
                                @if(in_array($user->role, ['Kepala', 'Katim']))
                                <div class="mb-4 p-3 border rounded-4 bg-white shadow-xs">
                                    <label class="small fw-bold mb-2 d-block text-dark">
                                        <i class="fas fa-pen-nib me-1 text-primary"></i> Tanda Tangan Digital
                                    </label>
                                    
                                    @if($user->signature)
                                        <div class="mb-3 p-2 border rounded bg-light text-center">
                                            <img src="{{ asset('storage/' . $user->signature) }}" alt="TTD Digital" style="max-height: 80px; width: auto;">
                                            <p class="text-muted mt-1 mb-0" style="font-size: 0.65rem;">Tanda tangan aktif saat ini</p>
                                        </div>
                                    @endif

                                    <input type="file" name="signature" class="form-control form-control-sm rounded-3 @error('signature') is-invalid @enderror" accept="image/png">
                                    <div class="form-text text-muted" style="font-size: 0.65rem;">
                                        Format: <b>PNG Transparan</b> (Max 2MB). Digunakan untuk cetak dokumen rapat.
                                    </div>
                                    @error('signature') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @endif

                                <h6 class="fw-bold text-dark mb-3 mt-4 border-bottom pb-2">
                                    <i class="fas fa-key me-2 text-warning"></i>Keamanan
                                </h6>
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Password Baru</label>
                                    <input type="password" name="password" class="form-control rounded-3 bg-light border-0 @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ganti">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="small fw-bold mb-1">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control rounded-3 bg-light border-0" placeholder="Ulangi password baru">
                                </div>
                            </div>

                            {{-- SISI KANAN: ROLE & AKSES --}}
                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                    <i class="fas fa-shield-alt me-2 text-success"></i>Akses & Tim
                                </h6>

                                {{-- Info Tim --}}
                                <div class="mb-4 p-3 bg-primary bg-opacity-10 rounded-4 shadow-xs border border-primary border-opacity-10">
                                    <label class="small fw-bold d-block mb-1 text-primary">Unit Kerja / Tim:</label>
                                    <span class="h6 fw-bold mb-0 text-dark">{{ $user->team->nama_tim ?? 'Lintas Tim' }}</span>
                                </div>

                                {{-- HIDDEN INPUTS (Agar tetap ter-update tanpa pemilihan manual) --}}
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <input type="hidden" name="has_super_access" value="{{ $user->has_super_access }}">

                                <div class="mt-5 pt-2">
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/pages/profile-edit.css') }}">
@endsection