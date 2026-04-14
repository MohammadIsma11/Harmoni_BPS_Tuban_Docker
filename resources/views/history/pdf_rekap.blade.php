<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ public_path('css/pages/history-pdf.css') }}">
</head>
<body>

    @php \Carbon\Carbon::setLocale('id'); @endphp

    <table class="kop-table">
        <tr>
            <td width="60">
                <img src="{{ public_path('img/logo-bps.png') }}" width="60">
            </td>
            <td class="header-text">
                <h1>BADAN PUSAT STATISTIK</h1>
                <h2>KABUPATEN TUBAN</h2>
                <p>LAPORAN SEMUA KEGIATAN PENGAWASAN LAPANGAN</p>
            </td>
            <td width="60"></td> </tr>
    </table>

    <div class="meta">
        Digenerate: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} &bull; Total Laporan: {{ $riwayat->count() }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Pegawai & Tanggal</th>
                <th width="30%">Kegiatan & Lokasi</th>
                <th width="45%">Detail Laporan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $key => $l)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>
                    <strong>{{ $l->assignee->nama_lengkap }}</strong><br>
                    <span style="font-size: 7.5pt;">{{ \Carbon\Carbon::parse($l->event_date)->translatedFormat('l, d/m/Y') }}</span>
                </td>
                <td>
                    <strong>{{ $l->title }}</strong><br>
                    <small>Lokasi: {{ $l->location }}</small>
                </td>
                <td class="bg-gray">
                    <span class="label">AKTIVITAS:</span>
                    <div style="margin-bottom: 8px;">{{ $l->aktivitas }}</div>
                    
                    <span class="label">PERMASALAHAN:</span>
                    <div>{{ $l->permasalahan }}</div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-wrapper">
        <div class="footer-sign">
            <p>Tuban, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <br><br><br>
            <p><strong>( ________________ )</strong></p>
            <p>Kepala BPS Tuban</p>
        </div>
    </div>

</body>
</html>             