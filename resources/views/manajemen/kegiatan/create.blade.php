@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('manajemen.kegiatan.index') }}" class="btn btn-light rounded-circle me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h3 class="fw-bold mb-0">Tambah Kegiatan Baru</h3>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('manajemen.kegiatan.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">NAMA KEGIATAN *</label>
                            <input type="text" name="nama_kegiatan" class="form-control rounded-3 bg-light border-0 @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan') }}" required placeholder="Contoh: Survei Angkatan Kerja Nasional">
                            @error('nama_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">TIM / FUNGSI *</label>
                            <select name="tim_id" class="form-select rounded-3 bg-light border-0 @error('tim_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Tim Penanggungjawab --</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->nama_tim }}</option>
                                @endforeach
                            </select>
                            @error('tim_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">MIN HONOR (STANDARD)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">Rp</span>
                                    <input type="number" name="min_honor_standard" class="form-control rounded-end-3 bg-light border-0" value="{{ old('min_honor_standard', 0) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">MAX HONOR (STANDARD)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">Rp</span>
                                    <input type="number" name="max_honor_standard" class="form-control rounded-end-3 bg-light border-0" value="{{ old('max_honor_standard', 0) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('manajemen.kegiatan.index') }}" class="btn btn-light rounded-pill px-4 text-muted fw-bold">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">Simpan Kegiatan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
