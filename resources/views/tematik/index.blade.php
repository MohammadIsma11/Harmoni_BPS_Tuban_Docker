@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        {{-- HEADER NAVIGATION --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-map-marked-alt text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Monitoring Tematik SIG</h5>
                            <p class="text-muted small mb-0">Integrasi Data Spasial & Pelaporan Wilayah</p>
                        </div>
                    </div>
                    
                    <div class="nav nav-pills bg-light p-1 rounded-pill shadow-sm" id="tematik-main-tabs">
                        <button class="nav-link active rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#panel-sig">
                            <i class="fas fa-globe-asia me-2"></i> Peta SIG
                        </button>
                        <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#panel-info">
                            <i class="fas fa-database me-2"></i> Info Data
                        </button>
                        <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#panel-laporan">
                            <i class="fas fa-file-alt me-2"></i> Laporan
                        </button>
                        <button class="nav-link rounded-pill px-4 fw-bold" data-bs-toggle="tab" data-bs-target="#panel-form">
                            <i class="fas fa-plus-circle me-2"></i> Form Input
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content border-0">
            {{-- PANEL 1: SIG (MAP) --}}
            <div class="tab-pane fade show active" id="panel-sig">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative" style="height: 700px;">
                            {{-- MAP FILTERS OVERLAY --}}
                            <div class="position-absolute top-0 start-0 w-100 p-3 d-flex gap-2 align-items-start" style="z-index: 1000;">
                                <div class="bg-white bg-opacity-75 backdrop-blur p-2 rounded-4 shadow-sm border d-flex gap-2 flex-wrap flex-grow-1" style="max-width: 600px;">
                                    <select id="filterKecamatan" class="form-select form-select-sm rounded-3 border-0 bg-white" style="width: 150px;">
                                        <option value="">Kecamatan</option>
                                    </select>
                                    <select id="filterDesa" class="form-select form-select-sm rounded-3 border-0 bg-white" style="width: 150px;" disabled>
                                        <option value="">Pilih Desa</option>
                                    </select>
                                    <input type="text" id="map-search" class="form-control form-control-sm rounded-3 border-0 px-3 bg-white" placeholder="Cari wilayah..." style="flex: 1; min-width: 150px;">
                                    <button id="resetFilter" class="btn btn-white btn-sm rounded-3 border-0 shadow-sm"><i class="fas fa-undo"></i></button>
                                </div>
                                <div id="search-suggestions" class="position-absolute top-100 start-0 mt-2 bg-white shadow-lg rounded-4 overflow-hidden d-none border" style="width: 300px; margin-left: 330px;"></div>
                            </div>
                            
                            <div id="main-map" style="height: 100%; width: 100%;"></div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        {{-- DETAIL CARD --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-4 animate-up">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                                    <h6 class="fw-bold mb-0">Detail Titik Lokasi</h6>
                                    <span id="badge-status" class="badge rounded-pill bg-light text-muted">No Selection</span>
                                </div>
                                <div id="detail-placeholder" class="text-center py-5 text-muted">
                                    <i class="fas fa-map-marker-alt fa-3x mb-3 opacity-25"></i>
                                    <p class="small mb-0">Klik salah satu marker di peta untuk melihat detail laporan.</p>
                                </div>
                                <div id="detail-content" class="d-none">
                                    <h5 id="txt-title" class="fw-bold text-primary mb-3">-</h5>
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Tanggal</small>
                                            <span id="txt-date" class="fw-bold small">-</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Kecamatan</small>
                                            <span id="txt-kec" class="fw-bold small">-</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Pelapor</small>
                                            <span id="txt-pic" class="fw-bold small">-</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Status</small>
                                            <span id="txt-status-val" class="fw-bold small">-</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Desa/Kelurahan</small>
                                            <span id="txt-desa" class="fw-bold small">-</span>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted d-block mb-1">Satuan Lingkungan Setempat (RT/RW)</small>
                                            <span id="txt-sls" class="fw-bold small">-</span>
                                        </div>
                                    </div>
                                    <div class="bg-light p-3 rounded-4 border">
                                        <small class="text-muted d-block mb-2 text-uppercase fw-bold ls-1" style="font-size: 0.65rem;">Keterangan / Member</small>
                                        <p id="txt-members" class="small mb-0 text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- STATS SECTION --}}
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-4">Distribusi Data Wilayah</h6>
                                <div style="height: 250px;">
                                    <canvas id="pieChart"></canvas>
                                </div>
                                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="text-muted small">Total Lokasi</div>
                                        <div class="h4 fw-bold mb-0" id="stat-total-points">0</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted small">Update Terbaru</div>
                                        <div class="fw-bold small">{{ date('d M Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL 2: INFO DATA --}}
            <div class="tab-pane fade" id="panel-info">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white p-4 border-0">
                        <h5 class="fw-bold mb-0">Ringkasan Statistik per Kecamatan</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="bg-light text-muted small text-uppercase">
                                            <tr>
                                                <th class="ps-4 border-0">Kecamatan</th>
                                                <th class="border-0">Jumlah Titik</th>
                                                <th class="border-0 pe-4">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="info-table-body">
                                            {{-- Data via AJAX --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="bg-light rounded-4 p-4 h-100 border">
                                    <h6 class="fw-bold mb-4">Visualisasi Sebaran</h6>
                                    <div style="height: 350px;">
                                        <canvas id="infoBarChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL 3: LAPORAN --}}
            <div class="tab-pane fade" id="panel-laporan">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Daftar Histori Laporan Kegiatan</h5>
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="laporan-search" placeholder="Cari kegiatan...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Tanggal</th>
                                        <th>Kecamatan</th>
                                        <th>Pelapor</th>
                                        <th>Kegiatan</th>
                                        <th>Anggota</th>
                                        <th>Status</th>
                                        <th class="text-center pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="laporan-table-body">
                                    {{-- Data via AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL 4: FORM INPUT --}}
            <div class="tab-pane fade" id="panel-form">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-5 bg-primary bg-opacity-10 p-5 d-flex flex-column justify-content-center">
                                    <h3 class="fw-bold text-primary mb-3">Input Laporan Berbasis Wilayah</h3>
                                    <p class="text-muted mb-5">Gunakan kotak pencarian pada peta di sebelah kanan untuk menemukan lokasi kegiatan. Sistem akan mendeteksi detail RT/RW secara otomatis.</p>
                                    
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">1</div>
                                            <h6 class="fw-bold mb-0">Cari Lokasi/Koordinat</h6>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">2</div>
                                            <h6 class="fw-bold mb-0">Verifikasi Detail Wilayah</h6>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">3</div>
                                            <h6 class="fw-bold mb-0">Isi Detail & Simpan</h6>
                                        </div>
                                    </div>

                                    <div id="form-loc-card" class="bg-white p-3 rounded-4 border shadow-sm animate-up d-none">
                                        <div class="small text-muted mb-1"><i class="fas fa-map-marker-alt text-danger me-2"></i> Lokasi Terdeteksi:</div>
                                        <div id="form-loc-display" class="fw-bold text-primary small">-</div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body p-5">
                                        <form id="form-unified">
                                            <div class="mb-4">
                                                <div class="position-relative">
                                                    <div id="form-map" class="rounded-4 border overflow-hidden shadow-sm" style="height: 300px;"></div>
                                                    <div class="position-absolute top-0 start-0 m-3 w-75" style="z-index: 1000;">
                                                        <div class="input-group shadow-sm border rounded-3 overflow-hidden">
                                                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                                                            <input type="text" id="form-map-search" class="form-control border-0" placeholder="Cari lokasi/koordinat (lat, lng)...">
                                                        </div>
                                                        <div id="form-search-suggestions" class="bg-white shadow-lg rounded-3 mt-1 d-none border" style="max-height: 250px; overflow-y: auto; z-index: 1100; position: relative;"></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 px-1">
                                                    <small class="text-muted">Klik pada peta untuk menentukan titik presisi</small>
                                                    <button type="button" id="btn-recenter" class="btn btn-link btn-sm p-0 text-decoration-none">Recenter Tuban</button>
                                                </div>
                                                <div id="form-coord-info" class="mt-2 p-2 bg-light rounded-3 border d-none animate-up">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted fw-bold">KOORDINAT PRESISI:</small>
                                                        <code id="txt-latlng-small" class="text-primary fw-bold" style="font-size: 0.75rem;">-</code>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="fw-bold small text-muted mb-2 text-uppercase">2. Detail Kegiatan</label>
                                                <div class="form-group mb-3">
                                                    <input type="text" id="in-judul" class="form-control rounded-3 py-2 px-3" placeholder="Judul Kegiatan (Contoh: Audit Produksi Batik)" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label class="small text-muted mb-1 ps-1">Anggota Tim / Member (Bisa pilih lebih dari satu)</label>
                                                    <select id="in-member" class="form-control" multiple="multiple" style="width: 100%"></select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <input type="text" class="form-control rounded-3 py-2 px-3 bg-light" value="{{ Auth::user()->nama_lengkap }}" readonly>
                                                    <small class="text-muted ps-2">Pelapor Otomatis</small>
                                                </div>
                                            </div>

                                            <input type="hidden" id="in-lat">
                                            <input type="hidden" id="in-lng">
                                            <input type="hidden" id="in-kec">
                                            <input type="hidden" id="in-desa">
                                            <input type="hidden" id="in-sls">

                                            <button type="submit" id="btn-submit-report" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow" disabled>
                                                <i class="fas fa-save me-2"></i> SIMPAN LAPORAN KE PETA
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS & TOASTS --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 d-inline-block mb-3">
                    <i class="fas fa-trash-alt fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2">Hapus Laporan?</h5>
                <p class="text-muted small mb-4">Data koordinat dan histori kegiatan ini akan dihapus permanen dari sistem.</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-light w-100 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button id="confirm-delete" class="btn btn-danger w-100 rounded-pill fw-bold">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        padding: 4px 8px;
        min-height: 45px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--bps-blue);
        box-shadow: 0 0 0 0.25rem rgba(11, 40, 114, 0.1);
    }
    .select2-selection__choice {
        background-color: var(--bps-blue) !important;
        color: white !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 2px 10px !important;
        font-size: 0.8rem;
    }
    .select2-selection__choice__remove {
        color: white !important;
        margin-right: 5px !important;
    }
    .backdrop-blur { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
    .animate-up { animation: fadeInUp 0.4s ease-out; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .nav-pills .nav-link { 
        color: #64748b; font-size: 0.9rem; transition: all 0.3s;
        border: 1px solid transparent; margin: 0 2px;
    }
    .nav-pills .nav-link:hover { background: rgba(0,0,0,0.03); }
    .nav-pills .nav-link.active { background-color: var(--bps-blue) !important; color: white !important; box-shadow: 0 4px 12px rgba(11, 40, 114, 0.2); }
    
    .ls-1 { letter-spacing: 1px; }
    #main-map, #form-map { background: #f8fafc; cursor: crosshair; }
    .custom-label { 
        background: transparent !important; border: none !important; box-shadow: none !important; 
        font-weight: 800; font-size: 0.7rem; color: #1e293b;
        text-shadow: 1px 1px 2px white, -1px -1px 2px white, 0 0 5px white;
    }
    .custom-label::before { display: none !important; }
    .suggestion-item:hover { background-color: #f1f5f9; cursor: pointer; }
    
    .table thead th { font-weight: 700; border-bottom: 2px solid #f1f5f9; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // STATE
    let mainMap, formMap, geojsonLayer, markersLayer, formMarker;
    let slsData, kecData, desaData;
    let currentKec = '', currentDesa = '';
    let pieChart, barChart;

    // 1. INIT ALL MAPS
    function initMaps() {
        // MAIN MAP
        mainMap = L.map('main-map', { zoomControl: false }).setView([-6.89, 112.06], 11);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mainMap);
        L.control.zoom({ position: 'bottomright' }).addTo(mainMap);
        markersLayer = L.layerGroup().addTo(mainMap);

        // FORM MAP
        formMap = L.map('form-map', { zoomControl: false }).setView([-6.89, 112.06], 11);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(formMap);
        
        // Enable Click to select location
        formMap.on('click', function(e) {
            updateFormLocation(e.latlng.lat, e.latlng.lng);
        });
        
        // Use a shared function for both map and search updates
        window.updateFormLocation = function(lat, lng, forcedDetails = null) {
            console.log("Updating form location:", lat, lng, forcedDetails);
            let details = forcedDetails || findLocationDetails(lat, lng);
            
            if (!details.kec && !forcedDetails) {
                console.warn("Location outside boundaries.");
                Swal.fire({ icon: 'warning', title: 'Di Luar Batas', text: 'Silakan pilih lokasi di dalam wilayah Kabupaten Tuban.' });
                return;
            }

            const latlng = [lat, lng];
            if (formMarker) {
                formMarker.setLatLng(latlng);
            } else {
                formMarker = L.marker(latlng).addTo(formMap);
            }
            
            formMarker.bindPopup(`<b>Lokasi Terpilih</b><br>${details.kec}, ${details.desa || ''}`).openPopup();

            $('#in-lat').val(lat);
            $('#in-lng').val(lng);
            $('#in-kec').val(details.kec);
            $('#in-desa').val(details.desa || '');
            $('#in-sls').val(details.sls || '');
            
            // Update Coordinate Display below map
            $('#txt-latlng-small').text(`${lat.toFixed(7)}, ${lng.toFixed(7)}`);
            $('#form-coord-info').removeClass('d-none').show();

            let displayLoc = `${details.kec}`;
            if (details.desa) displayLoc += `, ${details.desa}`;
            if (details.sls) displayLoc += ` (${details.sls})`;
            
            $('#form-loc-display').html(`<b>${displayLoc}</b><br><small class="text-muted">${lat.toFixed(6)}, ${lng.toFixed(6)}</small>`);
            $('#form-loc-card').removeClass('d-none').show().addClass('animate-up');
            $('#btn-submit-report').prop('disabled', false);
        };

        // Load Assets
        Promise.all([
            fetch('{{ asset("geojson/kecamatan.geojson") }}').then(r => r.json()),
            fetch('{{ asset("geojson/desa.geojson") }}').then(r => r.json()),
            fetch('{{ asset("geojson/peta_sls_202513523.geojson") }}').then(r => r.json()),
            $.get('{{ route("tematik.api.users") }}')
        ]).then(([kec, desa, sls, users]) => {
            kecData = kec; desaData = desa; slsData = sls;
            renderLayer();
            populateDropdowns();
            initMemberSelect(users);
        }).catch(err => {
            console.error("GeoJSON Load Error:", err);
            Swal.fire('Error', 'Gagal memuat data geospasial. Silakan refresh halaman.', 'error');
        });
    }

    function initMemberSelect(users) {
        const data = users.map(u => ({ id: u.nama_lengkap, text: u.nama_lengkap }));
        $('#in-member').select2({
            data: data,
            placeholder: 'Pilih Anggota Tim...',
            allowClear: true
        });
    }

    function renderLayer() {
        if (geojsonLayer) mainMap.removeLayer(geojsonLayer);
        let features = [];
        let color = '#3b82f6';

        if (!currentKec) features = kecData.features;
        else if (!currentDesa) {
            features = desaData.features.filter(f => f.properties.kec_desa.startsWith(currentKec.toUpperCase() + '_'));
            color = '#10b981';
        } else {
            features = slsData.features.filter(f => f.properties.nmkec.toUpperCase() === currentKec.toUpperCase() && f.properties.nmdesa.toUpperCase() === currentDesa.toUpperCase());
            color = '#f59e0b';
        }

        geojsonLayer = L.geoJSON(features, {
            style: (f) => {
                let finalColor = color;
                if (currentDesa) {
                    // Random-ish color for SLS to make them distinct
                    const colors = ['#f59e0b', '#d97706', '#b45309', '#facc15', '#eab308'];
                    finalColor = colors[Math.abs(f.properties.idsls || 0) % colors.length];
                }
                return { color: finalColor, fillOpacity: 0.15, weight: 1.5, fillWeight: 1 };
            },
            onEachFeature: (f, l) => {
                let name = '';
                if (currentDesa) name = f.properties.nmsls; // RT/RW level
                else if (currentKec) name = f.properties.kec_desa?.split('_')[1] || f.properties.nmdesa; // Desa level
                else name = f.properties.nmkec; // Kec level

                l.bindTooltip(name, { 
                    permanent: true, 
                    direction: 'center', 
                    className: 'custom-label',
                    opacity: 0.8
                });
                
                l.on('mouseover', () => {
                    l.setStyle({ fillOpacity: 0.3, weight: 3 });
                });
                l.on('mouseout', () => {
                    l.setStyle({ fillOpacity: 0.1, weight: 2 });
                });

                l.on('click', (e) => {
                    L.DomEvent.stopPropagation(e);
                    if (!currentKec) { 
                        currentKec = f.properties.nmkec; 
                        $('#filterKecamatan').val(currentKec).trigger('change'); 
                    }
                    else if (!currentDesa) { 
                        currentDesa = f.properties.kec_desa.split('_')[1]; 
                        $('#filterDesa').val(currentDesa).trigger('change'); 
                    } else {
                        // SLS level click
                        $('#detail-placeholder').addClass('d-none');
                        $('#detail-content').removeClass('d-none').addClass('animate-up');
                        $('#txt-title').text('Wilayah SLS/RT-RW');
                        $('#txt-date').text('-');
                        $('#txt-kec').text(f.properties.nmkec);
                        $('#txt-desa').text(f.properties.nmdesa);
                        $('#txt-sls').text(f.properties.nmsls);
                        $('#txt-pic').text('-');
                        $('#txt-status-val').text('Region');
                        $('#badge-status').text('Wilayah').attr('class', 'badge rounded-pill bg-info');
                        $('#txt-members').html(`ID SLS: ${f.properties.idsls}<br>Klik marker untuk detail laporan.`);
                        mainMap.fitBounds(l.getBounds());
                    }
                });
            }
        }).addTo(mainMap);

        if (features.length > 0) mainMap.fitBounds(geojsonLayer.getBounds(), { padding: [30, 30] });
    }

    function populateDropdowns() {
        const set = new Set();
        kecData.features.forEach(f => set.add(f.properties.nmkec));
        Array.from(set).sort().forEach(k => $('#filterKecamatan').append(`<option value="${k}">${k}</option>`));
    }

    // 2. FILTERS & SEARCH
    $('#filterKecamatan').on('change', function() {
        currentKec = $(this).val(); currentDesa = '';
        $('#filterDesa').html('<option value="">Semua Desa</option>').prop('disabled', !currentKec);
        if (currentKec) {
            const dSet = new Set();
            desaData.features.forEach(f => { if (f.properties.kec_desa.startsWith(currentKec.toUpperCase() + '_')) dSet.add(f.properties.kec_desa.split('_')[1]); });
            Array.from(dSet).sort().forEach(d => $('#filterDesa').append(`<option value="${d}">${d}</option>`));
        }
        renderLayer();
    });

    $('#filterDesa').on('change', function() { currentDesa = $(this).val(); renderLayer(); });
    $('#resetFilter').on('click', function() { 
        currentKec = ''; currentDesa = ''; $('#filterKecamatan').val(''); $('#filterDesa').val('').prop('disabled', true); 
        renderLayer(); mainMap.setView([-6.89, 112.06], 11);
    });

    $('#map-search').on('input', function() {
        const q = $(this).val().toUpperCase();
        if (q.length < 2) { $('#search-suggestions').addClass('d-none'); return; }
        let html = '';
        
        // Search by Coordinates
        const coordMatch = q.match(/^(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)$/);
        if (coordMatch) {
            html += `<div class="p-3 suggestion-item border-bottom small" data-type="coord" data-lat="${coordMatch[1]}" data-lng="${coordMatch[2]}">
                        <i class="fas fa-crosshairs text-danger me-2"></i><b>Koordinat:</b> ${coordMatch[1]}, ${coordMatch[2]}
                     </div>`;
        }

        kecData.features.forEach(f => { if (f.properties.nmkec.includes(q)) html += `<div class="p-3 suggestion-item border-bottom small" data-type="kec" data-val="${f.properties.nmkec}"><i class="fas fa-map-marked-alt text-primary me-2"></i><b>${f.properties.nmkec}</b> <span class="text-muted small ms-2">Kecamatan</span></div>`; });
        desaData.features.forEach(f => { const d = f.properties.kec_desa.split('_')[1]; if (d.includes(q)) html += `<div class="p-3 suggestion-item border-bottom small" data-type="desa" data-val="${d}" data-kec="${f.properties.kec_desa.split('_')[0]}"><i class="fas fa-map-pin text-success me-2"></i><b>${d}</b> <span class="text-muted small ms-2">${f.properties.kec_desa.split('_')[0]}</span></div>`; });
        
        // Search by SLS (RT/RW)
        if (slsData) {
            let count = 0;
            slsData.features.some(f => {
                if (f.properties.nmsls.toUpperCase().includes(q)) {
                    html += `<div class="p-3 suggestion-item border-bottom small" data-type="sls" data-idsls="${f.properties.idsls}" data-val="${f.properties.nmsls}">
                                <i class="fas fa-home text-warning me-2"></i><b>${f.properties.nmsls}</b><br>
                                <small class="text-muted ms-4">${f.properties.nmkec} - ${f.properties.nmdesa}</small>
                             </div>`;
                    count++;
                }
                return count >= 10; // limit suggestions
            });
        }

        $('#search-suggestions').html(html || '<div class="p-4 text-center text-muted small">Wilayah tidak ditemukan</div>').removeClass('d-none');
    });

    $(document).on('click', '.suggestion-item', function() {
        const d = $(this).data(); 
        $('#map-search').val(d.val || `${d.lat}, ${d.lng}`); 
        $('#search-suggestions').addClass('d-none');
        
        if (d.type === 'coord') {
            const lat = parseFloat(d.lat), lng = parseFloat(d.lng);
            const details = findLocationDetails(lat, lng);
            if (details.kec) {
                const marker = L.marker([lat, lng]).addTo(mainMap).bindPopup(`<b>Koordinat Dicari</b><br>${lat}, ${lng}`).openPopup();
                mainMap.flyTo([lat, lng], 18);
            } else {
                Swal.fire('Di Luar Wilayah', 'Koordinat berada di luar Kabupaten Tuban', 'warning');
            }
        } else if (d.type === 'kec') {
            $('#filterKecamatan').val(d.val).trigger('change');
        } else if (d.type === 'desa') {
            $('#filterKecamatan').val(d.kec).trigger('change'); 
            setTimeout(() => $('#filterDesa').val(d.val).trigger('change'), 500);
        } else if (d.type === 'sls') {
            const feature = slsData.features.find(f => f.properties.idsls === d.idsls);
            if (feature) {
                currentKec = feature.properties.nmkec;
                currentDesa = feature.properties.nmdesa;
                $('#filterKecamatan').val(currentKec);
                $('#filterDesa').html(`<option value="${currentDesa}">${currentDesa}</option>`).val(currentDesa);
                renderLayer();
                
                // Find feature in the newly rendered layer
                setTimeout(() => {
                    geojsonLayer.eachLayer(l => {
                        if (l.feature.properties.idsls === d.idsls) {
                            mainMap.fitBounds(l.getBounds());
                            l.openTooltip();
                            l.fire('click');
                        }
                    });
                }, 600);
            }
        }
    });

    // 3. CORE DATA FUNCTIONS
    function refreshAllData() {
        // Markers
        $.get('{{ route("tematik.api.lokasi") }}', function(res) {
            markersLayer.clearLayers();
            $('#stat-total-points').text(res.length);
            const dist = {};
            res.forEach(loc => {
                const m = L.marker([loc.lat, loc.lng]).addTo(markersLayer);
                m.on('click', () => showMarkerDetail(loc));
                const k = loc.kecamatan || 'Unknown';
                dist[k] = (dist[k] || 0) + 1;
            });
            updatePieChart(dist);
        });

        // Info Table & Bar Chart
        $.get('{{ route("tematik.api.info") }}', function(res) {
            let html = '';
            const labels = [], data = [];
            res.forEach(r => {
                html += `<tr><td class="ps-4 fw-bold text-dark small">${r.kategori}</td><td><span class="badge bg-primary rounded-pill">${r.jumlah}</span></td><td class="text-muted pe-4 small">${r.keterangan}</td></tr>`;
                labels.push(r.kategori); data.push(r.jumlah);
            });
            $('#info-table-body').html(html || '<tr><td colspan="3" class="text-center py-5">Belum ada data</td></tr>');
            updateBarChart(labels, data);
        });

        // Laporan Table
        $.get('{{ route("tematik.api.laporan") }}', function(res) {
            let html = '';
            res.forEach(r => {
                html += `
                    <tr>
                        <td class="ps-4 small fw-bold">${new Date(r.tanggal).toLocaleDateString('id-ID')}</td>
                        <td>
                            <span class="badge bg-light text-primary border rounded-pill small d-block mb-1">${r.kecamatan}</span>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">${r.desa || '-'}</small>
                        </td>
                        <td class="small">${r.pic}</td>
                        <td>
                            <div class="small fw-bold text-dark">${r.judul}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">${r.sls || ''}</small>
                        </td>
                        <td class="small text-muted">${r.member || '-'}</td>
                        <td><span class="badge rounded-pill ${r.status === 'Active' ? 'bg-success' : 'bg-warning'} small">${r.status}</span></td>
                        <td class="text-center pe-4">
                            <button class="btn btn-sm btn-light rounded-circle btn-delete" data-id="${r.id}"><i class="fas fa-trash-alt text-danger"></i></button>
                        </td>
                    </tr>
                `;
            });
            $('#laporan-table-body').html(html || '<tr><td colspan="7" class="text-center py-5 text-muted">Data laporan masih kosong</td></tr>');
        });
    }

    function showMarkerDetail(loc) {
        $('#detail-placeholder').addClass('d-none');
        $('#detail-content').removeClass('d-none').addClass('animate-up');
        $('#txt-title').text(loc.judul);
        $('#txt-date').text(new Date(loc.tanggal).toLocaleDateString('id-ID'));
        $('#txt-kec').text(loc.kecamatan || '-');
        $('#txt-desa').text(loc.desa || '-');
        $('#txt-sls').text(loc.sls || '-');
        $('#txt-pic').text(loc.pic);
        $('#txt-status-val').text(loc.status);
        $('#badge-status').text(loc.status).attr('class', 'badge rounded-pill ' + (loc.status === 'Active' ? 'bg-success' : 'bg-warning'));
        $('#txt-members').html(loc.member ? loc.member.split(',').join('<br>') : 'Tidak ada catatan anggota.');
        mainMap.flyTo([loc.lat, loc.lng], 17);
    }

    // 4. CHARTS
    function updatePieChart(dist) {
        const ctx = document.getElementById('pieChart').getContext('2d');
        if (pieChart) pieChart.destroy();
        pieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(dist),
                datasets: [{ data: Object.values(dist), backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'], borderWidth: 0 }]
            },
            options: { cutout: '70%', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } }
        });
    }

    function updateBarChart(labels, data) {
        const ctx = document.getElementById('infoBarChart').getContext('2d');
        if (barChart) barChart.destroy();
        barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'Jumlah Titik', data: data, backgroundColor: 'rgba(59, 130, 246, 0.8)', borderRadius: 8 }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } }
        });
    }

    // 5. CRUD OPERATIONS
    $('#form-unified').on('submit', function(e) {
        e.preventDefault();
        const payload = {
            nama: $('#in-judul').val(), 
            judul: $('#in-judul').val(), 
            kecamatan: $('#in-kec').val(), 
            desa: $('#in-desa').val(),
            sls: $('#in-sls').val(),
            member: $('#in-member').val() ? $('#in-member').val().join(', ') : '',
            lat: $('#in-lat').val(), 
            lng: $('#in-lng').val(), 
            status: 'Active', 
            pic: {!! json_encode(Auth::user()->nama_lengkap) !!},
            tanggal: new Date().toISOString().split('T')[0], 
            _token: '{{ csrf_token() }}'
        };
        const btn = $('#btn-submit-report');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

        $.post('{{ route("tematik.api.store") }}', payload)
            .done(function() {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Laporan titik lokasi telah disimpan ke dalam peta.' });
                $('#form-unified')[0].reset(); 
                $('#form-loc-card').addClass('d-none');
                if (formMarker) formMap.removeLayer(formMarker); 
                formMarker = null;
                refreshAllData(); 
                $('[data-bs-target="#panel-sig"]').tab('show');
            })
            .fail(function(err) {
                console.error("Store Error:", err);
                let msg = 'Gagal menyimpan laporan.';
                if (err.responseJSON && err.responseJSON.errors) {
                    msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                } else if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Gagal Simpan', html: `<div class="text-start small">${msg}</div>` });
            })
            .always(function() {
                btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i> SIMPAN LAPORAN KE PETA');
            });
    });

    $(document).on('click', '.btn-delete', function() { 
        $('#confirm-delete').data('id', $(this).data('id')); 
        new bootstrap.Modal('#deleteModal').show(); 
    });

    $('#confirm-delete').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: `{{ url('tematik/api/lokasi') }}/${id}`, type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
            success: function() { 
                bootstrap.Modal.getInstance('#deleteModal').hide(); 
                refreshAllData(); 
                Swal.fire('Terhapus', 'Laporan telah dihapus', 'success'); 
            }
        });
    });

    // 6. MISC
    $('#btn-recenter').on('click', function() { formMap.setView([-6.89, 112.06], 11); });
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
        mainMap.invalidateSize(); formMap.invalidateSize();
    });

    function findLocationDetails(lat, lng) {
        let res = { kec: null, desa: null, sls: null };
        const point = L.latLng(lat, lng);

        // Point-in-Polygon Helper
        function isInside(pt, vs) {
            let x = pt.lng, y = pt.lat;
            let inside = false;
            for (let i = 0, j = vs.length - 1; i < vs.length; j = i++) {
                let xi = vs[i][0], yi = vs[i][1];
                let xj = vs[j][0], yj = vs[j][1];
                let intersect = ((yi > y) != (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }

        function checkGeometry(geometry, pt) {
            if (geometry.type === 'Polygon') {
                return isInside(pt, geometry.coordinates[0]);
            } else if (geometry.type === 'MultiPolygon') {
                return geometry.coordinates.some(poly => isInside(pt, poly[0]));
            }
            return false;
        }

        // 1. Find Kecamatan
        if (kecData) {
            for (const f of kecData.features) {
                if (checkGeometry(f.geometry, point)) {
                    res.kec = f.properties.nmkec;
                    break;
                }
            }
        }

        // 2. Find Desa
        if (desaData && res.kec) {
            for (const f of desaData.features) {
                if (f.properties.kec_desa.startsWith(res.kec.toUpperCase() + '_')) {
                    if (checkGeometry(f.geometry, point)) {
                        res.desa = f.properties.kec_desa.split('_')[1];
                        break;
                    }
                }
            }
        }

        // 3. Find SLS (RT/RW)
        if (slsData && res.desa) {
            for (const f of slsData.features) {
                if (f.properties.nmkec.toUpperCase() === res.kec.toUpperCase() && f.properties.nmdesa.toUpperCase() === res.desa.toUpperCase()) {
                    if (checkGeometry(f.geometry, point)) {
                        res.sls = f.properties.nmsls;
                        break;
                    }
                }
            }
        }

        console.log("Detected Details:", res);
        return res;
    }

    // 7. FORM MAP SEARCH
    $('#form-map-search').on('input', function() {
        const q = $(this).val().toUpperCase();
        if (q.length < 2) { $('#form-search-suggestions').addClass('d-none'); return; }
        let html = '';
        
        const coordMatch = q.match(/^(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)$/);
        if (coordMatch) {
            html += `<div class="p-2 suggestion-item-form border-bottom small" data-type="coord" data-lat="${coordMatch[1]}" data-lng="${coordMatch[2]}"><b>Koordinat:</b> ${coordMatch[1]}, ${coordMatch[2]}</div>`;
        }

        kecData.features.forEach(f => { if (f.properties.nmkec.includes(q)) html += `<div class="p-2 suggestion-item-form border-bottom small" data-type="kec" data-val="${f.properties.nmkec}">${f.properties.nmkec} (Kec)</div>`; });
        desaData.features.forEach(f => { const d = f.properties.kec_desa.split('_')[1]; if (d.includes(q)) html += `<div class="p-2 suggestion-item-form border-bottom small" data-type="desa" data-val="${d}" data-kec="${f.properties.kec_desa.split('_')[0]}">${d} (${f.properties.kec_desa.split('_')[0]})</div>`; });
        
        if (slsData) {
            let count = 0;
            slsData.features.some(f => {
                if (f.properties.nmsls.toUpperCase().includes(q)) {
                    html += `<div class="p-2 suggestion-item-form border-bottom small" data-type="sls" data-idsls="${f.properties.idsls}" data-val="${f.properties.nmsls}">${f.properties.nmsls}</div>`;
                    count++;
                }
                return count >= 8;
            });
        }
        $('#form-search-suggestions').html(html || '<div class="p-3 text-center text-muted small">Tidak ditemukan</div>').removeClass('d-none');
    });

    $(document).on('click', '.suggestion-item-form', function() {
        const d = $(this).data(); 
        console.log("Suggestion clicked:", d);
        $('#form-map-search').val(d.val || `${d.lat}, ${d.lng}`); 
        $('#form-search-suggestions').addClass('d-none');
        
        if (d.type === 'coord') {
            const lat = parseFloat(d.lat), lng = parseFloat(d.lng);
            formMap.setView([lat, lng], 18);
            setTimeout(() => updateFormLocation(lat, lng), 400);
        } else if (d.type === 'kec' || d.type === 'desa') {
            const data = d.type === 'kec' ? kecData : desaData;
            const feat = data.features.find(f => (f.properties.nmkec === d.val || (f.properties.kec_desa && f.properties.kec_desa.split('_')[1] === d.val)));
            if (feat) {
                const layer = L.geoJSON(feat);
                const bounds = layer.getBounds();
                const center = bounds.getCenter();
                formMap.fitBounds(bounds);
                setTimeout(() => updateFormLocation(center.lat, center.lng), 400);
            }
        } else if (d.type === 'sls') {
            if (!slsData) { console.error("slsData not loaded!"); return; }
            const feat = slsData.features.find(f => f.properties.idsls == d.idsls);
            if (feat) {
                const layer = L.geoJSON(feat);
                const bounds = layer.getBounds();
                const center = bounds.getCenter();
                console.log("SLS Feature found, zooming to:", center);
                formMap.setView(center, 18);
                setTimeout(() => {
                    updateFormLocation(center.lat, center.lng, {
                        kec: feat.properties.nmkec,
                        desa: feat.properties.nmdesa,
                        sls: feat.properties.nmsls
                    });
                }, 500);
            } else {
                console.warn("SLS feature not found for ID:", d.idsls);
            }
        }
    });

    initMaps();
    refreshAllData();
});
</script>
@endpush
@endsection
