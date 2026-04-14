// 2. Fungsi Validasi Terpusat
function jalankanValidasi(tglTerpilih) {
    if (!tglTerpilih) return { valid: true };

    // --- A. CEK CUTI ---
    let cutiFound = null;
    window.daftarCuti.forEach(range => {
        const start = range.start_date.substring(0, 10);
        const end = range.end_date.substring(0, 10);
        if (tglTerpilih >= start && tglTerpilih <= end) {
            cutiFound = { start, end };
        }
    });

    if (cutiFound) {
        return {
            valid: false,
            title: 'Sedang Cuti!',
            icon: 'error',
            color: '#dc3545',
            msg: `Petugas sedang CUTI pada tanggal tersebut (${cutiFound.start} s.d ${cutiFound.end}).`
        };
    }

    // --- B. CEK TANGGAL BENTROK ---
    const cleanLaporanTerpakai = window.laporanTerpakai.map(tgl => tgl.substring(0, 10));
    if (cleanLaporanTerpakai.includes(tglTerpilih)) {
        return {
            valid: false,
            title: 'Tanggal Bentrok!',
            icon: 'warning',
            color: '#f59e0b',
            msg: 'Tanggal tersebut sudah digunakan untuk laporan tugas lain oleh petugas ini.'
        };
    }

    return { valid: true };
}

// --- SCRIPT WILAYAH ---
function updateDesaOptions(selectedKec, preselectDesa = '') {
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
        "Tuban": ["Banyuurip", "Doromukti", "Gedongombo", "Karang", "Karangsari", "Kebonsari", "Kutorejo", "Latsari", "Mondokan", "Panyuran", "Perbon", "Ronggomulyo", "Sendangharjo", "Sidomulyo", "Sukolilo", "Sukolilo", "Sugihwaras", "Sumurgung"],
        "Widang": ["Widang", "Banjar", "Bunut", "Kompang", "Mulyorejo", "Ngadirejo", "Ngadipuro", "Patihan", "Simorejo", "Sumberejo", "Tegalrejo", "Tegalsari", "Widang"]
    };

    const desaSelect = document.getElementById('desa');
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

document.addEventListener('DOMContentLoaded', function() {
    const inputTanggal = document.getElementById('tanggal_pelaksanaan');
    const formLaporan = document.getElementById('formLaporan');
    const kecSelect = document.getElementById('kecamatan');

    // 3. Event Listener saat Tanggal Diganti
    inputTanggal.addEventListener('change', function() {
        const check = jalankanValidasi(this.value);
        if (!check.valid) {
            Swal.fire({
                title: check.title,
                text: check.msg,
                icon: check.icon,
                confirmButtonColor: check.color
            });
            this.value = window.tanggalAwal; // Balikin ke tanggal semula
        }
    });

    // 4. Proteksi Terakhir saat Tombol Update Diklik
    formLaporan.addEventListener('submit', function(e) {
        const check = jalankanValidasi(inputTanggal.value);
        if (!check.valid) {
            e.preventDefault(); // Gagalkan kirim data
            Swal.fire({
                title: 'Gagal Simpan!',
                text: check.msg,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    });

    if (kecSelect.value) { updateDesaOptions(kecSelect.value, window.initialDesa); }
    kecSelect.addEventListener('change', function() { updateDesaOptions(this.value); });
});
