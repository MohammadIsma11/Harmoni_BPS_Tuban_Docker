@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @php
        // 1. Pecah string Lokasi
        $currentLocation = $agenda->location;
        $currentDesa = '';
        $currentKec = '';
        if (str_contains($currentLocation, ', Kec. ')) {
            $parts = explode(', Kec. ', $currentLocation);
            $currentKec = trim($parts[1] ?? '');
            $currentDesa = trim(str_replace('Desa ', '', $parts[0] ?? ''));
        }

        // 2. LOGIKA TANGGAL PELAKSANAAN
        // Kita prioritaskan kolom 'tanggal_pelaksanaan' (hasil input pegawai)
        // Jika kosong baru ambil 'event_date'
        $tanggalTerdeteksi = $agenda->tanggal_pelaksanaan 
                             ?? ($agenda->event_date 
                             ?? now()->format('Y-m-d'));

        // Format paksa ke Y-m-d agar Browser mau nampilin di input date
        $valTanggal = \Carbon\Carbon::parse($tanggalTerdeteksi)->format('Y-m-d');
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-11">
            {{-- Header --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="bg-warning p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3 text-white">
                            <i class="fas fa-edit fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-bold mb-0">Perbarui Laporan Pengawasan</h5>
                            <small class="text-white text-opacity-75">ID Agenda: #{{ $agenda->id }}</small>
                        </div>
                    </div>
                    <span class="badge bg-white text-warning rounded-pill px-3 shadow-sm fw-bold">MODE EDIT</span>
                </div>
            </div>

            <form action="{{ route('history.update', $agenda->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- SISI KIRI --}}
                    <div class="col-lg-5">
                        
                        {{-- INFO BAKU (READ ONLY) --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                            <h6 class="fw-bold mb-3 text-muted border-bottom pb-2">
                                <i class="fas fa-lock me-2"></i>Informasi Baku
                            </h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nama Kegiatan</label>
                                <textarea class="form-control border-0 bg-white fw-bold rounded-3" rows="2" readonly>{{ $agenda->title }}</textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nomor Surat Tugas</label>
                                <input type="text" class="form-control border-0 bg-white fw-bold rounded-3 text-primary" 
                                       value="{{ $agenda->nomor_surat_tugas ?? '-' }}" readonly>
                            </div>
                        </div>

                        {{-- LOKASI --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-primary">
                            <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-map-marked-alt me-2"></i>Perbarui Lokasi</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase">Kecamatan *</label>
                                <select name="kecamatan" id="kecamatan" class="form-select rounded-3 border-0 bg-light p-3 fw-bold" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    @php
                                        $kecamatans = ["Bancar", "Bangilan", "Grabagan", "Jatirogo", "Jenu", "Kenduruan", "Kerek", "Merakurak", "Montong", "Palang", "Parengan", "Plumpang", "Rengel", "Semanding", "Senori", "Singgahan", "Soko", "Tambakboyo", "Tuban", "Widang"];
                                        sort($kecamatans);
                                    @endphp
                                    @foreach($kecamatans as $kec)
                                        <option value="{{ $kec }}" {{ (old('kecamatan', $currentKec) == $kec) ? 'selected' : '' }}>{{ $kec }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase">Desa / Kelurahan *</label>
                                <select name="desa" id="desa" class="form-select rounded-3 border-0 bg-light p-3 fw-bold" required>
                                    <option value="">-- Pilih Desa --</option>
                                </select>
                            </div>
                        </div>

                        {{-- WAKTU PELAKSANAAN & FOTO --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                            <h6 class="fw-bold mb-3 text-warning"><i class="fas fa-calendar-check me-2"></i>Waktu Pelaksanaan</h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark text-uppercase">Tanggal Pelaksanaan Lapangan *</label>
                                <input type="date" name="tanggal_pelaksanaan" class="form-control rounded-3 shadow-sm border-warning fw-bold" 
                                       min="{{ \Carbon\Carbon::parse($agenda->event_date)->format('Y-m-d') }}" 
                                       max="{{ \Carbon\Carbon::parse($agenda->end_date)->format('Y-m-d') }}" 
                                       value="{{ $valTanggal }}" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark text-uppercase">Ganti Foto Dokumentasi</label>
                                <input type="file" name="fotos[]" id="foto_upload" class="form-control" accept="image/*" multiple>
                                <div class="form-text text-danger fw-bold" style="font-size: 0.6rem;">
                                    * Upload foto baru akan mengganti semua foto lama.
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-3 p-2 bg-light rounded-3 border border-dashed">
                                    @forelse($agenda->photos as $photo)
                                        <div class="position-relative border rounded-2 overflow-hidden shadow-sm" style="width: 55px; height: 55px;">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @empty
                                        <small class="text-muted italic">Tidak ada foto lama.</small>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                            <h6 class="fw-bold mb-4 border-bottom pb-2 text-dark"><i class="fas fa-clipboard-check me-2 text-success"></i>Detail Hasil Pengawasan</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">RESPONDEN / PETUGAS DITEMUI *</label>
                                <input type="text" name="responden" class="form-control rounded-3 bg-light border-0 p-3" required value="{{ old('responden', $agenda->responden) }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">AKTIVITAS DILAKUKAN *</label>
                                <textarea name="aktivitas" class="form-control rounded-3 bg-light border-0 p-3" rows="6" required>{{ old('aktivitas', $agenda->aktivitas) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-secondary">PERMASALAHAN LAPANGAN *</label>
                                <textarea name="permasalahan" class="form-control rounded-3 bg-light border-0 p-3" rows="3" required>{{ old('permasalahan', $agenda->permasalahan) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-success text-uppercase">Solusi / Tindak Lanjut *</label>
                                <textarea name="solusi_antisipasi" class="form-control rounded-3 bg-light border-0 p-3" rows="3" required>{{ old('solusi_antisipasi', $agenda->solusi_antisipasi) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <a href="{{ route('history.index') }}" class="btn btn-light px-4 rounded-pill fw-bold text-muted">Batal</a>
                                <button type="submit" class="btn btn-warning px-5 rounded-pill fw-bold shadow-lg text-white">
                                    <i class="fas fa-save me-2"></i> Update Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        "Rengel": ["Rengel", "Banjaragung", "Bulurejo", "Campurejo", "Kanor Kulon", "Karangtinoto", "Kebonagung", "Maibit", "Ngadirejo", "Pekuwon", "Prambontergayang", "Punggulrejo", "Rengel", "Sawahan", "Sumberejo", "Tambakharjo"],
        "Semanding": ["Semanding", "Bejagung", "Genaharjo", "Gesing", "Jadi", "Karang", "Kowang", "Ngino", "Penambangan", "Prunggahan Kulon", "Prunggahan Wetan", "Sambongrejo", "Semanding", "Tegalagung", "Tunah"],
        "Senori": ["Senori", "Banyuurip", "Jatisari", "Kaligede", "Kerep", "Leran", "Meduri", "Rayung", "Sendang", "Sidoharjo", "Wanglukulon", "Wangluwetan"],
        "Singgahan": ["Singgahan", "Binangun", "Lajo Kidul", "Lajo Lor", "Mulyoasri", "Mulyorejo", "Ngawun", "Saren", "Tanjungrejo", "Tingkis", "Tunggulrejo"],
        "Soko": ["Soko", "Bangunrejo", "Cekalang", "Glodog", "Jati", "Jegulo", "Kandangan", "Kenongosari", "Klumpit", "Menilo", "Nguruan", "Pandansari", "Pandanagung", "Prambontergayang", "Sandingrowo", "Simo", "Soko", "Tandun", "Tlogowaru"],
        "Tambakboyo": ["Tambakboyo", "Belikanget", "Cokrowati", "Dikir", "Gadun", "Kalisari", "Kenanti", "Klutuk", "Mabulur", "Nguluhan", "Pabeyan", "Plajan", "Pulogede", "Sawir", "Sotang", "Sukoharjo", "Tambakboyo"],
        "Tuban": ["Banyuurip", "Doromukti", "Gedongombo", "Karang", "Karangsari", "Kebonsari", "Kutorejo", "Latsari", "Mondokan", "Panyuran", "Perbon", "Ronggomulyo", "Sendangharjo", "Sidomulyo", "Sukolilo", "Sugihwaras", "Sumurgung"],
        "Widang": ["Widang", "Banjar", "Bunut", "Kompang", "Mulyorejo", "Ngadirejo", "Ngadipuro", "Patihan", "Simorejo", "Sumberejo", "Tegalrejo", "Tegalsari", "Widang"]
    };

    const kecSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');
    const initialDesa = @json(old('desa', $currentDesa));

    const fotoInput = document.getElementById('foto_upload');
    
    fotoInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        const maxSize = 10 * 1024 * 1024; // Kita set ke 10MB per foto agar lebih longgar
        let oversizedFiles = [];

        files.forEach(file => {
            if (file.size > maxSize) {
                oversizedFiles.push(file.name);
            }
        });

        if (oversizedFiles.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar!',
                html: `Foto berikut melebihi batas 10MB:<br><small class="text-danger">${oversizedFiles.join(', ')}</small><br><br>Silakan kompres foto atau pilih foto lain.`,
                confirmButtonColor: '#f59e0b',
                customClass: { popup: 'rounded-4' }
            });
            // Reset input agar user wajib memilih ulang yang benar
            this.value = ""; 
        }
    });

    // Tambahan: Efek Loading saat Update agar user tahu proses sedang berjalan
    const updateForm = document.querySelector('form');
    updateForm.addEventListener('submit', function() {
        Swal.fire({
            title: 'Sedang Mengunggah...',
            text: 'Harap tunggu, foto sedang diproses.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
    
    
    function updateDesaOptions(selectedKec, preselectDesa = '') {
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (selectedKec && dataWilayah[selectedKec]) {
            desaSelect.disabled = false;
            dataWilayah[selectedKec].sort().forEach(desa => {
                const option = document.createElement('option');
                option.value = desa;
                option.text = desa;
                if (preselectDesa === desa) { option.selected = true; }
                desaSelect.add(option);
            });
        } else {
            desaSelect.disabled = true;
        }
    }

    if (kecSelect.value) { updateDesaOptions(kecSelect.value, initialDesa); }
    kecSelect.addEventListener('change', function() { updateDesaOptions(this.value); });
</script>
@endsection