<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/pages/panduan-index.css')); ?>">

<div class="container-fluid px-4 pb-5">
    <div class="guide-header animate-up">
        <div class="d-flex align-items-center justify-content-center mb-3">
            <img src="<?php echo e(asset('img/logo-bps.png')); ?>" alt="BPS" width="50" class="me-3">
            <h1 class="fw-bold mb-0">Harmoni Guide Center</h1>
        </div>
        <p class="text-muted mb-0 font-medium">Panduan Lengkap Penggunaan Aplikasi Harmoni BPS Kabupaten Tuban.</p>
    </div>

    
    <div class="row mb-4 animate-up delay-1">
        <div class="col-lg-8 mx-auto">
            <ul class="nav nav-pills nav-pills-custom justify-content-center mb-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-pegawai-tab" data-bs-toggle="pill" data-bs-target="#pills-pegawai" type="button" role="tab"><i class="fas fa-user"></i>Pegawai / Pelaksana</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-manajemen-tab" data-bs-toggle="pill" data-bs-target="#pills-manajemen" type="button" role="tab"><i class="fas fa-user-tie"></i>Katim & Kepala</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-admin-tab" data-bs-toggle="pill" data-bs-target="#pills-admin" type="button" role="tab"><i class="fas fa-tools"></i>Admin & Umum</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="row animate-up delay-2">
        <div class="col-lg-9 mx-auto">
            <div class="tab-content" id="pills-tabContent">
                
                
                <div class="tab-pane fade show active" id="pills-pegawai" role="tabpanel">
                    <h4 class="fw-bold mb-4"><i class="fas fa-tasks text-primary me-2"></i>Panduan Pelaksanaan Tugas</h4>
                    <div class="accordion" id="accPegawai">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#peg1">
                                    <span class="step-number">1</span> Melaporkan Tugas Lapangan (Transloka)
                                </button>
                            </h2>
                            <div id="peg1" class="accordion-collapse collapse show">
                                <div class="accordion-body text-muted small">
                                    <p>Tugas lapangan muncul setelah SPT disetujui oleh Kepala Kantor atau diunggah oleh pembuat tugas.</p>
                                    <ol>
                                        <li>Buka menu <strong>Tugas Lapangan > Daftar Tugas</strong>.</li>
                                        <li>Klik tombol <strong>Lapor</strong> pada tugas yang aktif.</li>
                                        <li><strong>Input Lokasi</strong>: Pilih Kecamatan dan Desa tujuan tugas.</li>
                                        <li><strong>Input Aktivitas</strong>: Tuliskan hasil kegiatan secara poin-per-poin (Gunakan format 1., 2., dst) agar rapi di laporan PDF.</li>
                                        <li><strong>Dokumentasi</strong>: Unggah minimal 1 foto kegiatan (Maksimal 6 foto per laporan).</li>
                                        <li><strong>Target Laporan</strong>: Jika tugas memiliki target lebih dari 1 (misal 5 desa), status tugas akan tetap "Pending" sampai Anda mengirimkan total 5 laporan desa yang berbeda.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#peg2">
                                    <span class="step-number">2</span> Presensi Rapat Digital (Tanda Tangan)
                                </button>
                            </h2>
                            <div id="peg2" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted small">
                                    <ol>
                                        <li>Masuk ke menu <strong>Kegiatan Dinas > Jadwal Rapat</strong>.</li>
                                        <li>Klik tombol <strong>Absen</strong> pada agenda yang sedang berlangsung hari ini.</li>
                                        <li>Goreskan tanda tangan digital Anda pada area kanvas yang tersedia.</li>
                                        <li>Klik <strong>Kirim Kehadiran</strong>. Status Anda akan otomatis berubah menjadi "Hadir".</li>
                                    </ol>
                                    <div class="info-highlight mt-2">
                                        <i class="fas fa-info-circle me-1"></i> Tanda tangan ini akan otomatis terinput ke dalam Dokumen Daftar Hadir Rapat yang dapat diunduh oleh Notulis.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#peg3">
                                    <span class="step-number">3</span> Pelaporan Dinas Luar (Luar Kota/Kantor)
                                </button>
                            </h2>
                            <div id="peg3" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted small">
                                    <ol>
                                        <li>Buka menu <strong>Kegiatan Dinas > Jadwal Kegiatan</strong>.</li>
                                        <li>Cari agenda bertipe "Dinas Luar". Klik tombol <strong>Lapor</strong>.</li>
                                        <li>Tulis lokasi spesifik kunjungan dan unggah file laporan (PDF/Word/Docx) hasil perjalanan dinas Anda beserta foto dokumentasi.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pills-manajemen" role="tabpanel">
                    <h4 class="fw-bold mb-4"><i class="fas fa-file-signature text-primary me-2"></i>Panduan Perencanaan & Persetujuan</h4>
                    <div class="accordion" id="accManagemen">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#man1">
                                    <span class="step-number">1</span> Membuat Penugasan Baru (Plotting)
                                </button>
                            </h2>
                            <div id="man1" class="accordion-collapse collapse show">
                                <div class="accordion-body text-muted small">
                                    <p>Katim dapat mengatur agenda tim melalui menu <strong>Perencanaan > Assignment</strong>.</p>
                                    <ul>
                                        <li><strong>Tipe Kegiatan</strong>: Pilih Tugas Lapangan, Rapat, atau Dinas Luar.</li>
                                        <li><strong>Smart Validation</strong>: Saat memilih tanggal, perhatikan nama pegawai:
                                            <ul>
                                                <li><span class="text-warning fw-bold">Kuning</span>: Pegawai sudah punya agenda lain di tanggal tersebut.</li>
                                                <li><span class="text-danger fw-bold">Merah</span>: Pegawai sedang dalam masa Cuti/Perjalanan Dinas (Sync dari Subbag Umum).</li>
                                            </ul>
                                        </li>
                                        <li><strong>Mode Surat</strong>: Gunakan "Ketik Surat" agar sistem men-generate SPT otomatis, atau "Upload PDF" jika SPT sudah ditandatangani manual.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#man2">
                                    <span class="step-number">2</span> Alur Persetujuan SPT (Katim & Kepala)
                                </button>
                            </h2>
                            <div id="man2" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted small">
                                    <ol>
                                        <li>Buka menu <strong>Perencanaan > Persetujuan SPT</strong>.</li>
                                        <li><strong>Katim</strong>: Melakukan review awal (Tombol Centang). Status tetap "Pending" namun akan muncul di akun Kepala.</li>
                                        <li><strong>Kepala</strong>: Klik Setujui untuk meresmikan SPT. Setelah disetujui, tanda tangan QR-Code Kepala akan muncul di dokumen dan tugas akan otomatis masuk ke HP/Sidebar Pegawai.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pills-admin" role="tabpanel">
                    <h4 class="fw-bold mb-4"><i class="fas fa-cogs text-primary me-2"></i>Panduan Administrasi Sistem</h4>
                    <div class="accordion" id="accAdmin">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#adm1">
                                    <span class="step-number">1</span> Manajemen User & Hak Akses
                                </button>
                            </h2>
                            <div id="adm1" class="accordion-collapse collapse show">
                                <div class="accordion-body text-muted small">
                                    <p>Admin dapat mengelola akun melalui menu <strong>Pengaturan > Manajemen User</strong>.</p>
                                    <ul>
                                        <li><strong>Role Kepala</strong>: Memiliki otoritas menyetujui seluruh SPT di aplikasi.</li>
                                        <li><strong>Role Katim</strong>: Dapat merencanakan penugasan dan memeriksa (review) SPT timnya.</li>
                                        <li><strong>Akses Super</strong>: Akun biasa (Pegawai) yang diberi hak akses tambahan untuk melihat seluruh riwayat kantor tanpa filter tim.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adm2">
                                    <span class="step-number">2</span> Sinkronisasi Absensi & Cuti (Subbag Umum)
                                </button>
                            </h2>
                            <div id="adm2" class="accordion-collapse collapse">
                                <div class="accordion-body text-muted small">
                                    <p>Sangat penting untuk mengupdate data Cuti agar Katim tidak menugaskan orang yang sedang tidak di kantor.</p>
                                    <ol>
                                        <li>Buka menu <strong>Administrasi > Gatekeeper Absensi</strong>.</li>
                                        <li><strong>Input Manual</strong>: Masukkan nama pegawai dan rentang tanggal cuti/dinas luar.</li>
                                        <li><strong>Import Excel</strong>: Anda dapat menunggah file Rekap Absensi bulanan untuk sinkronisasi massal data Perjalanan Dinas (PD) dan Cuti.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            
            <div class="card bg-light border-0 rounded-4 p-4 mt-5 text-center shadow-sm">
                <h5 class="fw-bold mb-2">Butuh Bantuan Lebih Lanjut?</h5>
                <p class="text-muted small mb-4">Jika Anda menemukan kendala teknis atau kesalahan data, silakan hubungi Tim IT BPS Kabupaten Tuban.</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="https://wa.me/6285156626040" target="_blank" class="btn btn-bps-blue px-4 rounded-pill fw-bold">
                        <i class="fab fa-whatsapp me-2"></i>Hubungi Admin IT (+62 851-5662-6040)
                    </a>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">
                        <i class="fas fa-home me-2"></i>Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5 mb-4 text-muted small">
        <p>© 2026 Harmoni BPS Tuban - Sistem Informasi Manajemen Agenda & Penugasan Terintegrasi</p>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                window.scrollTo({ top: 300, behavior: 'smooth' });
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/panduan/index.blade.php ENDPATH**/ ?>