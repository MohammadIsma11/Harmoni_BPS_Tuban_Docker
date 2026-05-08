// --- LOGIKA KECAMATAN & DESA ---
const dataWilayah = {
    "BANCAR": ["Jatisari", "Kayen", "Sukoharjo", "Sidomulyo", "Cingklung", "Margosuko", "Ngampelrejo", "Pugoh", "Karangrejo", "Sumberan", "Siding", "Tengger Klon", "Ngujuran", "Tlogoagung", "Latsari", "Sukolilo", "Bulujowo", "Bulumeduro", "Banjarejo", "Tergambang", "Sembungin", "Boncong", "Bogorejo", "Bancar"],
    "BANGILAN": ["Klakeh", "Bate", "Kablukan", "Ngrojo", "Weden", "Sidokumpul", "Sidotentrem", "Bangilan", "Kedunghardjo", "Kedungmulyo", "Banjarworo", "Sidodadi", "Kedungjambangan", "Kumpulrejo"],
    "GRABAGAN": ["Ngarum", "Ngrejeng", "Banyubang", "Grabagan", "Waleran", "Gesikan", "Ngandong", "Dahor", "Dermawuhhardjo", "Menyunyur", "Pakis"],
    "JATIROGO": ["Karangtengah", "Jombok", "Wotsogo", "Sidomulyo", "Jatiklabang", "Dingil", "Demit", "Sugihan", "Sadang", "Bader", "Paseyan", "Kebonharjo", "Wangi", "Ketodan", "Besowo", "Ngepon", "Kedungmakam", "Sekaran"],
    "JENU": ["Karangasem", "Socorejo", "Temaji", "Purworejo", "Tasikharjo", "Remen", "Mentoso", "Rawasan", "Sumurgeneng", "Wadung", "Kaliuntu", "Beji", "Suwalan", "Jenggolo", "Sekardadi", "Jenu", "Sugihwaras"],
    "KENDURUAN": ["Sokogunung", "Jamprong", "Bendonglateng", "Sidorejo", "Sokogrenjeng", "Sidohasri", "Sidomukti", "Tawaran", "Jlodro"],
    "KEREK": ["Gemulung", "Wolutengah", "Trantang", "Sidonganti", "Tengger Wetan", "Hargoretno", "Temayang", "Padasan", "Karanglo", "Sumberarum", "Margomulyo", "Jarorejo", "Margorejo", "Gaji", "Kedungrejo", "Kasiman", "Mliwang"],
    "MERAKURAK": ["Kapu", "Tegalrejo", "Tahulu", "Mandirejo", "Bogorejo", "Sumberejo", "Sendanghaji", "Sambonggede", "Sumber", "Tuwiri Wetan", "Tuwiri Kulon", "Borehbangle", "Senori", "Sembungrejo", "Pongpongan", "Temandang", "Tlogowaru", "Tobo", "Sugihan"],
    "MONTONG": ["Manjung", "Tanggulangin", "Sumurgung", "Bringin", "Maindu", "Jetak", "Talun", "Pucangan", "Pakel", "Montongsekar", "Talangkembar", "Nguluhan", "Guwoterus"],
    "PALANG": ["Ngimbang", "Wangun", "Ketambul", "Cepokorejo", "Pliwetan", "Karangagung", "Leran Wetan", "Leran Kulon", "Glodog", "Palang", "Gesikharjo", "Pucangan", "Cendoro", "Dawung", "Tegalbang", "Sumurgung", "Kradenan", "Tasikmadu", "Panyuran"],
    "PARENGAN": ["Kemlaten", "Mergoasri", "Kumpulrejo", "Cengkong", "Brangkal", "Mergorejo", "Selogabus", "Sendangrejo", "Mojomalang", "Sugihwaras", "Suciharjo", "Pacing", "Parangbatu", "Sukorejo", "Sembung", "Ngawun", "Wukirharjo", "Dagangan"],
    "PLUMPANG": ["Trutup", "Kesamben", "Kepohagung", "Kedungrojo", "Cangkring", "Sembungrejo", "Plandirejo", "Bandungrejo", "Klotok", "Kebomlati", "Kedungsoko", "Penidon", "Magersari", "Jatimulyo", "Plumpang", "Sumurjalak", "Ngrayung", "Sumberagung"],
    "RENGEL": ["Kebonagung", "Bulurejo", "Karangtinoto", "Tambakrejo", "Kanorejo", "Ngadirejo", "Sumberejo", "Campurejo", "Banjararum", "Prambon Wetan", "Banjaragung", "Punggulrejo", "Rengel", "Sawahan", "Maibit", "Pekuwon"],
    "SEMANDING": ["Ngino", "Bektiharjo", "Sambongrejo", "Genaharjo", "Gesing", "Tunah", "Kowang", "Penambangan", "Semanding", "Prunggahan Wetan", "Prunggahan Kulon", "Jadi", "Boto", "Tegalagung", "Bejagung", "Gedongombo", "Karang"],
    "SENORI": ["Banyuurip", "Wonosari", "Katerban", "Rayung", "Sidoharjo", "Wanglu Wetan", "Wanglu Kulon", "Leran", "Kaligede", "Jatisari", "Medalem", "Sendang"],
    "SINGGAHAN": ["Binangun", "Saringembat", "Kedungjambe", "Tunggulrejo", "Tanjungrejo", "Lajo Kidul", "Tanggir", "Mergosari", "Mulyorejo", "Tingkis", "Mulyoagung", "Lajo Lor"],
    "SOKO": ["Menilo", "Simo", "Kendalrejo", "Mojoagung", "Pandanwangi", "Glagahsari", "Kenongosari", "Sandingrowo", "Rahayu", "Sokosari", "Bangunrejo", "Mentoro", "Pandanagung", "Prambontergayang", "Jati", "Cekalang", "Tluwe", "Wadung", "Klumpit", "Jegulo", "Sumurcinde", "Nguruan", "Gununganyar"],
    "TAMBAKBOYO": ["Nguluhan", "Dikir", "Mander", "Plajan", "Belikanget", "Cokrowati", "Sotang", "Pulogede", "Gadon", "Pabeyan", "Tambakboyo", "Klutuk", "Dasin", "Kenanti", "Sobontoro", "Sawir", "Merkawang", "Glondonggede"],
    "TUBAN": ["Sumurgung", "Sugiharjo", "Kembangbilo", "Mondokan", "Perbon", "Latsari", "Sidorejo", "Doromukti", "Kebonsari", "Sukolilo", "Baturetno", "Sendangharjo", "Kutorejo", "Sidomulyo", "Ronggomulyo", "Kingking", "Karangsari"],
    "WIDANG": ["Patihan", "Ngadipuro", "Ngadirejo", "Bunut", "Widang", "Compreng", "Banjar", "Tegalsari", "Kedungharjo", "Tegalrejo", "Simorejo", "Mrutuk", "Minohorejo", "Sumberejo", "Mlangi", "Kujung"]
};

