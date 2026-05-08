<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPK - {{ $penugasan->no_spk }}</title>
    <style>
        /* Document Setup */
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; }
        
        body { 
            background: #e0e0e0; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            font-family: Arial, Helvetica, sans-serif; 
            color: #000;
        }

        /* Paper Style for Preview */
        .page {
            background: white;
            width: 21cm;
            min-height: 29.7cm;
            margin: 20px auto;
            padding: 2cm 2.5cm;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }

        /* Typography & Spacing */
        p { margin: 0; padding-bottom: 0px; line-height: 1.5; text-align: justify; font-size: 11pt; }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .fw-bold { font-weight: 700; }
        .text-uppercase { text-transform: uppercase; }
        
        .header { margin-bottom: 1.5rem; }
        .pasal { margin: 1rem 0 0.5rem 0; font-weight: bold; text-align: center; }
        
        ol { margin: 0; padding-left: 1.25rem; }
        li { text-align: justify; padding-bottom: 0.3rem; line-height: 1.4; }

        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        .table-bordered th, .table-bordered td { border: 1px solid black; padding: 6px; }

        .signature-grid { width: 100%; margin-top: 2rem; border: none; }
        .signature-grid td { width: 50%; vertical-align: top; text-align: center; border: none; padding: 0; }
        
        .page-num { position: absolute; top: 1.5cm; right: 2cm; font-size: 10pt; }

        /* Print Override */
        @media print {
            body { background: none; }
            .no-print { display: none; }
            .page { 
                margin: 0; 
                box-shadow: none; 
                width: 100%;
                min-height: auto;
                padding: 0; /* Let browser handle margins */
            }
            .page-break { page-break-after: always; }
        }

        /* Fixed Print Button */
        .btn-print-fixed {
            position: fixed; top: 20px; right: 20px; z-index: 1000;
            background: #0058a8; color: white; border: none; padding: 12px 24px;
            border-radius: 50px; font-weight: bold; cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: all 0.2s;
        }
        .btn-print-fixed:hover { transform: scale(1.05); background: #00427e; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn-print-fixed no-print">
        <i class="fas fa-file-pdf"></i> SIMPAN PDF / CETAK
    </button>

    <!-- HALAMAN 1 -->
    <div class="page page-break">
        <div class="header text-center">
            <h3 class="mb-0 fw-bold text-uppercase" style="font-size: 13pt;">PERJANJIAN KERJA</h3>
            <h3 class="mb-0 fw-bold text-uppercase" style="font-size: 13pt;">PETUGAS PENDATAAN LAPANGAN</h3>
            <h3 class="mb-0 fw-bold text-uppercase" style="font-size: 13pt;">PADA BADAN PUSAT STATISTIK KABUPATEN TUBAN</h3>
            <div class="fw-bold" style="font-size: 12pt;">NOMOR: {{ $penugasan->no_spk ?: '.../3523/SPK/'.Carbon\Carbon::now()->month.'/2026' }}</div>
        </div>

        @php \Carbon\Carbon::setLocale('id'); @endphp
        <p>Pada hari ini, <b>{{ Carbon\Carbon::parse($penugasan->tgl_mulai)->isoFormat('dddd') }}</b> tanggal <b>{{ Carbon\Carbon::parse($penugasan->tgl_mulai)->isoFormat('D') }}</b> bulan <b>{{ Carbon\Carbon::parse($penugasan->tgl_mulai)->isoFormat('MMMM') }}</b> tahun <b>Dua Ribu Dua Puluh Enam</b> bertempat di Kabupaten Tuban, yang bertanda tangan di bawah ini:</p>

        <table style="width: 100%; border: none; margin: 1rem 0;">
            <tr>
                <td style="width: 30px; vertical-align: top;">(1)</td>
                <td class="text-justify">{{ $katim->nama_lengkap ?? '.......................' }} : Badan Pusat Statistik Kabupaten Tuban, berkedudukan di Tuban, bertindak untuk dan atas nama Badan Pusat Statistik Jl. Dr. Wahidin Sudirohusodo No.8, Tuban, selanjutnya disebut sebagai <b>PIHAK PERTAMA</b>.</td>
            </tr>
            <tr style="height: 10px;"><td></td><td></td></tr>
            <tr>
                <td style="width: 30px; vertical-align: top;">(2)</td>
                <td class="text-justify">{{ $penugasan->mitra->nama_lengkap }} : Petugas Pendataan Lapangan, berkedudukan di {{ $penugasan->lokasi_tugas ?? 'Kabupaten Tuban' }}, bertindak untuk dan atas nama diri sendiri, selanjutnya disebut sebagai <b>PIHAK KEDUA</b>.</td>
            </tr>
        </table>

        <p>bahwa <b>PIHAK PERTAMA</b> dan <b>PIHAK KEDUA</b> yang secara bersama-sama disebut <b>PARA PIHAK</b>, sepakat untuk mengikatkan diri dalam Perjanjian Kerja Petugas Pendataan Lapangan pada Badan Pusat Statistik Kabupaten Tuban Nomor: {{ $penugasan->no_spk ?: '.......................' }}, yang selanjutnya disebut Perjanjian, dengan ketentuan-ketentuan sebagai berikut:</p>

        <div class="pasal">Pasal 1</div>
        <p><b>PIHAK PERTAMA</b> memberikan pekerjaan kepada <b>PIHAK KEDUA</b> dan <b>PIHAK KEDUA</b> menerima pekerjaan dari <b>PIHAK PERTAMA</b> sebagai Petugas Pendataan Lapangan pada Badan Pusat Statistik Kabupaten Tuban, dengan lingkup pekerjaan yang ditetapkan oleh <b>PIHAK PERTAMA</b>.</p>

        <div class="pasal">Pasal 2</div>
        <p>Ruang lingkup pekerjaan dalam Perjanjian ini mengacu pada wilayah kerja dan beban kerja sebagaimana tertuang dalam lampiran Perjanjian, dan ketentuan-ketentuan lainnya yang ditetapkan oleh <b>PIHAK PERTAMA</b>.</p>

        <div class="pasal">Pasal 3</div>
        <p>Jangka waktu Perjanjian ini terhitung sejak <b>1 Januari 2026</b> sampai dengan <b>31 Desember 2026</b>.</p>
    </div>

    <!-- HALAMAN 2 -->
    <div class="page page-break">
        <div class="page-num">2</div>
        <div class="pasal">Pasal 4</div>
        <ol>
            <li><b>PIHAK KEDUA</b> berkewajiban menyelesaikan pekerjaan yang diberikan oleh <b>PIHAK PERTAMA</b> sesuai ruang lingkup pekerjaan sebagaimana dimaksud dalam Pasal 2.</li>
            <li><b>PIHAK KEDUA</b> untuk waktu yang tidak terbatas dan/atau tidak terikat kepada masa berlakunya Perjanjian ini, menjamin untuk memperlakukan sebagai rahasia setiap data/informasi yang diterima atau diperolehnya dari <b>PIHAK PERTAMA</b>, serta menjamin bahwa keterangan demikian hanya dipergunakan untuk melaksanakan tujuan menurut Perjanjian ini.</li>
        </ol>

        <div class="pasal">Pasal 5</div>
        <ol>
            <li><b>PIHAK KEDUA</b> apabila melakukan peminjaman dokumen/data/aset milik <b>PIHAK PERTAMA</b>, wajib menjaga dan menggunakan sesuai dengan tujuan perjanjian dan mengembalikan dalam keadaan utuh sama dengan saat peminjaman, serta dilarang menggandakan, menyalin, menunjukkan, dan/atau mendokumentasikan dalam bentuk foto atau bentuk apapun untuk kepentingan pribadi ataupun kepentingan lain yang tidak berkaitan dengan tujuan perjanjian ini.</li>
            <li><b>PIHAK KEDUA</b> dilarang memberikan dokumen/data/aset milik <b>PIHAK PERTAMA</b> yang berada dalam penguasaan <b>PIHAK KEDUA</b>, baik secara langsung maupun tidak langsung, termasuk memberikan akses kepada pihak lain untuk menggunakan, menyalin, memfotokopi, menunjukkan, dan/atau mendokumentasikan dalam bentuk foto atau bentuk apapun, sehingga informasi diketahui oleh pihak lain untuk tujuan apapun.</li>
        </ol>

        <div class="pasal">Pasal 6</div>
        <ol>
            <li><b>PIHAK KEDUA</b> berhak untuk mendapatkan honorarium dari <b>PIHAK PERTAMA</b> sesuai ketentuan yang berlaku dan anggaran yang tersedia untuk pekerjaan sebagaimana dimaksud dalam Pasal 2, termasuk biaya pajak dan bea meterai.</li>
            <li>Honorarium sebagaimana dimaksud pada ayat (1) dibayarkan oleh <b>PIHAK PERTAMA</b> kepada <b>PIHAK KEDUA</b> setelah menyelesaikan seluruh pekerjaan yang ditargetkan sebagaimana tercantum dalam lampiran Perjanjian, dituangkan dalam Berita Acara Serah Terima Hasil Pekerjaan.</li>
        </ol>

        <div class="pasal">Pasal 7</div>
        <ol>
            <li>Pembayaran honorarium sebagaimana dimaksud dalam Pasal 6, dilakukan setelah <b>PIHAK KEDUA</b> menyelesaikan dan menyerahkan hasil pekerjaan sesuai dengan kegiatan pekerjaan yang dilakukan sebagaimana dimaksud dalam Pasal 2 kepada <b>PIHAK PERTAMA</b>.</li>
            <li>Pembayaran sebagaimana dimaksud pada ayat (1) dilakukan oleh <b>PIHAK PERTAMA</b> kepada <b>PIHAK KEDUA</b> sesuai dengan ketentuan peraturan perundang-undangan.</li>
        </ol>
    </div>

    <!-- HALAMAN 3 -->
    <div class="page page-break">
        <div class="page-num">3</div>
        <div class="pasal">Pasal 8</div>
        <ol style="list-style-type: lower-alpha;">
            <li><b>PIHAK PERTAMA</b> secara berjenjang melakukan pemeriksaan dan evaluasi atas target penyelesaian dan kualitas hasil pekerjaan yang dilaksanakan oleh <b>PIHAK KEDUA</b>.</li>
            <li>Hasil pemeriksaan dan evaluasi sebagaimana dimaksud pada ayat (1) menjadi dasar pembayaran honorarium <b>PIHAK KEDUA</b> oleh <b>PIHAK PERTAMA</b> sebagaimana dimaksud dalam Pasal 6 ayat (2), yang dituangkan dalam Berita Acara Serah Terima Hasil Pekerjaan yang ditandatangani oleh <b>PARA PIHAK</b>.</li>
        </ol>

        <div class="pasal">Pasal 9</div>
        <p><b>PIHAK PERTAMA</b> dapat memutuskan Perjanjian ini secara sepihak sewaktu-waktu dalam hal <b>PIHAK KEDUA</b> tidak dapat melaksanakan kewajibannya sebagaimana dimaksud dalam Pasal 4 dengan menerbitkan Surat Pemutusan Perjanjian Kerja.</p>

        <div class="pasal">Pasal 10</div>
        <ol>
            <li>Apabila <b>PIHAK KEDUA</b> mengundurkan diri dengan tidak menyelesaikan pekerjaan sebagaimana dimaksud dalam Pasal 2, maka akan diberikan sanksi oleh <b>PIHAK PERTAMA</b>, sebagai berikut:
                <ol style="list-style-type: decimal; padding-left: 20px;">
                    <li>mengundurkan diri setelah pelatihan dan belum mengikuti kegiatan diberikan sanksi sebesar biaya pelatihan.</li>
                    <li>mengundurkan diri pada saat pelaksanaan pekerjaan, diberikan sanksi tidak diberikan honorarium atas pekerjaan yang telah dilaksanakan.</li>
                </ol>
            </li>
            <li>Dikecualikan tidak dikenakan sanksi sebagaimana dimaksud pada ayat (1) oleh <b>PIHAK PERTAMA</b>, apabila <b>PIHAK KEDUA</b> meninggal dunia, mengundurkan diri karena sakit dengan keterangan rawat inap, kecelakaan dengan keterangan kepolisian, dan/atau telah diberikan Surat Pemutusan Perjanjian Kerja dari <b>PIHAK PERTAMA</b>.</li>
            <li>Dalam hal terjadi peristiwa sebagaimana dimaksud pada ayat (2), <b>PIHAK PERTAMA</b> membayarkan honorarium kepada <b>PIHAK KEDUA</b> secara proporsional sesuai pekerjaan yang telah dilaksanakan.</li>
        </ol>
        
        <div class="pasal">Pasal 11</div>
        <ol>
            <li>Apabila terjadi Keadaan Kahar, yang meliputi bencana alam, bencana nonalam, dan bencana sosial, <b>PIHAK KEDUA</b> memberitahukan kepada <b>PIHAK PERTAMA</b> dalam waktu paling lambat 14 (empat belas) hari sejak mengetahui atas kejadian Keadaan Kahar dengan menyertakan bukti.</li>
            <li>Dalam hal terjadi peristiwa sebagaimana dimaksud pada ayat (1) pelaksanaan pekerjaan oleh <b>PIHAK KEDUA</b> dihentikan sementara dan dilanjutkan kembali setelah Keadaan Kahar berakhir, merujuk pada ketentuan yang ditetapkan oleh <b>PIHAK PERTAMA</b>.</li>
        </ol>
    </div>

    <!-- HALAMAN 4 -->
    <div class="page page-break">
        <div class="page-num">4</div>
        <ol start="3">
            <li>Apabila akibat Keadaan Kahar tidak memungkinkan dilanjutkan/diselesaikannya pelaksanaan pekerjaan, <b>PIHAK KEDUA</b> berhak menerima honorarium secara proporsional sesuai pekerjaan yang telah diselesaikan dan diterima oleh <b>PIHAK PERTAMA</b>.</li>
        </ol>

        <div class="pasal">Pasal 12</div>
        <p>Hal-hal yang belum diatur dalam Perjanjian ini atau segala perubahan terhadap Perjanjian ini diatur lebih lanjut oleh <b>PARA PIHAK</b> dalam perjanjian tambahan/adendum dan merupakan bagian tidak terpisahkan dari Perjanjian ini.</p>

        <div class="pasal">Pasal 13</div>
        <ol>
            <li>Segala perselisihan atau perbedaan pendapat yang mungkin timbul sebagai akibat dari Perjanjian ini, diselesaikan secara musyawarah untuk mufakat oleh <b>PARA PIHAK</b>.</li>
            <li>Apabila musyawarah untuk mufakat sebagaimana dimaksud pada ayat (1) tidak berhasil, maka <b>PARA PIHAK</b> sepakat untuk menyelesaikan perselisihan dengan memilih kedudukan/domisili hukum di Kepaniteraan Pengadilan Negeri Kabupaten Tuban.</li>
            <li>Selama perselisihan dalam proses penyelesaian pengadilan, <b>PIHAK PERTAMA</b> dan <b>PIHAK KEDUA</b> wajib tetap melaksanakan kewajiban masing-masing berdasarkan Perjanjian ini.</li>
        </ol>

        <p style="margin-top: 1.5rem;">Demikian Perjanjian ini dibuat dan ditandatangani oleh <b>PARA PIHAK</b> dalam 2 (dua) rangkap asli bermeterai cukup, tanpa paksaan dari PIHAK manapun dan untuk dilaksanakan oleh <b>PARA PIHAK</b>.</p>

        <table class="signature-grid">
            <tr>
                <td style="text-align: left; padding-left: 1cm;">
                    <p class="fw-bold text-uppercase">PIHAK KEDUA,</p>
                    <div style="height: 2.5cm;"></div>
                    <p class="text-uppercase">{{ $penugasan->mitra->nama_lengkap }}</p>
                </td>
                <td style="text-align: right; padding-right: 1cm;">
                    <div style="display: inline-block; text-align: center;">
                        <p class="fw-bold text-uppercase text-center">PIHAK PERTAMA,</p>
                        <div style="height: 2.5cm;"></div>
                        <p class="text-uppercase text-center">{{ $katim->nama_lengkap ?? '.......................' }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- HALAMAN 5 (LAMPIRAN) -->
    <div class="page">
        <div class="header text-center">
            <h3 class="mb-0 fw-bold text-uppercase">LAMPIRAN I</h3>
            <h3 class="mb-0 fw-bold text-uppercase">PERJANJIAN KERJA PETUGAS PENDATAAN LAPANGAN</h3>
            <h3 class="mb-0 fw-bold text-uppercase">PADA BADAN PUSAT STATISTIK KABUPATEN TUBAN</h3>
            <div class="fw-bold">NOMOR: {{ $penugasan->no_spk ?: '.......................' }}</div>
        </div>

        <p class="fw-bold text-uppercase" style="font-size: 10pt; margin-top: 1rem;">DAFTAR URAIAN TUGAS, JANGKA WAKTU, TARGET PEKERJAAN DAN NILAI PERJANJIAN</p>

        <table class="table table-bordered text-center">
            <thead class="bg-light fw-bold text-uppercase">
                <tr>
                    <th style="width: 45%;">Uraian Tugas</th>
                    <th style="width: 20%;">Jangka Waktu</th>
                    <th>Volume</th>
                    <th>Satuan</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: left;">{{ $penugasan->nama_kegiatan_full }}</td>
                    <td>
                        {{ Carbon\Carbon::parse($penugasan->tgl_mulai)->format('d/m/Y') }} s.d <br>
                        {{ Carbon\Carbon::parse($penugasan->tgl_selesai_target)->format('d/m/Y') }}
                    </td>
                    <td>{{ $penugasan->volume }}</td>
                    <td>{{ $penugasan->satuan }}</td>
                    <td class="text-right">Rp {{ number_format($penugasan->total_honor_tugas, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="fw-bold text-right">TOTAL NILAI PERJANJIAN</td>
                    <td class="fw-bold text-right">Rp {{ number_format($penugasan->total_honor_tugas, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: left; background: #f9f9f9;">
                        <i># {{ $totalHonorTerbilang }} #</i>
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Signature section removed from Lampiran as per request --}}
    </div>
</body>
</html>
