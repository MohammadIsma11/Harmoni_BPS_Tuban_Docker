@php
    \Carbon\Carbon::setLocale('id');
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Hadir - {{ $meeting->title }}</title>
    <style>
        @page { margin: 1.5cm 2cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #111; 
            line-height: 1.4;
        }

        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
        }
        .header h3 { margin: 0; font-size: 11pt; font-weight: normal; text-transform: uppercase; }
        .header h2 { margin: 5px 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }

        .info-table { width: 100%; margin-bottom: 20px; border: none; }
        .info-table td { padding: 3px 0; vertical-align: top; border: none; }
        .fw-bold { font-weight: bold; }

        table.daftar-hadir { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        table.daftar-hadir th, table.daftar-hadir td { 
            border: 1px solid #000; 
            padding: 10px 8px; 
            vertical-align: middle;
        }
        table.daftar-hadir th { 
            background-color: #f2f2f2; 
            text-transform: uppercase; 
            font-size: 8pt; 
            font-weight: bold;
            text-align: center;
        }
        
        .signature-img-row { 
            max-height: 50px; 
            max-width: 100px;
            display: block; 
            margin: 0 auto; 
        }

        .signature-pejabat { 
            max-height: 70px; 
            display: block; 
            margin: 5px auto; 
        }
        
        .footer-table { 
            width: 100%; 
            margin-top: 30px; 
            border: none;
        }
        .footer-table td { 
            text-align: center; 
            border: none; 
            vertical-align: top;
            width: 50%;
        }
        .signature-space { height: 80px; }
        .underline { text-decoration: underline; text-transform: uppercase; font-weight: bold; }
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