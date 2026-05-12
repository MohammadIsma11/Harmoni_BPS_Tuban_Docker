<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agenda;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\WorkloadClusteringService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $user->role;
        $filterBulan = $request->input('filter_bulan', date('Y-m'));
        $filterTim = $request->input('filter_tim');

        $nama = $user->nama_lengkap;
        $tim = $user->team->nama_tim ?? 'Lintas Fungsi';

        $total_pegawai = User::whereNotIn('role', ['Admin', 'Kepala'])->count();
        $total_agenda = Agenda::count();
        $tugas_selesai = Agenda::where('status_laporan', 'Selesai')->count();

        $top_teams = collect();
        if ($role == 'Kepala' || $role == 'Admin') {
            $top_teams = Team::withCount(['agendas' => function($query) use ($filterBulan) {
                $query->whereMonth('event_date', Carbon::parse($filterBulan)->month)
                      ->whereYear('event_date', Carbon::parse($filterBulan)->year);
            }])->orderBy('agendas_count', 'desc')->take(7)->get();
        }

        $monthly_stats = Agenda::selectRaw('EXTRACT(MONTH FROM event_date) as month, count(*) as total')
            ->whereRaw("EXTRACT(YEAR FROM event_date) = ?", [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $query = Agenda::with(['assignee', 'team'])->latest();
        if ($filterTim) $query->where('team_id', $filterTim);
        $query->whereMonth('event_date', Carbon::parse($filterBulan)->month)
              ->whereYear('event_date', Carbon::parse($filterBulan)->year);

        $agenda_terbaru = $query->paginate(10);
        $list_tim = Team::all();

        return view('dashboard', compact(
            'nama', 'role', 'tim', 'total_pegawai', 'total_agenda', 'tugas_selesai',
            'top_teams', 'monthly_stats', 'agenda_terbaru', 'list_tim'
        ));
    }

    public function allAgenda(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');

        $query = Agenda::with(['assignee', 'creator', 'team'])->latest();

        if ($search) $query->where('title', 'like', '%' . $search . '%');
        if ($type) $query->where('activity_type_id', $type);
        if ($status) $query->where('status_laporan', $status);

        $allAgendas = $query->paginate(20);
        $list_tim = Team::all();

        return view('agenda.all', compact('allAgendas', 'list_tim'));
    }

    public function monitoring(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        $agendas = Agenda::with(['assignee', 'team'])
            ->whereYear('event_date', $year)
            ->get();

        $matrixData = [];
        foreach ($agendas as $a) {
            $month = (int)Carbon::parse($a->event_date)->month;
            $day = (int)Carbon::parse($a->event_date)->day;

            $matrixData[$month][$day][] = [
                'title' => $a->title,
                'color' => $this->getTeamColor($a->team->nama_tim ?? ''),
                'type_name' => $a->activity_type_id == 1 ? 'Lapangan' : ($a->activity_type_id == 3 ? 'Dinas Luar' : 'Rapat'),
                'team_name' => $a->team->nama_tim ?? 'Lintas Fungsi',
                'location' => $a->location ?? 'Ruang Rapat',
                'personnel' => [
                    ['name' => $a->assignee->nama_lengkap ?? 'Tanpa Nama']
                ]
            ];
        }

        return view('monitoring.index', compact('year', 'matrixData'));
    }

    public function panduanIndex()
    {
        return view('panduan.index');
    }

    public function kepalaDashboard(WorkloadClusteringService $clusteringService)
    {
        $user = Auth::user();
        if ($user->role !== 'Kepala' && $user->role !== 'Admin') {
            abort(403, 'Akses khusus Pimpinan.');
        }

        $pegawai = User::whereNotIn('role', ['Admin', 'Kepala'])->get();
        $dataset = [];
        $pegawaiInfo = [];

        $currentMonth = date('m');
        $currentYear = date('Y');

        foreach ($pegawai as $p) {
            $agendas = Agenda::where('assigned_to', $p->id)
                ->whereMonth('event_date', $currentMonth)
                ->whereYear('event_date', $currentYear)
                ->get();

            $agendasCount = (float)$agendas->count();

            $totalPoints = 0;
            foreach ($agendas as $a) {
                if ($a->activity_type_id == 1) $totalPoints += 5;
                elseif ($a->activity_type_id == 3) $totalPoints += 3;
                else $totalPoints += 1;
            }

            $pendingTasks = (float)Agenda::where('assigned_to', $p->id)
                ->whereMonth('event_date', $currentMonth)
                ->whereYear('event_date', $currentYear)
                ->where('status_laporan', '!=', 'Selesai')
                ->count();

            $dataset[] = [$agendasCount, (float)$totalPoints, $pendingTasks];
            $pegawaiInfo[] = [
                'id' => $p->id,
                'nama' => $p->nama_lengkap,
                'agendas' => $agendasCount,
                'points' => $totalPoints,
                'pending' => $pendingTasks,
            ];
        }

        $analysisResults = $clusteringService->analyze($dataset);
        $pegawaiResult = [];
        $counts = ['Beban Tinggi' => 0, 'Beban Ideal' => 0, 'Beban Rendah' => 0];

        foreach ($pegawaiInfo as $index => $info) {
            $label = $analysisResults[$index] ?? 'Beban Ideal';
            
            $reason = "";
            if ($label == 'Beban Tinggi') {
                $reason = "Kombinasi kegiatan banyak dengan tunggakan tugas (" . $info['pending'] . ") yang cukup tinggi.";
            } elseif ($label == 'Beban Ideal') {
                $reason = "Ritme kerja stabil dengan penyelesaian tugas yang terjaga.";
            } else {
                $reason = "Beban kerja rendah, kapasitas tersedia untuk percepatan tugas.";
            }

            $pegawaiResult[] = array_merge($info, [
                'cluster' => $label,
                'reason' => $reason
            ]);
            $counts[$label]++;
        }

        $totalOverload = $counts['Beban Tinggi'];
        usort($pegawaiResult, function($a, $b) {
            $order = ['Beban Tinggi' => 1, 'Beban Ideal' => 2, 'Beban Rendah' => 3];
            return $order[$a['cluster']] <=> $order[$b['cluster']];
        });

        return view('dashboard.kepala', compact('pegawaiResult', 'counts', 'totalOverload'));
    }

    private function getTeamColor($name)
    {
        $name = strtolower($name);
        if (str_contains($name, 'umum')) return '#64748b'; // Subbag Umum (Sesuai Monitoring)
        if (str_contains($name, 'sosial')) return '#ef4444';
        if (str_contains($name, 'distribusi')) return '#10b981';
        if (str_contains($name, 'produksi')) return '#f59e0b';
        if (str_contains($name, 'neraca')) return '#8b5cf6';
        if (str_contains($name, 'pengolahan') || str_contains($name, 'ipds')) return '#ec4899';
        if (str_contains($name, 'sektoral') || str_contains($name, 'pembinaan')) return '#06b6d4';
        return '#0058a8'; // Kepala BPS / Default
    }
}