<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAST - {{ $penugasan->no_bast }}</title>
    <style>
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; }
        body { background: #e0e0e0; margin: 0; padding: 0; display: flex; flex-direction: column; align-items: center; font-family: Arial, sans-serif; color: #000; }
        .page { background: white; width: 21cm; min-height: 29.7cm; margin: 20px auto; padding: 2cm 2.5cm; box-shadow: 0 0 15px rgba(0,0,0,0.3); position: relative; }
        p { margin: 0; padding-bottom: 10px; line-height: 1.5; text-align: justify; font-size: 11pt; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: 700; }
        .text-uppercase { text-transform: uppercase; }
        .header { margin-bottom: 1.5rem; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        .table-bordered th, .table-bordered td { border: 1px solid black; padding: 8px; }
        .signature-grid { width: 100%; margin-top: 2rem; border: none; }
        .signature-grid td { width: 50%; vertical-align: top; text-align: center; border: none; padding: 0; }
        @media print {
            body { background: none; }
            .no-print { display: none; }
            .page { margin: 0; box-shadow: none; width: 100%; padding: 1.5cm; }
        }
        .btn-print-fixed { position: fixed; top: 20px; right: 20px; z-index: 1000; background: #0058a8; color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn-print-fixed no-print">CETAK BAST</button>

    <div class="page">
        <div class="header text-center">
            <h3 class="mb-0 fw-bold text-uppercase">BERITA ACARA SERAH TERIMA HASIL PEKERJAAN</h3>
            <div class="fw-bold">NOMOR: {{ $penugasan->no_bast ?: '.../3523/BAST/'.Carbon\Carbon::now()->month.'/2026' }}</div>
        </div>

        @php \Carbon\Carbon::setLocale('id'); @endphp
        <p>Pada hari ini, <b>{{ Carbon\Carbon::now()->isoFormat('dddd') }}</b> tanggal <b>{{ Carbon\Carbon::now()->isoFormat('D') }}</b> bulan <b>{{ Carbon\Carbon::now()->isoFormat('MMMM') }}</b> tahun <b>Dua Ribu Dua Puluh Enam</b>, kami yang bertanda tangan di bawah ini:</p>

        <table style="width: 100%; border: none; margin-bottom: 1rem;">
            <tr>
                <td style="width: 30px; vertical-align: top;">1.</td>
                <td style="width: 120px;">Nama</td>
                <td>: <b>{{ $penugasan->mitra->nama_lengkap }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td>Sobat ID</td>
                <td>: {{ $penugasan->mitra_id }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">Selanjutnya disebut sebagai <b>PIHAK PERTAMA</b> (yang menyerahkan).</td>
            </tr>
            <tr style="height: 10px;"><td></td><td></td><td></td></tr>
            <tr>
                <td style="width: 30px; vertical-align: top;">2.</td>
                <td>Nama</td>
                <td>: <b>{{ $katim->nama_lengkap }}</b></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>: Ketua Tim / Pejabat Pembuat Komitmen</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">Selanjutnya disebut sebagai <b>PIHAK KEDUA</b> (yang menerima).</td>
            </tr>
        </table>

        <p><b>PIHAK PERTAMA</b> menyatakan telah menyelesaikan dan menyerahkan hasil pekerjaan kepada <b>PIHAK KEDUA</b>, dan <b>PIHAK KEDUA</b> menyatakan telah menerima hasil pekerjaan dari <b>PIHAK PERTAMA</b> sesuai dengan Perjanjian Kerja Nomor: {{ $penugasan->no_spk ?: '................' }} dengan rincian sebagai berikut:</p>

        <table class="table table-bordered text-center">
            <thead class="bg-light fw-bold text-uppercase">
                <tr>
                    <th>Uraian Tugas</th>
                    <th style="width: 80px;">Volume</th>
                    <th style="width: 80px;">Satuan</th>
                    <th style="width: 120px;">Nilai Honor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penugasans as $p)
                <tr>
                    <td style="text-align: left;">{{ $p->nama_kegiatan_full }}</td>
                    <td>{{ $p->volume }}</td>
                    <td>{{ $p->satuan }}</td>
                    <td style="text-align: right;">Rp {{ number_format($p->total_honor_tugas, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="fw-bold text-right">TOTAL NILAI</td>
                    <td class="fw-bold text-right">Rp {{ number_format($totalHonor, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <p style="margin-top: 1rem;">Demikian Berita Acara Serah Terima ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

        <table class="signature-grid">
            <tr>
                <td>
                    <p class="fw-bold">PIHAK PERTAMA,</p>
                    <div style="height: 2.5cm;"></div>
                    <p class="text-uppercase fw-bold">{{ $penugasan->mitra->nama_lengkap }}</p>
                </td>
                <td>
                    <p class="fw-bold">PIHAK KEDUA,</p>
                    <div style="height: 2.5cm;"></div>
                    <p class="text-uppercase fw-bold">{{ $katim->nama_lengkap }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
