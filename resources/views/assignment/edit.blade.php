@extends('layouts.app')

@section('content')
<style>
    :root { --bps-blue: #0058a8; --bps-text: #1e293b; }
    .card-assignment { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
    .form-section-title { 
        font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; 
        letter-spacing: 1.5px; margin-bottom: 1.25rem; display: flex; align-items: center;
    }
    .form-section-title i { margin-right: 10px; color: var(--bps-blue); }
    .form-section-title::after { content: ""; flex: 1; height: 1px; background: #f1f5f9; margin-left: 15px; }
    
    .user-selection-container { border: 1px solid #e2e8f0; border-radius: 15px; background: #fff; overflow: hidden; transition: 0.3s; }
    .user-selection-box { max-height: 450px; overflow-y: auto; }
    .user-group-label { background: #f8fafc; color: #64748b; font-weight: 800; padding: 12px 15px; font-size: 0.65rem; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 10; }
    .user-item { padding: 12px 15px; border-bottom: 1px solid #f8fafc; display: flex; align-items: center; cursor: pointer; transition: 0.2s; }
    .user-item:hover { background-color: #f0f7ff; }
    .custom-chk { width: 20px; height: 20px; border-radius: 6px; border: 2px solid #cbd5e1; margin-right: 12px; display: flex; align-items: center; justify-content: center; background: #fff; flex-shrink: 0; }
    .user-check:checked + .custom-chk { background-color: var(--bps-blue); border-color: var(--bps-blue); }
    .user-check:checked + .custom-chk::after { content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: #fff; font-size: 10px; }
    
    .form-control, .form-select { border-radius: 10px; padding: 0.75rem; min-height: 48px; }
    .required-star { color: #ef4444; margin-left: 3px; font-weight: bold; }
</style>

<div class="container-fluid px-4 pb-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('assignment.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-muted shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <form id="formAssignment" action="{{ route('assignment.update', $assignment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">
            {{-- KIRI: DETAIL KEGIATAN --}}
            <div class="col-lg-7">
                <div class="card card-assignment shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning"><i class="fas fa-edit fa-lg"></i></div>
                            <div>
                                <h4 class="fw-bold mb-0">Edit Rangkaian Penugasan</h4>
                                <p class="text-muted small mb-0">Perubahan akan diterapkan pada semua petugas dalam grup ini.</p>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="fas fa-info-circle"></i>1. Informasi Utama</div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Jenis Kegiatan<span class="required-star">*</span></label>
                                <select name="activity_type_id" id="activity_type_id" class="form-select border-primary border-opacity-25" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" 
                                            {{ (int)old('activity_type_id', $assignment->activity_type_id) === (int)$type->id ? 'selected' : '' }}>
                                            {{ $type->name }}  {{-- GANTI DARI nama_tipe MENJADI name --}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Nama Kegiatan<span class="required-star">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $assignment->title) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold mb-2">Deskripsi / Instruksi</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $assignment->description) }}</textarea>
                        </div>

                        <div class="row g-3 mb-4" id="st-number-container">
                            <div class="col-md-12">
                                <label class="small fw-bold mb-2">Nomor Surat Tugas (ST)<span class="required-star">*</span></label>
                                <input type="text" name="nomor_surat_tugas" id="nomor_surat_tugas" class="form-control" value="{{ old('nomor_surat_tugas', $assignment->nomor_surat_tugas) }}">
                            </div>
                        </div>

                        <div class="form-section-title"><i class="fas fa-clock"></i>2. Waktu & Dokumen</div>
                        <div class="row g-3 mb-4">
                           <div class="col-md-6">
                                <label class="small fw-bold mb-2" id="label-event-date">Tanggal Mulai<span class="required-star">*</span></label>
                                <input type="date" name="event_date" id="event_date" class="form-control" 
                                    value="{{ old('event_date', \Carbon\Carbon::parse($assignment->event_date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6" id="end-date-container">
                                <label class="small fw-bold mb-2">Tanggal Selesai<span class="required-star">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                    value="{{ old('end_date', $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-4" id="time-field" style="display: none;">
                                <label class="small fw-bold mb-2">Jam<span class="required-star">*</span></label>
                                <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $assignment->start_time) }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold mb-2" id="label-upload">
                                <i class="fas fa-file-pdf me-1 text-danger"></i> Update Dokumen (PDF)
                            </label>
                            <input type="file" name="surat_tugas" id="surat_tugas" class="form-control" accept="application/pdf">
                            @if($assignment->surat_tugas_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/'.$assignment->surat_tugas_path) }}" target="_blank" class="badge bg-light text-primary border py-2 px-3">
                                        <i class="fas fa-eye me-1"></i> Lihat Dokumen Saat Ini
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div id="rapat-fields" style="display: none;" class="bg-light p-3 rounded-4 border">
                            <label class="small fw-bold mb-2 text-primary"><i class="fas fa-user-pen me-1"></i> Pilih Notulis Rapat<span class="required-star">*</span></label>
                            <select name="notulis_id" class="form-select" id="notulis-select">
                                <option value="">-- Pilih dari petugas terpilih --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KANAN: PILIH PETUGAS --}}
            <div class="col-lg-5">
                <div class="card card-assignment shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="fas fa-users"></i>3. Edit Daftar Petugas<span class="required-star">*</span></div>
                        
                        @php
                            $currentGroupIds = \App\Models\Agenda::where('title', $assignment->title)
                                ->where('event_date', $assignment->event_date)
                                ->pluck('assigned_to')
                                ->toArray();
                        @endphp

                        <div class="user-selection-container shadow-sm mb-4">
                            <div class="user-selection-box">
                                @php $groups = ['Kepala BPS' => $kepalas, 'Ketua Tim' => $katims, 'Staf' => $pegawais]; @endphp
                                @foreach($groups as $label => $users)
                                    <div class="user-group-label">{{ $label }}</div>
                                    @foreach($users as $u)
                                        <div class="user-item petugas-row" data-id="{{ $u->id }}" data-name="{{ $u->nama_lengkap }}">
                                            <input type="checkbox" name="assigned_to[]" value="{{ $u->id }}" class="user-check d-none" {{ in_array($u->id, $currentGroupIds) ? 'checked' : '' }}>
                                            <div class="custom-chk"></div>
                                            <span class="user-name small fw-bold text-dark">{{ $u->nama_lengkap }}</span>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-3 fw-bold shadow-lg" id="btnSubmit">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateNotulisDropdown() {
        const notulisSelect = $('#notulis-select');
        const savedNotulisId = "{{ $assignment->notulis_id }}";
        
        notulisSelect.html('<option value="">-- Pilih dari petugas terpilih --</option>');
        $('.user-check:checked').each(function() {
            const id = $(this).val();
            const name = $(this).closest('.petugas-row').data('name');
            const isSelected = (id == savedNotulisId) ? 'selected' : '';
            notulisSelect.append(`<option value="${id}" ${isSelected}>${name}</option>`);
        });
    }

    function handleTypeChange(type) {
        const star = '<span class="required-star">*</span>';
        
        // Sembunyikan semua dulu secara default
        $('#st-number-container, #end-date-container, #time-field, #rapat-fields').hide();

        if (type == '1') { // TUGAS LAPANGAN
            $('#st-number-container, #end-date-container').show();
            $('#label-event-date').text('Tanggal Mulai');
            $('#label-event-date').append(star);
            
            $('#nomor_surat_tugas, #end_date').prop('required', true);
            $('#start_time, #notulis-select').prop('required', false);
        } 
        else if (type == '2' || type == '3') { // RAPAT atau DINAS LUAR
            $('#time-field').show();
            $('#label-event-date').text('Tanggal Pelaksanaan');
            $('#label-event-date').append(star);
            
            $('#nomor_surat_tugas, #end_date').prop('required', false);
            $('#start_time').prop('required', true);

            if (type == '2') { // Hanya Rapat
                $('#rapat-fields').show();
                $('#notulis-select').prop('required', true);
            }
        }
    }

    $(document).ready(function() {
        // AMBIL NILAI AWAL DARI DATABASE
        const currentType = "{{ $assignment->activity_type_id }}";
        
        // Jalankan fungsi berdasarkan tipe yang ada di database
        handleTypeChange(currentType);
        updateNotulisDropdown();

        // Listen perubahan manual
        $('#activity_type_id').on('change', function() {
            handleTypeChange($(this).val());
        });

        // Trigger perubahan tanggal selesai jika bukan lapangan
        $('#event_date').on('change', function() {
            const val = $(this).val();
            $('#end_date').attr('min', val);
            if($('#activity_type_id').val() != '1') {
                $('#end_date').val(val);
            }
        });

        // Handling checkbox petugas
        $('.user-item').on('click', function(e) {
            if (!$(e.target).is('input')) {
                const cb = $(this).find('.user-check');
                cb.prop('checked', !cb.prop('checked')).trigger('change');
            }
        });

        $('.user-check').on('change', function() {
            updateNotulisDropdown();
        });
    });
</script>
</script>
@endsection