@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Rekap Honor Dasar</h4>
                    <p class="text-muted small mb-0">Tahun Anggaran {{ $selectedYear }}</p>
                </div>
                <form action="{{ route('rekap-honor.index') }}" method="GET" class="d-flex gap-2">
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 75vh;">
                <table class="table table-bordered align-middle mb-0" id="main-table-rekap">
                    <thead>
                        <tr class="table-light">
                            <th class="stk-header stk-left-1">No</th>
                            <th class="stk-header stk-left-2">Nama Mitra</th>
                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $m)
                                <th class="text-center stk-header month-col">{{ $m }}</th>
                            @endforeach
                            <th class="text-end stk-header total-col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($mitras as $index => $m)
                            @php $rowTotal = 0; @endphp
                            <tr>
                                <td class="text-center stk-left-1 bg-white">{{ $index + 1 }}</td>
                                <td class="stk-left-2 bg-white">
                                    <div class="fw-bold small">{{ $m->nama_lengkap }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">{{ $m->sobat_id }}</div>
                                </td>
                                @foreach(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'] as $mNum)
                                    @php 
                                        $val = $pivotData[$m->sobat_id][$mNum] ?? 0;
                                        $rowTotal += $val;
                                        $isOverLimit = $val > 3200000;
                                    @endphp
                                    <td class="text-end cell-click month-col {{ $isOverLimit ? 'bg-danger bg-opacity-10 text-danger animate-pulse-soft' : '' }}" 
                                        data-sobat="{{ $m->sobat_id }}" 
                                        data-month="{{ $selectedYear }}-{{ $mNum }}" 
                                        data-name="{{ $m->nama_lengkap }}">
                                        @if($val > 0)
                                            <span class="small fw-bold">Rp {{ number_format($val, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted opacity-25">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-end fw-bold text-primary total-col">
                                    Rp {{ number_format($rowTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @php $grandTotal += $rowTotal; @endphp
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="2" class="text-center stk-left-1 bg-dark">TOTAL</th>
                            @for($i=0; $i<12; $i++) <th></th> @endfor
                            <th class="text-end total-col bg-dark">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- DETAIL AREA --}}
    <div id="detail-section" class="mt-4 d-none">
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Rincian: <span id="det-name" class="text-primary"></span></h6>
                <button class="btn-close" onclick="$('#detail-section').addClass('d-none')"></button>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-4">Kegiatan</th>
                            <th class="text-center">Tim</th>
                            <th class="text-center">Vol</th>
                            <th class="text-end">Honor</th>
                            <th class="text-end pe-4">Cair</th>
                        </tr>
                    </thead>
                    <tbody id="det-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* STICKY CONFIG */
    .table-responsive { position: relative; }
    #main-table-rekap { 
        border-collapse: separate; 
        border-spacing: 0; 
        min-width: 1400px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    #main-table-rekap th, #main-table-rekap td {
        padding: 8px 10px !important; /* Tighter padding */
        font-size: 0.7rem !important;  /* Smaller font */
        white-space: nowrap;
    }

    .stk-header {
        position: sticky !important;
        top: 0;
        z-index: 10;
        background-color: #f8fafc !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .stk-left-1 { position: sticky !important; left: 0; z-index: 20; width: 40px; }
    .stk-left-2 { position: sticky !important; left: 40px; z-index: 20; width: 220px; border-right: 2px solid #dee2e6 !important; }
    
    .month-col { width: 95px !important; min-width: 95px !important; }
    .total-col { width: 140px !important; min-width: 140px !important; background-color: #f8fafc !important; color: #0058a8 !important; }

    /* Intersection */
    thead th.stk-left-1 { z-index: 30; }
    thead th.stk-left-2 { z-index: 30; }

    .cell-click { cursor: pointer; transition: 0.1s; }
    .cell-click:hover { background-color: #f0f7ff !important; }

    /* ALERT PULSE */
    .animate-pulse-soft {
        animation: pulse-red 2s infinite;
    }
    @keyframes pulse-red {
        0% { background-color: rgba(220, 53, 69, 0.1); }
        50% { background-color: rgba(220, 53, 69, 0.2); }
        100% { background-color: rgba(220, 53, 69, 0.1); }
    }
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $('.cell-click').on('click', function() {
        const d = $(this).data();
        $('#detail-section').removeClass('d-none');
        $('#det-name').text(d.name);
        $('#det-body').html('<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>');
        
        $('html, body').animate({ scrollTop: $('#detail-section').offset().top - 100 }, 300);

        $.get("{{ route('rekap-honor.detail') }}", { sobat_id: d.sobat, month: d.month }, function(res) {
            let h = '';
            res.forEach(item => {
                h += `<tr>
                    <td class="ps-4 small">${item.kegiatan}</td>
                    <td class="text-center small">${item.tim}</td>
                    <td class="text-center small">${parseFloat(item.volume)}</td>
                    <td class="text-end small">Rp ${new Intl.NumberFormat('id-ID').format(item.total_honor_tugas)}</td>
                    <td class="text-end pe-4 small fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(item.nominal_cair_bulan_ini)}</td>
                </tr>`;
            });
            $('#det-body').html(h || '<tr><td colspan="5" class="text-center py-4">No data</td></tr>');
        });
    });
});
</script>
@endpush
@endsection