window.initTaskCreate = function() {
    const inputTanggal = document.getElementById('tanggal_pelaksanaan');
    const kecSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');

    if (!kecSelect || !inputTanggal || !desaSelect) return;

    // --- A. LOGIKA KECAMATAN & DESA ---
    const updateDesa = (selectedKec, preselectDesa = '') => {
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        if (selectedKec && dataWilayah[selectedKec]) {
            desaSelect.disabled = false;
            [...dataWilayah[selectedKec]].sort().forEach(desa => {
                const option = document.createElement('option');
                option.value = desa;
                option.text = desa;
                if (preselectDesa === desa) { option.selected = true; }
                desaSelect.add(option);
            });
        } else {
            desaSelect.disabled = true;
        }
    };

    // Re-bind listeners correctly for SWUP
    kecSelect.onchange = function() { updateDesa(this.value); };

    // Initial populate if value exists
    if (kecSelect.value) { 
        updateDesa(kecSelect.value, window.initialDesa || ''); 
    }

    // --- B. LOGIKA TANGGAL & VALIDASI ---
    inputTanggal.onchange = function() {
        const tglTerpilih = this.value;
        if (!tglTerpilih) return;

        // Cek Cuti
        let isCuti = false;
        let rangeCutiText = "";
        (window.userCuti || []).forEach(range => {
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
            this.value = '';
            return;
        }

        // Cek Jadwal Bentrok
        const cleanLaporanTerpakai = (window.laporanTerpakai || []).map(tgl => tgl.substring(0, 10));
        if (cleanLaporanTerpakai.includes(tglTerpilih)) {
            Swal.fire({
                title: 'Jadwal Bentrok!',
                text: 'Tanggal tersebut sudah Anda gunakan untuk melaporkan penugasan lain.',
                icon: 'warning',
                confirmButtonColor: '#f59e0b'
            });
            this.value = '';
        }
    };
};

// Initial run
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    window.initTaskCreate();
} else {
    document.addEventListener('DOMContentLoaded', window.initTaskCreate);
}
