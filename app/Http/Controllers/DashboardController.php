<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agenda;
use App\Models\Team;
use App\Models\Pembayaran;
use App\Models\Mitra;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\HonorariumService;

class DashboardController extends Controller
{
    protected $honorService;

    public function __construct(HonorariumService $honorService)
    {
        $this->honorService = $honorService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $mode = session('dashboard_mode', 'harmoni');

        // Mitra selalu masuk dashboard honor
        if ($user->role === 'Mitra') {
            return $this->dashboardMitra($user);
        }

        // Jika mode aktif adalah 'honor', tampilkan dashboard manajemen honor
        if ($mode === 'honor') {
            return $this->dashboardHonorManajemen($request, $user);
        }
        
        // --- LOGIKA DASHBOARD HARMONI (EXISTING) ---
        // --- 1. SET FILTER ---
        $filterTim = $request->filter_tim;
        $filterBulan = $request->filter_bulan ?: Carbon::now()->format('Y-m');
        $parsedDate = Carbon::parse($filterBulan);
        $bulan = $parsedDate->month;
        $tahun = $parsedDate->year;

        $list_tim = Team::where('nama_tim', '!=', 'Kepala BPS')->orderBy('nama_tim', 'asc')->get();

        // --- 2. DATA IDENTITAS ---
        $data = [
            'nama'      => $user->nama_lengkap,
            'role'      => $user->role,
            'tim'       => $user->team->nama_tim ?? 'Lintas Tim',
            'list_tim'  => $list_tim,
        ];

        // --- 3. LOGIKA TOP TEAMS ---
        $data['top_teams'] = Team::where('nama_tim', '!=', 'Kepala BPS')
            ->withCount(['agendas' => function($q) use ($bulan, $tahun) {
                $q->whereMonth('event_date', $bulan)->whereYear('event_date', $tahun);
            }])
            ->orderBy('agendas_count', 'desc')
            ->take(7)
            ->get();

        // --- 4. LOGIKA TREN BULANAN ---
        $monthly_stats = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthly_stats[$m] = Agenda::whereYear('event_date', $tahun)
                ->whereMonth('event_date', $m)
                ->when($filterTim, function($q) use ($filterTim) {
                    $q->where('team_id', $filterTim);
                })
                ->count();
        }
        $data['monthly_stats'] = $monthly_stats;

        // --- 5. LOGIKA STATISTIK CARDS ---
        $agendaQuery = Agenda::whereMonth('event_date', $bulan)->whereYear('event_date', $tahun);

        if ($user->role == 'Pegawai') {
            $data['total_pegawai'] = User::where('team_id', $user->team_id)->where('role', '!=', 'Admin')->count();
            
            $stats = Agenda::where('assigned_to', $user->id)
                ->whereMonth('event_date', $bulan)->whereYear('event_date', $tahun)
                ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
                ->first();
                
            $data['total_agenda']  = $stats->total;
            $data['tugas_selesai'] = $stats->selesai;
        } 
        elseif ($user->role == 'Katim') {
            $data['total_pegawai'] = User::where('team_id', $user->team_id)
                                        ->where('role', '!=', 'Admin')
                                        ->count();

            $katimStats = Agenda::whereMonth('event_date', $bulan)->whereYear('event_date', $tahun)
                ->where(function($q) use ($user) {
                    $q->where('assigned_to', $user->id)->orWhere('user_id', $user->id);
                })
                ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
                ->first();

            $data['total_agenda']  = $katimStats->total;
            $data['tugas_selesai'] = $katimStats->selesai;
        }
        else {
            $userQuery = User::where('role', '!=', 'Admin');
            
            if ($filterTim) {
                $userQuery->where('team_id', $filterTim);
                $agendaQuery->where('team_id', $filterTim); 
            }

            $data['total_pegawai'] = $userQuery->count();
            $stats = $agendaQuery->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
                ->first();
            
            $data['total_agenda']  = $stats->total;
            $data['tugas_selesai'] = $stats->selesai;
        }

        // --- 6. QUERY TABEL AGENDA TERKINI ---
        $agenda_terbaru = Agenda::with(['assignee', 'activityType'])
            ->leftJoin('users as creators', 'agendas.user_id', '=', 'creators.id')
            ->leftJoin('teams', 'creators.team_id', '=', 'teams.id')
            ->select('agendas.*', 'teams.nama_tim as creator_team_name')
            ->whereMonth('agendas.event_date', $bulan)
            ->whereYear('agendas.event_date', $tahun)
            
