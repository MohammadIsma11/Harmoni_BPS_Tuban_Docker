// --- LOGIKA KECAMATAN & DESA ---
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

function updateDesaOptions(selectedKec, preselectDesa = '') {
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
    const kecSelect = document.getElementById('kecamatan');

    inputTanggal.addEventListener('change', function() {
        const tglTerpilih = this.value; // Format: YYYY-MM-DD
        
        if (!tglTerpilih) return;

        // --- A. LOGIKA CEK CUTI (DIPERKETAT) ---
        let isCuti = false;
        let rangeCutiText = "";

        window.userCuti.forEach(range => {
            // Pastikan format date-only (ambil 10 karakter pertama YYYY-MM-DD)
            const start = range.start_date.substring(0, 10);
            const end = range.end_date.substring(0, 10);

            if (tglTerpilih >= start && tglTerpilih <= end) {
                isCuti = true;
                rangeCutiText = `${start} s.d ${end}`;
            }
        });

        if (isCuti) {
            Swal.fire({
                title: 'Akses Ditolak!',
                html: `Anda tidak bisa memilih tanggal <b>${tglTerpilih}</b> karena status Anda sedang <b>CUTI</b> pada periode ${rangeCutiText}.`,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            this.value = ''; // Kosongkan kembali
            return; // Berhenti disini
        }

        // --- B. LOGIKA CEK TANGGAL SUDAH TERPAKAI ---
        // Normalisasi array laporanTerpakai (ambil YYYY-MM-DD saja)
        const cleanLaporanTerpakai = window.laporanTerpakai.map(tgl => tgl.substring(0, 10));

        if (cleanLaporanTerpakai.includes(tglTerpilih)) {
            Swal.fire({
                title: 'Jadwal Bentrok!',
                text: 'Tanggal tersebut sudah Anda gunakan untuk melaporkan penugasan lain. Silakan pilih tanggal pelaksanaan yang berbeda.',
                icon: 'warning',
                confirmButtonColor: '#f59e0b'
            });
            this.value = ''; // Kosongkan kembali
            return;
        }
    });

    if (kecSelect.value) { updateDesaOptions(kecSelect.value, window.initialDesa); }
    kecSelect.addEventListener('change', function() { updateDesaOptions(this.value); });
});
