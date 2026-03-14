<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agenda;
use App\Models\Team; // Pastikan model Team ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    
    // --- 1. SET FILTER (Default: Bulan Ini) ---
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

    // --- 3. LOGIKA TOP TEAMS (Hanya untuk Kepala/Admin di View) ---
    $data['top_teams'] = Team::where('nama_tim', '!=', 'Kepala BPS')
        ->withCount(['agendas' => function($q) use ($bulan, $tahun) {
            $q->whereMonth('event_date', $bulan)
              ->whereYear('event_date', $tahun);
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
                $q->whereHas('assignee', fn($sq) => $sq->where('team_id', $filterTim));
            })
            ->count();
    }
    $data['monthly_stats'] = $monthly_stats;

    // --- 5. LOGIKA STATISTIK CARDS ---
    $agendaQuery = Agenda::whereMonth('event_date', $bulan)->whereYear('event_date', $tahun);

    if ($user->role == 'Pegawai') {
        $stats = Agenda::where('assigned_to', $user->id)
            ->whereMonth('event_date', $bulan)->whereYear('event_date', $tahun)
            ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
            ->first();
        $data['total_pegawai'] = User::where('team_id', $user->team_id)->count(); 
        $data['total_agenda']  = $stats->total;
        $data['tugas_selesai'] = $stats->selesai;
    } 
    else {
        $userQuery = User::query();
        if ($user->role !== 'Kepala' && $user->role !== 'Admin') {
            $filterTim = $user->team_id; // Katim dikunci ke timnya
        }

        if ($filterTim) {
            $userQuery->where('team_id', $filterTim);
            $agendaQuery->whereHas('assignee', fn($q) => $q->where('team_id', $filterTim));
        }

        $stats = $agendaQuery->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
            ->first();

        // Khusus Katim, total agenda di card statistik adalah agenda yang dia buat + agenda miliknya
        if ($user->role == 'Katim') {
            $katimStats = Agenda::whereMonth('event_date', $bulan)->whereYear('event_date', $tahun)
                ->where(function($q) use ($user) {
                    $q->where('assigned_to', $user->id)->orWhere('user_id', $user->id);
                })
                ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status_laporan = 'Selesai' THEN 1 END) as selesai")
                ->first();
            $data['total_agenda']  = $katimStats->total;
            $data['tugas_selesai'] = $katimStats->selesai;
        } else {
            $data['total_agenda']  = $stats->total;
            $data['tugas_selesai'] = $stats->selesai;
        }

        $data['total_pegawai'] = $userQuery->count();
    }

    // --- 6. QUERY TABEL AGENDA TERKINI (Sesuai Permintaan Anda) ---
    $agenda_terbaru = Agenda::with(['assignee', 'activityType'])
        ->leftJoin('users as creators', 'agendas.user_id', '=', 'creators.id')
        ->leftJoin('teams', 'creators.team_id', '=', 'teams.id')
        ->select('agendas.*', 'teams.nama_tim as creator_team_name')
        ->whereMonth('agendas.event_date', $bulan)
        ->whereYear('agendas.event_date', $tahun)
        
        ->when($user->role == 'Katim', function($q) use ($user) {
            // POV Katim: Hanya tugas miliknya sendiri ATAU tugas yang dia kasih ke orang lain
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
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $users = User::whereIn('role', ['Pegawai', 'Katim'])
            ->orderBy('role', 'asc')
            ->orderBy('nama_lengkap', 'asc')
            ->with(['agendas' => function($q) use ($month, $year) {
                $q->whereMonth('event_date', $month)
                  ->whereYear('event_date', $year)
                  ->with(['creator.team']); 
            }])
            ->get();

        return view('monitoring.index', compact('users', 'daysInMonth', 'month', 'year'));
    }

    public function panduanIndex()
    {
        return view('panduan.index');
    }
}