            ->when($user->role == 'Katim', function($q) use ($user) {
                $q->where(function($sq) use ($user) {
                    $sq->where('agendas.assigned_to', $user->id)
                      ->orWhere('agendas.user_id', $user->id);
                });
            })
            ->when($user->role == 'Pegawai', function($q) use ($user) {
                $q->where('agendas.assigned_to', $user->id);
            })
            ->when(($user->role == 'Kepala' || $user->role == 'Admin') && $filterTim, function($q) use ($filterTim) {
                $q->whereHas('assignee', fn($sq) => $sq->where('team_id', $filterTim));
            })
            
            ->orderBy('agendas.event_date', 'asc')
            ->paginate(10);

        return view('dashboard', array_merge($data, ['agenda_terbaru' => $agenda_terbaru]));
    }

    /**
     * Dashboard Khusus Mitra
     */
    private function dashboardMitra($user)
    {
        $id_sobat = $user->username;
        $mitra = Mitra::where('sobat_id', $id_sobat)->first();
        
        $pembayaran = Pembayaran::whereHas('penugasan', function($q) use ($id_sobat) {
            $q->where('mitra_id', $id_sobat);
        })->with('penugasan.anggaran')->orderBy('bulan_bayar', 'desc')->get();

        $stats = [
            'total_honor' => $pembayaran->sum('nominal_cair'),
            'lunas'       => $pembayaran->where('status_bayar', 'Lunas')->sum('nominal_cair'),
            'antre'       => $pembayaran->where('status_bayar', 'Antre')->sum('nominal_cair'),
            'penugasan'   => Penugasan::where('mitra_id', $id_sobat)->count(),
        ];

        // 12-Month Matrix (Adaptation from Malowopati)
        $currentYear = date('Y');
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $honorMatrix = [];
        
        foreach (range(1, 12) as $m) {
            $honorMatrix[$months[$m-1]] = Penugasan::where('mitra_id', $id_sobat)
                ->whereYear('tgl_selesai_target', $currentYear)
                ->whereMonth('tgl_selesai_target', $m)
                ->sum('total_honor_tugas');
        }

        return view('manajemen.mitra.dashboard_detail', compact('user', 'mitra', 'pembayaran', 'stats', 'honorMatrix', 'currentYear'));
    }

    /**
     * Dashboard Manajemen Honorarium (Untuk Katim, Umum, Kepala)
     */
    private function dashboardHonorManajemen(Request $request, $user)
    {
        $filterBulan = $request->filter_bulan ?: Carbon::now()->format('Y-m');
        $parsedDate = \Carbon\Carbon::parse($filterBulan);
        $bulanNum = $parsedDate->month;
        $tahunNum = $parsedDate->year;

        // 1. REKAP HONOR BULANAN
        // We fetch both Planned (Penugasan) and Realized (Lunas)
        $filterBulanString = $filterBulan; // 'YYYY-MM'
        
        $monthlyHonorDetails = Mitra::select('m_mitra.*')
            ->addSelect([
                // Planned: Based on assignments target month
                'accumulated_honor' => Penugasan::selectRaw('COALESCE(SUM(total_honor_tugas), 0)')
                    ->whereColumn('mitra_id', 'm_mitra.sobat_id')
                    ->whereMonth('tgl_selesai_target', $bulanNum)
                    ->whereYear('tgl_selesai_target', $tahunNum),
                
                // Realized: Based on payments marked 'Lunas'
                'realized_honor' => \App\Models\Pembayaran::selectRaw('COALESCE(SUM(nominal_cair), 0)')
                    ->where('status_bayar', 'Lunas')
                    ->where('bulan_bayar', $filterBulanString)
                    ->whereExists(function($q) {
                        $q->select(DB::raw(1))
                            ->from('t_penugasan')
                            ->whereColumn('t_penugasan.id', 't_pembayaran.penugasan_id')
                            ->whereColumn('t_penugasan.mitra_id', 'm_mitra.sobat_id');
                    })
            ])
            ->orderByDesc('realized_honor')
            ->get();

        // 2. REKAPITULASI HONOR BERDASARKAN PENUGASAN (Table 2 - LUNAS ONLY)
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $categories = ['Pendataan', 'Pemeriksaan', 'Pengolahan'];
        
        $crosstabData = [];
        foreach (range(1, 12) as $m) {
            $monthStr = $tahunNum . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $row = ['bulan' => $months[$m-1]];
            $rowTotal = 0;
            foreach ($categories as $cat) {
                $val = \App\Models\Pembayaran::join('t_penugasan', 't_pembayaran.penugasan_id', '=', 't_penugasan.id')
                    ->where('t_pembayaran.status_bayar', 'Lunas')
                    ->where('t_pembayaran.bulan_bayar', $monthStr)
                    ->where('t_penugasan.kategori_kegiatan', $cat)
                    ->sum('t_pembayaran.nominal_cair');
                $row[$cat] = $val;
                $rowTotal += $val;
            }
            $row['total'] = $rowTotal;
            $crosstabData[] = $row;
        }

        // 3. REKAP PETUGAS DENGAN JUMLAH PENUGASAN TERBANYAK SETAHUN (Planned)
        $mitraRankings = Penugasan::whereYear('tgl_selesai_target', $tahunNum)
            ->select('mitra_id', \Illuminate\Support\Facades\DB::raw('SUM(volume) as total_survey'))
            ->groupBy('mitra_id')
            ->orderBy('total_survey', 'desc')
            ->with('mitra')
            ->take(10)
            ->get();

        // 4. REKAP PRODUKTIVITAS TIM
        $teamRecap = \App\Models\Team::select('teams.*')
            ->addSelect([
                'total_activities' => Penugasan::selectRaw('COUNT(DISTINCT COALESCE(CAST(anggaran_id AS VARCHAR), nama_kegiatan_manual))')
                    ->whereColumn('team_id', 'teams.id')
                    ->whereMonth('tgl_selesai_target', $bulanNum)
                    ->whereYear('tgl_selesai_target', $tahunNum)
            ])
            ->where('teams.id', '!=', 1)
            ->where('nama_tim', 'not like', '%Kepala%')
            ->orderByDesc('total_activities')
            ->get();

        // 5. STAT CARD DATA
        $topHonorMonth = [
            'total_honor' => $monthlyHonorDetails->sortByDesc('realized_honor')->first()->realized_honor ?? 0,
            'mitra' => $monthlyHonorDetails->sortByDesc('realized_honor')->first()
        ];
        
        $topHonorYear = Mitra::select('m_mitra.*')
            ->addSelect([
                'total_honor' => \App\Models\Pembayaran::join('t_penugasan', 't_pembayaran.penugasan_id', '=', 't_penugasan.id')
                    ->whereColumn('t_penugasan.mitra_id', 'm_mitra.sobat_id')
                    ->where('t_pembayaran.status_bayar', 'Lunas')
                    ->where('t_pembayaran.bulan_bayar', 'like', $tahunNum . '-%')
                    ->selectRaw('COALESCE(SUM(t_pembayaran.nominal_cair), 0)')
            ])
            ->orderByDesc('total_honor')
            ->first();

        return view('dashboard-honor', [
            'filter_bulan' => $filterBulan,
            'months' => $months,
            'monthlyHonorDetails' => $monthlyHonorDetails,
            'crosstabData' => $crosstabData,
            'mitraRankings' => $mitraRankings,
            'teamRecap' => $teamRecap,
            'topHonorMonth' => $topHonorMonth,
            'topHonorYear' => $topHonorYear,
            'totalSurveyCount' => Penugasan::whereYear('tgl_selesai_target', $tahunNum)->sum('volume'), 
        ]);
    }

    /**
     * ALL AGENDA (Halaman Lihat Semua)
     */
    public function allAgenda(Request $request)
    {
        $user = auth()->user();
        $query = Agenda::with(['assignee', 'creator.team', 'activityType']);

        // Logika Hak Akses
        if ($user->role == 'Katim') {
            $query->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id)
                  ->orWhereHas('assignee', function($sq) use ($user) {
                      $sq->where('team_id', $user->team_id);
                  });
            });
        } elseif ($user->role == 'Pegawai') {
            $query->where('assigned_to', $user->id);
        }

        // Filter Tambahan
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status_laporan', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('activity_type_id', $request->type);
        }

        $allAgendas = $query->latest('event_date')->paginate(15);
        return view('agenda.all', compact('allAgendas'));
    }

    public function monitoring(Request $request)
    {
        $user = auth()->user();
        $isPegawai = $user->role === 'Pegawai';
        
        // Admin tidak perlu menu ini (sesuai request)
        if ($user->role === 'Admin') {
            abort(403, 'Akses ditolak.');
        }

        $year = $request->get('year', date('Y'));
        
        // Ambil data agenda dengan relasi terkait
        $query = Agenda::with(['assignee.team', 'activityType', 'team', 'reports'])
            ->where(function($q) use ($year) {
                $q->whereYear('event_date', $year)
                  ->orWhereYear('end_date', $year);
            });

        // Role-based visibility: Pegawai hanya melihat tugas miliknya
        if ($isPegawai) {
            $query->where('assigned_to', $user->id);
        }

        $agendas = $query->orderBy('event_date', 'asc')->get();

        // Grouping for the matrix
        $matrixData = [];
        foreach ($agendas as $agenda) {
            $dates = [];
            
            // LOGIKA MARKING TANGGAL:
            if ($isPegawai && $agenda->activity_type_id == 1 && $agenda->reports->count() > 0) {
                // POV Pegawai: Jika Tugas Lapangan & sudah ada laporan, hanya tandai tanggal lapor (Actual)
                foreach ($agenda->reports as $report) {
                    $dates[] = Carbon::parse($report->tanggal_lapor);
                }
            } else {
                // Default (Katim/Kepala/Pegawai Baru): Tandai seluruh rentang tanggal (Planned)
                $start = $agenda->event_date;
                $end = $agenda->end_date ?: $start;
                
                // Iterasi setiap hari dalam rentang
                $curr = $start->copy();
                while ($curr <= $end) {
                    if ($curr->year == $year) {
                        $dates[] = $curr->copy();
                    }
                    $curr->addDay();
                }
            }
            
            // Masukkan setiap tanggal ke dalam Matrix Data
            foreach ($dates as $date) {
                $month = (int)$date->format('m');
                $day = (int)$date->format('d');
                
                // Key untuk mengelompokkan personil dalam satu kegiatan yang sama di hari yang sama
                $key = md5($agenda->title . ($agenda->nomor_surat_tugas ?: $agenda->id));
                
                if (!isset($matrixData[$month][$day][$key])) {
                    $matrixData[$month][$day][$key] = [
                        'title'      => $agenda->title,
                        'type_id'    => $agenda->activity_type_id,
                        'type_name'  => $agenda->activityType->name ?? 'Kegiatan',
                        'team_id'    => $agenda->team_id,
                        'team_name'  => $agenda->team->nama_tim ?? 'Lintas Tim',
                        'location'   => $agenda->location ?: '-',
                        'status'     => $agenda->status_laporan,
                        'personnel'  => [],
                        'color'      => $this->getTeamColor($agenda->team_id, $agenda->team->nama_tim ?? '')
                    ];
                }
                
                // Tambahkan personil jika belum ada di daftar hari tersebut
                $personnelName = $agenda->assignee->nama_lengkap ?? 'Unknown';
                $exists = false;
                foreach ($matrixData[$month][$day][$key]['personnel'] as $p) {
                    if ($p['name'] === $personnelName) { $exists = true; break; }
                }
                
                if (!$exists) {
                    $matrixData[$month][$day][$key]['personnel'][] = [
                        'name' => $personnelName,
                        'team' => $agenda->assignee->team->nama_tim ?? ''
                    ];
                }
            }
        }

        return view('monitoring.index', compact('matrixData', 'year'));
    }

    /**
     * Helper untuk menentukan warna berdasarkan Tim (Update Berdasarkan Request)
     */
    private function getTeamColor($teamId, $teamName) 
    {
        $name = strtolower($teamName);
        
        // Kepala BPS & Subbagian Umum (Tetap menggunakan warna Standar BPS/Netral)
        if (str_contains($name, 'kepala')) return '#0058a8'; // Biru BPS
        if (str_contains($name, 'umum')) return '#64748b';   // Slate/Grey
        
        // Pemetaan Tim Teknis Spesifik ke Warna Vibrant
        // Menggunakan substring untuk fleksibilitas kecocokan nama
        if (str_contains($name, 'sosial')) return '#ef4444'; // Merah
        if (str_contains($name, 'distribusi')) return '#10b981'; // Hijau
        if (str_contains($name, 'produksi')) return '#f59e0b'; // Amber
        if (str_contains($name, 'neraca')) return '#8b5cf6'; // Ungu
        if (str_contains($name, 'pengolahan') || str_contains($name, 'ipds')) return '#ec4899'; // Pink
        if (str_contains($name, 'sektoral') || str_contains($name, 'pembinaan')) return '#06b6d4'; // Cyan
        
        // Default untuk tim lain jika ada
        return '#94a3b8';
    }

    public function panduanIndex()
    {
        return view('panduan.index');
    }
}