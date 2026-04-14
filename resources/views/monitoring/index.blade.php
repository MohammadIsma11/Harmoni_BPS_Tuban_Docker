@php
    \Carbon\Carbon::setLocale('id');
    $viewMode = request('view_mode', 'month');
    $startDate = \Carbon\Carbon::create($year, $month, 1);
    
    if($viewMode == 'week') {
        $startDate = \Carbon\Carbon::parse(request('week_start', now()->startOfWeek()->format('Y-m-d')));
        $daysToShow = 7;
    } else {
        $daysToShow = $daysInMonth;
    }
@endphp

@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/pages/monitoring-index.css') }}">

<div class="container-fluid px-4">
    <div class="card monitoring-card shadow-sm mb-4">
        <div class="card-body p-4">
            
            <div class="row align-items-center mb-4">
                <div class="col-xl-3 col-lg-12 mb-3 mb-xl-0">
                    <div class="d-flex align-items-center mb-1">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Timeline Monitoring</h4>
                    </div>
                    <p class="text-muted small mb-0">Visualisasi beban kerja personil.</p>
                </div>
                
                <div class="col-xl-9 col-lg-12">
                    <div class="d-flex flex-wrap justify-content-xl-end gap-3 align-items-center">
                        <div class="btn-group shadow-sm p-1 bg-light rounded-3">
                            <a href="{{ route('monitoring.index', ['view_mode' => 'week', 'month' => $month, 'year' => $year]) }}" 
                               class="btn view-filter-btn {{ $viewMode == 'week' ? 'active' : '' }}">Mingguan</a>
                            <a href="{{ route('monitoring.index', ['view_mode' => 'month', 'month' => $month, 'year' => $year]) }}" 
                               class="btn view-filter-btn {{ $viewMode == 'month' ? 'active' : '' }}">Bulanan</a>
                        </div>

                        {{-- Legend Warna - TAMBAH DINAS LUAR --}}
                        <div class="d-flex gap-3 px-3 border-end border-start d-none d-sm-flex">
                            <div class="legend-item"><div class="legend-color pill-tugas" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Tugas</small></div>
                            <div class="legend-item"><div class="legend-color pill-rapat" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Rapat</small></div>
                            <div class="legend-item"><div class="legend-color pill-dinas" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Dinas Luar</small></div>
                            <div class="legend-item"><div class="legend-color pill-selesai" style="width:10px;height:10px;border-radius:3px;display:inline-block;margin-right:5px;"></div> <small>Selesai</small></div>
                        </div>

                        <form action="{{ route('monitoring.index') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                            <div class="input-group input-group-sm shadow-sm">
                                <select name="month" class="form-select fw-bold border-0 bg-light">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="year" class="form-select fw-bold border-0 bg-light">
                                    @for($y = date('Y')-1; $y <= date('Y')+1; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                <button type="submit" class="btn btn-primary px-3"><i class="fas fa-filter"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive scrollbar-custom border shadow-sm rounded-4">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="sticky-name-col py-4 text-center text-uppercase small fw-bold">Petugas Pelaksana</th>
                            @for($i = 0; $i < $daysToShow; $i++)
                                @php 
                                    $dt = $startDate->copy()->addDays($i);
                                    $isWeekend = $dt->isWeekend();
                                    $isToday = $dt->isToday();
                                @endphp
                                <th class="text-center py-3 {{ $isWeekend ? 'weekend-cell text-danger' : 'text-primary' }} {{ $isToday ? 'today-cell' : '' }}" style="min-width: 55px;">
                                    <span class="d-block h6 fw-bold mb-0">{{ $dt->format('d') }}</span>
                                    <small class="fw-bold text-uppercase" style="font-size: 0.55rem;">{{ $dt->translatedFormat('D') }}</small>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="sticky-name-col px-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-init me-3 shadow-sm">
                                        {{ strtoupper(substr($user->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.85rem;">{{ $user->nama_lengkap }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $user->team->nama_tim ?? 'Internal' }}</small>
                                    </div>
                                </div>
                            </td>

                            @for($i = 0; $i < $daysToShow; $i++)
                                @php
                                    $dtCheck = $startDate->copy()->addDays($i);
                                    $currentDateStr = $dtCheck->format('Y-m-d');
                                    
                                    $agenda = $user->agendas->first(function($a) use ($currentDateStr) {
                                        return $currentDateStr >= $a->event_date->format('Y-m-d') && $currentDateStr <= $a->end_date->format('Y-m-d');
                                    });
                                    $isWeekend = $dtCheck->isWeekend();
                                    $isToday = $dtCheck->isToday();
                                @endphp
                                <td class="day-cell {{ $isWeekend ? 'weekend-cell' : '' }} {{ $isToday ? 'today-cell' : '' }}">
                                    @if($agenda)
                                        @php
                                            // LOGIKA WARNA & ICON BARU
                                            if($agenda->status_laporan == 'Selesai') {
                                                $pillClass = 'pill-selesai';
                                                $icon = 'fa-check';
                                            } elseif($agenda->activity_type_id == 2) {
                                                $pillClass = 'pill-rapat';
                                                $icon = 'fa-users';
                                            } elseif($agenda->activity_type_id == 3) {
                                                $pillClass = 'pill-dinas'; // Kategori baru
                                                $icon = 'fa-plane-departure';
                                            } else {
                                                $pillClass = 'pill-tugas';
                                                $icon = 'fa-briefcase';
                                            }

                                            // Label Detail
                                            $tipeMap = [1 => 'Tugas', 2 => 'Rapat', 3 => 'Dinas Luar'];
                                            $labelTipe = $tipeMap[$agenda->activity_type_id] ?? 'Kegiatan';
                                            $namaTim = $agenda->creator && $agenda->creator->team ? $agenda->creator->team->nama_tim : "Umum";
                                            $asalPenugasan = $labelTipe . " dari " . $namaTim;
                                        @endphp
                                        <div class="agenda-pill {{ $pillClass }}" 
                                             onclick="showDetail('{{ $agenda->title }}', '{{ $agenda->location }}', '{{ $user->nama_lengkap }}', '{{ $agenda->status_laporan }}', '{{ $asalPenugasan }}')">
                                            <i class="fas {{ $icon }} shadow-sm" style="font-size: 0.7rem;"></i>
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Script tetap sama --}}
    <script src="{{ asset('js/pages/monitoring-index.js') }}"></script>
@endsection