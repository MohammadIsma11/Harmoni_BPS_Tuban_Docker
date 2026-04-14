<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/assignment-edit.css')); ?>">

<div class="container-fluid px-4 pb-5">
    <div class="mb-4 mt-3">
        <a href="<?php echo e(route('assignment.index')); ?>" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-muted shadow-sm border">
            <i class="fas fa-arrow-left me-1"></i> Batal & Kembali
        </a>
    </div>

    <form id="formAssignment" action="<?php echo e(route('assignment.update', $assignment->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="row g-4">
            
            <div class="col-lg-7">
                <div class="card card-assignment shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-4 me-3 text-warning"><i class="fas fa-edit fa-lg"></i></div>
                            <div>
                                <h4 class="fw-bold mb-0">Edit Plotting Penugasan</h4>
                                <p class="text-muted small mb-0">Perbarui detail agenda personil BPS Tuban.</p>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="fas fa-info-circle"></i>1. Informasi Utama</div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-2">Jenis Kegiatan<span class="required-star">*</span></label>
                                <select name="activity_type_id" id="activity_type_id" class="form-select border-primary border-opacity-25" required>
                                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type->id); ?>" <?php echo e((int)old('activity_type_id', $assignment->activity_type_id) === (int)$type->id ? 'selected' : ''); ?>>
                                            <?php echo e($type->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2">Nama Kegiatan<span class="required-star">*</span></label>
                                <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $assignment->title)); ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="small fw-bold mb-2" id="label-nomor-dokumen">Nomor Dokumen<span class="required-star">*</span></label>
                                <input type="text" name="nomor_surat_tugas" id="nomor_surat_tugas" class="form-control" value="<?php echo e(old('nomor_surat_tugas', $assignment->nomor_surat_tugas)); ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3" id="translok-container">
                                <label class="small fw-bold mb-2">Target Laporan / Translok<span class="required-star">*</span></label>
                                <input type="number" name="report_target" id="report_target" class="form-control" value="<?php echo e(old('report_target', $assignment->report_target ?? 1)); ?>" min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-2">Deskripsi (Opsional)</label>
                                <textarea name="description" class="form-control" rows="1"><?php echo e(old('description', $assignment->description)); ?></textarea>
                            </div>
                        </div>

                        <div class="form-section-title"><i class="fas fa-clock"></i>2. Waktu & Dokumen</div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="small fw-bold mb-2" id="label-event-date">Tanggal Mulai<span class="required-star">*</span></label>
                                <input type="date" name="event_date" id="event_date" class="form-control" value="<?php echo e(old('event_date', \Carbon\Carbon::parse($assignment->event_date)->format('Y-m-d'))); ?>" required>
                            </div>
                            <div class="col-md-6" id="end-date-container">
                                <label class="small fw-bold mb-2">Tanggal Selesai<span class="required-star">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(old('end_date', $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d') : '')); ?>">
                            </div>
                            <div class="col-md-6" id="time-field" style="display: none;">
                                <label class="small fw-bold mb-2">Jam Kegiatan<span class="required-star">*</span></label>
                                <input type="time" name="start_time" id="start_time" class="form-control" value="<?php echo e(old('start_time', $assignment->start_time)); ?>">
                            </div>
                        </div>

                        <div id="rapat-fields" style="display: none;" class="mb-4">
                            <div class="p-3 border border-warning border-opacity-25 rounded-4 bg-warning bg-opacity-10">
                                <label class="small fw-bold mb-2 text-dark"><i class="fas fa-pen-nib me-1"></i> Pilih Notulis Rapat<span class="required-star">*</span></label>
                                <select name="notulis_id" id="notulis-select" class="form-select border-warning">
                                    <option value="">-- Pilih dari petugas terpilih --</option>
                                </select>
                            </div>
                        </div>

                            <div class="form-section-title"><i class="fas fa-file-signature"></i>3. Dokumen & Persetujuan</div>
<div class="mb-4 p-3 border rounded-4 bg-light shadow-sm">
    <label class="small fw-bold mb-3 d-block">Metode Dokumen Tugas<span class="required-star">*</span></label>
    <div class="d-flex gap-4 mb-3">
        <div class="form-check custom-radio">
            <input class="form-check-input" type="radio" name="mode_surat" id="modeUpload" value="upload" <?php echo e($assignment->mode_surat == 'upload' ? 'checked' : ''); ?>>
            <label class="form-check-label fw-bold small" for="modeUpload">Upload PDF</label>
        </div>
        <div class="form-check custom-radio">
            <input class="form-check-input" type="radio" name="mode_surat" id="modeGenerate" value="generate" <?php echo e($assignment->mode_surat == 'generate' ? 'checked' : ''); ?>>
            <label class="form-check-label fw-bold small" for="modeGenerate">Ketik Surat</label>
        </div>
    </div>

    
    <div id="section-upload">
        <label class="small fw-bold mb-2" id="label-upload">File PDF</label>
        <input type="file" name="surat_tugas" id="surat_tugas" class="form-control bg-white shadow-sm" accept="application/pdf">
        
        
        <?php if($assignment->mode_surat == 'upload' && $assignment->surat_tugas_path): ?>
            <div class="mt-2">
                <a href="<?php echo e(asset('storage/'.$assignment->surat_tugas_path)); ?>" target="_blank" class="badge bg-white text-primary border py-2 px-3 rounded-pill shadow-sm">
                    <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen Terupload
                </a>
            </div>
        <?php endif; ?>
    </div>

    
    <div id="section-generate" style="display: none;">
        
        <?php if($assignment->mode_surat == 'generate'): ?>
            <div class="mb-3">
                <a href="<?php echo e(route('assignment.download-spt', $assignment->id)); ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-eye me-1"></i> Preview Hasil Ketik Surat (PDF)
                </a>
            </div>
        <?php endif; ?>

        <div class="mb-3 p-2 border rounded-3 bg-white shadow-sm" id="print-mode-container">
            <label class="small fw-bold mb-2 d-block text-primary"><i class="fas fa-print me-1"></i> Mode Output Surat</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="print_mode" id="modePerorang" value="perorang" <?php echo e($assignment->print_mode == 'perorang' ? 'checked' : ''); ?>>
                    <label class="form-check-label small fw-bold" for="modePerorang">Per Orang</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="print_mode" id="modeKolektif" value="kolektif" <?php echo e($assignment->print_mode == 'kolektif' ? 'checked' : ''); ?>>
                    <label class="form-check-label small fw-bold" for="modeKolektif">Kolektif</label>
                </div>
            </div>
        </div>

        <div id="spt-fields">
            <div class="mb-3"><label class="small fw-bold mb-1">Menimbang</label><textarea name="menimbang" class="form-control" rows="2"><?php echo e($assignment->menimbang); ?></textarea></div>
            <div class="mb-3"><label class="small fw-bold mb-1">Mengingat</label><textarea name="mengingat" class="form-control" rows="2"><?php echo e($assignment->mengingat); ?></textarea></div>
        </div>

        <div id="memo-fields" style="display: none;">
            <div class="mb-3"><label class="small fw-bold mb-1">Yth (Kepada)</label><input type="text" name="yth" class="form-control" value="<?php echo e($assignment->yth ?? 'Pegawai BPS Kabupaten Tuban'); ?>"></div>
            <div class="mb-3"><label class="small fw-bold mb-1">Lokasi Kegiatan <span class="required-star">*</span></label><input type="text" name="location" id="location" class="form-control" value="<?php echo e($assignment->location); ?>"></div>
        </div>

        <div class="mb-3">
            <label class="small fw-bold mb-2" id="label-content-surat">Isi Surat<span class="required-star">*</span></label>
            <textarea name="content_surat" id="content_surat" class="form-control" rows="3"><?php echo e(old('content_surat', $assignment->content_surat)); ?></textarea>
        </div>

        
        <div class="p-3 bg-white border rounded-4 shadow-sm">
            <label class="small fw-bold mb-3 d-block text-primary"><i class="fas fa-shield-alt me-1"></i> Pengaturan Persetujuan</label>
            <div class="d-flex gap-3 mb-3">
                <div class="form-check"><input class="form-check-input" type="radio" name="approval_type" id="appSingle" value="single" <?php echo e(!$assignment->reviewer_id ? 'checked' : ''); ?>><label class="form-check-label small fw-bold" for="appSingle">Single</label></div>
                <div class="form-check"><input class="form-check-input" type="radio" name="approval_type" id="appMultiple" value="multiple" <?php echo e($assignment->reviewer_id ? 'checked' : ''); ?>><label class="form-check-label small fw-bold" for="appMultiple">Multiple</label></div>
            </div>
            <div id="reviewer-container" style="<?php echo e($assignment->reviewer_id ? '' : 'display: none;'); ?>" class="mb-3">
                <label class="small fw-bold mb-2">Pilih Ketua Tim</label>
                <select name="reviewer_id" id="reviewer_id" class="form-select">
                    <option value="">-- Pilih Katim --</option>
                    <?php $__currentLoopData = $katims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($k->id); ?>" <?php echo e($assignment->reviewer_id == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_lengkap); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="small fw-bold mb-2">Pilih Kepala BPS</label>
                <select name="approver_id" id="approver_id" class="form-select">
                    <option value="">-- Pilih Kepala --</option>
                    <?php $__currentLoopData = $kepalas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($k->id); ?>" <?php echo e($assignment->approver_id == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama_lengkap); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-5">
                <div class="card card-assignment shadow-sm h-100 border-0">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="form-section-title">
                            <i class="fas fa-users"></i>3. Daftar Petugas
                            <div type="button" id="btnSelectAll" class="ms-auto"><i class="fas fa-check-double me-1"></i> Pilih Semua</div>
                        </div>
                        
                        <?php
                            $currentGroupIds = \App\Models\Agenda::where('title', $assignment->title)
                                ->where('event_date', $assignment->event_date)
                                ->pluck('assigned_to')
                                ->toArray();
                        ?>

                        <div class="user-selection-container shadow-sm mb-4">
                            <div class="user-selection-box p-2">
                                <?php $groups = ['Kepala BPS' => $kepalas, 'Ketua Tim' => $katims, 'Staf' => $pegawais]; ?>
                                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $users): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="user-group-label"><?php echo e($label); ?></div>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $isSelected = in_array($u->id, $currentGroupIds); ?>
                                        <div class="user-item petugas-row <?php echo e($isSelected ? 'selected' : ''); ?>" data-id="<?php echo e($u->id); ?>" data-name="<?php echo e($u->nama_lengkap); ?>">
                                            <input type="checkbox" name="assigned_to[]" value="<?php echo e($u->id); ?>" class="user-check d-none" <?php echo e($isSelected ? 'checked' : ''); ?>>
                                            <div class="custom-chk"></div>
                                            <span class="user-name small fw-bold text-dark"><?php echo e($u->nama_lengkap); ?></span>
                                            <span class="status-badge is-busy-text d-none" id="status_busy_<?php echo e($u->id); ?>">Ada Agenda</span>
                                            <span class="status-badge is-leave-text d-none" id="status_leave_<?php echo e($u->id); ?>">Sedang Cuti</span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <button type="button" id="btnConfirmSubmit" class="btn btn-warning w-100 rounded-pill py-3 fw-bold shadow-lg mt-auto text-white">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.savedNotulisId = "<?php echo e($assignment->notulis_id); ?>";
    window.hasExistingFile = "<?php echo e($assignment->surat_tugas_path ? 'true' : 'false'); ?>";
</script>
<script src="<?php echo e(asset('js/pages/assignment-edit.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/assignment/edit.blade.php ENDPATH**/ ?>