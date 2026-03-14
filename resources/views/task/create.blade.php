@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-11 mt-4">
            {{-- Header Form --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-primary p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3">
                            <i class="fas fa-file-signature text-white fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Form Laporan Pengawasan Lapangan</h5>
                            <small class="text-white text-opacity-75">Silakan lengkapi detail lokasi dan hasil pengawasan</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-primary rounded-pill px-3 shadow-sm">ID AGENDA: #{{ $agenda->id }}</span>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('task.store', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    {{-- SISI KIRI: INFO PENUGASAN, WILAYAH & WAKTU --}}
                    <div class="col-lg-5">
                        {{-- CARD 1: INFO PENUGASAN --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 text-muted border-bottom pb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-info-circle me-2"></i>Informasi Penugasan
                            </h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Kegiatan</label>
                                <textarea class="form-control border-0 bg-white fw-bold rounded-3" rows="2" readonly style="resize: none; font-size: 0.9rem;">{{ $agenda->title }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nomor Surat Tugas</label>
                                <input type="text" class="form-control border-0 bg-white fw-bold rounded-3 text-primary" 
                                       value="{{ $agenda->nomor_surat_tugas }}" readonly>
                            </div>
                        </div>

                        {{-- CARD 2: LOKASI --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-primary">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Lokasi Pengawasan</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan *</label>
                                <select name="kecamatan" id="kecamatan" class="form-select rounded-3 border-0 bg-light p-3 fw-bold" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @php
                                        $kecamatans = ["Bancar", "Bangilan", "Grabagan", "Jatirogo", "Jenu", "Kenduruan", "Kerek", "Merakurak", "Montong", "Palang", "Parengan", "Plumpang", "Rengel", "Semanding", "Senori", "Singgahan", "Soko", "Tambakboyo", "Tuban", "Widang"];
                                        sort($kecamatans);
                                    @endphp
                                    @foreach($kecamatans as $kec)
                                        <option value="{{ $kec }}" {{ old('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Desa / Kelurahan *</label>
                                <select name="desa" id="desa" class="form-select rounded-3 border-0 bg-light p-3 fw-bold" required disabled>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                        </div>

                        {{-- CARD 3: WAKTU & FOTO --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-warning">
                            <h6 class="fw-bold mb-3 text-warning"><i class="fas fa-calendar-alt me-2"></i>Waktu & Dokumentasi</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase">Tanggal Pelaksanaan *</label>
                                <input type="date" name="tanggal_pelaksanaan" class="form-control rounded-3 shadow-sm border-warning fw-bold" 
                                       value="{{ old('tanggal_pelaksanaan') }}"
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" required>
                                <div class="form-text mt-2 text-muted" style="font-size: 0.7rem;">
                                    <i class="fas fa-info-circle me-1"></i> Rentang ST: 
                                    <strong>{{ \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d M') }} s/d {{ \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d M Y') }}</strong>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark text-uppercase">Upload Foto Dokumentasi *</label>
                                <input type="file" name="fotos[]" id="foto_upload" class="form-control rounded-3" accept="image/*" multiple required>
                                <div id="preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN: HASIL LAPORAN --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2 text-dark text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                                <i class="fas fa-clipboard-check me-2 text-success"></i>Detail Hasil Pengawasan
                            </h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">RESPONDEN / PETUGAS YANG DITEMUI *</label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3" 
                                       value="{{ old('responden') }}" placeholder="Nama responden atau petugas lapangan..." required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS YANG DILAKUKAN *</label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3" rows="5" 
                                          placeholder="Ceritakan detail kegiatan pengawasan..." required>{{ old('aktivitas') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN / TEMUAN *</label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3" rows="3" 
                                          placeholder="Kendala atau temuan di lokasi..." required>{{ old('permasalahan') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success text-uppercase">Solusi / Tindak Lanjut *</label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3" rows="3" 
                                          placeholder="Tindakan yang diambil untuk mengatasi masalah..." required>{{ old('solusi_antisipasi') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('task.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">Batal</a>
                                <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-lg py-2">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Laporan
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
    const dataWilayah = {
        "Bancar": ["Bancar", "Banjarejo", "Bogorejo", "Bulujowo", "Demit", "Gandu", "Jatisari", "Karangrejo", "Kayen", "Luwihaji", "Margosuko", "Ngadipuro", "Ngujuran", "Pugoh", "Sembungin", "Sidotentrem", "Siruar", "Sukasari", "Sumberan", "Tlogoagung", "Tengger Kulon", "Tengger Wetan"],
        "Bangilan": ["Bangilan", "Banjarworo", "Bate", "Bedukan", "Kumpulrejo", "Ngroto", "Sidokumpul", "Sidotentrem", "Sidorejo", "Soto", "Wedi", "Klakeh", "Kebonagung", "Wediyani"],
        "Grabagan": ["Grabagan", "Banyubang", "Dahor", "Dermawuharjo", "Gesikan", "Menyunyur", "Ngasinan", "Ngandong", "Ngarum", "Pacing", "Pakis", "Waleran"],
        "Jatirogo": ["Jatirogo", "Badegan", "Besowo", "Dingin", "Jatirejo", "Karangtengah", "Kebonharjo", "Ketitang", "Klampok", "Paseyan", "Sadang", "Sekaran", "Sidotentrem", "Sugihan", "Wotsogo"],
        "Jenu": ["Jenu", "Beji", "Jenggolo", "Kaliuntu", "Karangasem", "Mentoso", "Purworejo", "Rawasan", "Remen", "Sekardadi", "Socorejo", "Suwalan", "Tasikharjo", "Temaji", "Wadang", "Sugiawaras", "Sumurgeneng"],
        "Kenduruan": ["Kenduruan", "Bendonglateng", "Jamprong", "Jatihadi", "Pandan Agung", "Pandanwangi", "Papringan", "Sidorejo", "Sidomukti"],
        "Kerek": ["Kerek", "Gaji", "Gemulung", "Hargoretno", "Jarorejo", "Karanglo", "Kasiman", "Kedungrejo", "Margomulyo", "Mliwang", "Padasan", "Sidonganti", "Sumberarum", "Temayang", "Trantang", "Wolo"],
        "Merakurak": ["Merakurak", "Bogorejo", "Borehbilo", "Kapu", "Mandirejo", "Paparuan", "Sambonggede", "Sidoasri", "Sengon", "Sumberejo", "Tahulu", "Tegalrejo", "Temandang", "Tuwiri Kulon", "Tuwiri Wetan"],
        "Montong": ["Montong", "Bringin", "Guwoterus", "Jetak", "Maindu", "Manjung", "Montongsekar", "Nguluhan", "Pacing", "Pakel", "Pucangan", "Sumurgung", "Talangkembar", "Talun"],
        "Palang": ["Palang", "Cendoro", "Cepokorejo", "Dawung", "Glagahwaru", "Karangagung", "Ketambul", "Kradenan", "Leran Kulon", "Leran Wetan", "Ngimbang", "Panyuran", "Sumurgung", "Tegalbang", "Tasikmadu", "Waru"],
        "Parengan": ["Parengan", "Brangkal", "Cengkong", "Dagangan", "Kemlaten", "Kumpulrejo", "Mergoasri", "Mojoagung", "Mulyoagung", "Mulyorejo", "Ngawun", "Pacing", "Parangbatu", "Selogabus", "Sembung", "Suciharjo", "Sugihwaras", "Sukorejo", "Tinggahan"],
        "Plumpang": ["Plumpang", "Bandungrejo", "Cangkring", "Kebomlati", "Kecapi", "Kedungasri", "Kedungrejo", "Kedungsoko", "Kepohagung", "Klapadyangan", "Magersari", "Ngadipuro", "Panyuran", "Penidon", "Plandirejo", "Sembungrejo", "Sumberejo", "Trutup"],
        "Rengel": ["Rengel", "Banjaragung", "Bulurejo", "Campurejo", "Kanor Kulon", "Karangtinoto", "Kebonagung", "Maibit", "Ngadirejo", "Pekuwon", "Prambontergayang", "Punggulrejo", "Sawahan", "Sumberejo", "Tambakharjo"],
        "Semanding": ["Semanding", "Bejagung", "Genaharjo", "Gesing", "Jadi", "Karang", "Kowang", "Ngino", "Penambangan", "Prunggahan Kulon", "Prunggahan Wetan", "Sambongrejo", "Tegalagung", "Tunah"],
        "Senori": ["Senori", "Banyuurip", "Jatisari", "Kaligede", "Kerep", "Leran", "Meduri", "Rayung", "Sendang", "Sidoharjo", "Wanglukulon", "Wangluwetan"],
        "Singgahan": ["Singgahan", "Binangun", "Lajo Kidul", "Lajo Lor", "Mulyoasri", "Mulyorejo", "Ngawun", "Saren", "Tanjungrejo", "Tingkis", "Tunggulrejo"],
        "Soko": ["Soko", "Bangunrejo", "Cekalang", "Glodog", "Jati", "Jegulo", "Kandangan", "Kenongosari", "Klumpit", "Menilo", "Nguruan", "Pandansari", "Pandanagung", "Prambontergayang", "Sandingrowo", "Simo", "Soko", "Tandun", "Tlogowaru"],
        "Tambakboyo": ["Tambakboyo", "Belikanget", "Cokrowati", "Dikir", "Gadun", "Kalisari", "Kenanti", "Klutuk", "Mabulur", "Nguluhan", "Pabeyan", "Plajan", "Pulogede", "Sawir", "Sotang", "Sukoharjo"],
        "Tuban": ["Banyuurip", "Doromukti", "Gedongombo", "Karang", "Karangsari", "Kebonsari", "Kutorejo", "Latsari", "Mondokan", "Panyuran", "Perbon", "Ronggomulyo", "Sendangharjo", "Sidomulyo", "Sukolilo", "Sugihwaras", "Sumurgung"],
        "Widang": ["Widang", "Banjar", "Bunut", "Kompang", "Mulyorejo", "Ngadirejo", "Ngadipuro", "Patihan", "Simorejo", "Sumberejo", "Tegalrejo", "Tegalsari", "Widang"]
    };

    const kecSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');
    const fotoUpload = document.getElementById('foto_upload');
    const previewContainer = document.getElementById('preview-container');

    kecSelect.addEventListener('change', function() {
        const selectedKec = this.value;
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (selectedKec && dataWilayah[selectedKec]) {
            desaSelect.disabled = false;
            dataWilayah[selectedKec].sort().forEach(desa => {
                const option = document.createElement('option');
                option.value = desa;
                option.text = desa;
                desaSelect.add(option);
            });
        } else {
            desaSelect.disabled = true;
        }
    });

    // Preview Foto
    fotoUpload.addEventListener('change', function() {
        previewContainer.innerHTML = '';
        const files = Array.from(this.files);
        files.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                img.classList.add('rounded-3', 'border', 'shadow-sm');
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    });
</script>

<style>
    .form-select, .form-control { border: 1px solid #e2e8f0; transition: 0.3s; }
    .form-control:focus, .form-select:focus { 
        border-color: #0058a8; 
        box-shadow: 0 0 0 0.25rem rgba(0, 88, 168, 0.1); 
        background-color: #fff !important;
    }
    .btn-primary { background: linear-gradient(135deg, #0058a8 0%, #007bff 100%); border: none; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 88, 168, 0.3); }
</style>
@endsection