@php
    \Carbon\Carbon::setLocale('id');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir - {{ $meeting->title }}</title>
    <style>
        {!! file_get_contents(resource_path('css/pages/pdf-presensi.css')) !!}
    </style>
</head>
<body>
    <div class="header">
        <h3>BADAN PUSAT STATISTIK KABUPATEN TUBAN</h3>
        <h2>DAFTAR HADIR PESERTA {{ $meeting->activity_type_id == 2 ? 'RAPAT' : 'KEGIATAN' }}</h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="20%">Nama Kegiatan</td>
            <td width="2%">:</td>
            <td width="78%" class="fw-bold">{{ $meeting->title }}</td>
        </tr>
        <tr>
            <td>Hari / Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($meeting->event_date)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td>Pimpinan / Penanggung Jawab</td>
            <td>:</td>
            <td>{{ $meeting->pimpinan_rapat ?? ($meeting->creator->nama_lengkap ?? 'Admin') }}</td>
        </tr>
    </table>

    <table class="daftar-hadir">
        <thead>
            <tr>
                <th width="7%">No</th>
                <th width="40%">Nama Lengkap / NIP</th>
                <th width="23%">Jabatan / Tim</th>
                <th width="30%">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $index => $p)
            @php
                $presensiBarisIni = $dataPresensi->get($p->id);
            @endphp
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>
                    <div class="fw-bold" style="font-size: 9pt;">{{ $p->assignee->nama_lengkap }}</div>
                    <div style="font-size: 8pt; color: #333;">NIP. {{ $p->assignee->nip ?? '-' }}</div>
                </td>
                <td align="center" style="font-size: 9pt;">
                    {{ $p->assignee->team->nama_tim ?? 'Staf' }}
                </td>
                <td align="center" style="height: 60px;">
                    @if($presensiBarisIni && !empty($presensiBarisIni->signature_base64))
                        @php
                            $data = trim($presensiBarisIni->signature_base64);
                            if (strpos($data, ',') !== false) {
                                $data = explode(',', $data)[1];
                            }
                            $data = str_replace(["\r", "\n", ' '], '', $data);
                            $cleanImage = 'data:image/png;base64,' . $data;
                        @endphp
                        <img src="{!! $cleanImage !!}" class="signature-img-row">
                    @else
                        <span style="color: #bbb; font-size: 7pt; font-style: italic;">(Tidak Hadir)</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            {{-- KIRI: MENGETAHUI KEPALA --}}
            <td>
                <p>Mengetahui,</p>
                <p style="margin-bottom: 0;">Kepala BPS Kabupaten Tuban</p>
                
                <div class="signature-space">
                    {{-- Jika ada kolom ttd_image di tabel users --}}
                    @if($kepala && $kepala->ttd_image)
                        <img src="{{ public_path('storage/' . $kepala->ttd_image) }}" class="signature-pejabat">
                    @endif
                </div>
                
                <span class="underline">{{ $kepala->nama_lengkap ?? 'NAMA KEPALA BPS, M.Si' }}</span>
                <div style="font-size: 9pt; margin-top: 5px;">
                    NIP. {{ $kepala->nip ?? '19700000 000000 0 000' }}
                </div>
            </td>

            {{-- KANAN: PEMBUAT DAFTAR (ADMIN / KATIM) --}}
            <td>
                <p>Tuban, {{ \Carbon\Carbon::parse($meeting->event_date)->translatedFormat('d F Y') }}</p>
                <p style="margin-bottom: 0;">Pembuat Daftar,</p>
                
                <div class="signature-space">
                    @if($meeting->creator && $meeting->creator->ttd_image)
                        <img src="{{ public_path('storage/' . $meeting->creator->ttd_image) }}" class="signature-pejabat">
                    @endif
                </div>
                
                <span class="underline">{{ $meeting->creator->nama_lengkap }}</span>
                <div style="font-size: 9pt; margin-top: 5px;">
                    NIP. {{ $meeting->creator->nip ?? '-' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>