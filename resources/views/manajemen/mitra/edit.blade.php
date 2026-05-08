@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="d-flex align-items-center mb-4 animate-up">
                <a href="{{ route('manajemen.mitra.index') }}" class="btn btn-light rounded-circle me-3 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-0">Edit Data Mitra</h3>
                    <p class="text-muted small mb-0">Perbarui informasi profil dan administrasi mitra.</p>
                </div>
            </div>

            <form action="{{ route('manajemen.mitra.update', $mitra->sobat_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <!-- Column 1 -->
                    <div class="col-md-6">
                        <!-- Section: Identitas & Profil -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-up" style="animation-delay: 0.1s;">
                            <div class="card-header bg-white border-0 py-3 ps-4">
                                <h6 class="fw-bold mb-0"><i class="fas fa-id-card text-primary me-2"></i>Identitas & Profil</h6>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">SOBAT ID (Readonly)</label>
                                    <input type="text" class="form-control rounded-3 bg-light border-0" value="{{ $mitra->sobat_id }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">NAMA LENGKAP *</label>
                                    <input type="text" name="nama_lengkap" class="form-control rounded-3 bg-light border-0 @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $mitra->nama_lengkap) }}" required>
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">JENIS KELAMIN</label>
                                        <select name="jenis_kelamin" class="form-select rounded-3 bg-light border-0">
                                            <option value="L" {{ old('jenis_kelamin', $mitra->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('jenis_kelamin', $mitra->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">PENDIDIKAN</label>
                                        <select name="pendidikan" class="form-select rounded-3 bg-light border-0">
                                            <option value="">-- Pilih Pendidikan --</option>
                                            @foreach(['TAMAT SD / SEDERAJAT', 'TAMAT SMP / SEDERAJAT', 'TAMAT SMA / SEDERAJAT', 'TAMAT D1 / D2 / D3', 'TAMAT D4 / S1', 'TAMAT S2', 'TAMAT S3'] as $edu)
                                                <option value="{{ $edu }}" {{ old('pendidikan', $mitra->pendidikan) == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Data Kelahiran -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-up" style="animation-delay: 0.2s;">
                            <div class="card-header bg-white border-0 py-3 ps-4">
                                <h6 class="fw-bold mb-0"><i class="fas fa-calendar-day text-success me-2"></i>Data Kelahiran</h6>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">TEMPAT LAHIR</label>
                                    <input type="text" name="tempat_lahir" class="form-control rounded-3 bg-light border-0" value="{{ old('tempat_lahir', $mitra->tempat_lahir) }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <label class="form-label small fw-bold text-muted">TANGGAL LAHIR</label>
                                        <input type="date" name="tgl_lahir" class="form-control rounded-3 bg-light border-0" value="{{ old('tgl_lahir', $mitra->tgl_lahir ? $mitra->tgl_lahir->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold text-muted">UMUR</label>
                                        <input type="number" name="umur" class="form-control rounded-3 bg-light border-0" value="{{ old('umur', $mitra->umur) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-6">
                        <!-- Section: Pekerjaan & Posisi -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-up" style="animation-delay: 0.3s;">
                            <div class="card-header bg-white border-0 py-3 ps-4">
                                <h6 class="fw-bold mb-0"><i class="fas fa-briefcase text-warning me-2"></i>Pekerjaan & Posisi</h6>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">POSISI MITRA</label>
                                    <div class="d-flex flex-wrap gap-3 mt-1">
                                        @php
                                            $posisiStr = strtolower($mitra->posisi ?? '');
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="posisi[]" value="Mitra Pendataan" id="pos1" {{ str_contains($posisiStr, 'pendataan') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="pos1">Mitra Pendataan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="posisi[]" value="Mitra Pengawasan" id="pos2" {{ str_contains($posisiStr, 'pengawasan') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="pos2">Mitra Pengawasan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="posisi[]" value="Mitra Pengolahan" id="pos3" {{ str_contains($posisiStr, 'pengolahan') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="pos3">Mitra Pengolahan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">POSISI DAFTAR</label>
                                    <input type="text" name="posisi_daftar" class="form-control rounded-3 bg-light border-0" value="{{ old('posisi_daftar', $mitra->posisi_daftar) }}" placeholder="Contoh: Petugas Pendataan Lapangan">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">PEKERJAAN UTAMA</label>
                                    <input type="text" name="pekerjaan" class="form-control rounded-3 bg-light border-0" value="{{ old('pekerjaan', $mitra->pekerjaan) }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted">DESKRIPSI PEKERJAAN LAIN</label>
                                    <textarea name="deskripsi_pekerjaan_lain" class="form-control rounded-3 bg-light border-0" rows="2">{{ old('deskripsi_pekerjaan_lain', $mitra->deskripsi_pekerjaan_lain) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Kontak & Domisili -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-up" style="animation-delay: 0.4s;">
                            <div class="card-header bg-white border-0 py-3 ps-4">
                                <h6 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Kontak & Alamat</h6>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">EMAIL</label>
                                        <input type="email" name="email" class="form-control rounded-3 bg-light border-0" value="{{ old('email', $mitra->email) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">NO TELP</label>
                                        <input type="text" name="no_telp" class="form-control rounded-3 bg-light border-0" value="{{ old('no_telp', $mitra->no_telp) }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">ALAMAT DETAIL</label>
                                    <textarea name="alamat_detail" class="form-control rounded-3 bg-light border-0" rows="2">{{ old('alamat_detail', $mitra->alamat_detail) }}</textarea>
                                </div>
                                <div class="row g-2 mb-0">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Provinsi</label>
                                        <input type="text" name="alamat_prov" class="form-control form-control-sm rounded-3 bg-light border-0" value="{{ old('alamat_prov', $mitra->alamat_prov) }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Kabupaten</label>
                                        <input type="text" name="alamat_kab" class="form-control form-control-sm rounded-3 bg-light border-0" value="{{ old('alamat_kab', $mitra->alamat_kab) }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Kecamatan</label>
                                        <input type="text" name="alamat_kec" class="form-control form-control-sm rounded-3 bg-light border-0" value="{{ old('alamat_kec', $mitra->alamat_kec) }}" placeholder="Kecamatan">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Desa/Kelurahan</label>
                                        <input type="text" name="alamat_desa" class="form-control form-control-sm rounded-3 bg-light border-0" value="{{ old('alamat_desa', $mitra->alamat_desa) }}" placeholder="Desa">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Administrasi BPS -->
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-up" style="animation-delay: 0.5s;">
                            <div class="card-header bg-white border-0 py-3 ps-4">
                                <h6 class="fw-bold mb-0"><i class="fas fa-cog text-secondary me-2"></i>Administrasi BPS</h6>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">STATUS SELEKSI</label>
                                    <select name="status_seleksi" class="form-select rounded-3 bg-light border-0">
                                        @foreach(['Diterima', 'Ditolak', 'Menunggu Konfirmasi'] as $status)
                                            <option value="{{ $status }}" {{ old('status_seleksi', $mitra->status_seleksi) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted">PLAFON HONOR BULANAN *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 text-muted">Rp</span>
                                        <input type="number" name="max_honor_bulanan" class="form-control rounded-end-3 bg-light border-0" value="{{ old('max_honor_bulanan', $mitra->max_honor_bulanan) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2 mb-5 animate-up" style="animation-delay: 0.6s;">
                    <a href="{{ route('manajemen.mitra.index') }}" class="btn btn-light rounded-pill px-4 text-muted fw-bold">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i>Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .animate-up { animation: fadeInUp 0.5s ease-out backwards; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .card { transition: transform 0.2s; }
    .card:hover { transform: translateY(-5px); }
</style>
@endsection
