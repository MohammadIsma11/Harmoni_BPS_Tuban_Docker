<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\User;
use App\Models\MeetingPresence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST KEGIATAN & PERTEMUAN (PENDING)
    |--------------------------------------------------------------------------
    */
    public function listMeeting(Request $request)
    {
        $user = auth()->user();
        
        $query = Agenda::with(['creator.team', 'notulis', 'activityType'])
            ->where('assigned_to', $user->id)
            ->where('status_laporan', 'Pending');

        if ($request->has('type')) {
            $query->where('activity_type_id', $request->type);
        } else {
            $query->whereIn('activity_type_id', [2, 3]);
        }

        $meetings = $query->orderBy('event_date', 'asc')->get();

        return view('meeting.index', compact('meetings'));
    }

    /*
    |--------------------------------------------------------------------------
    | PRESENSI / ABSENSI
    |--------------------------------------------------------------------------
    */
    public function showPresensiMeeting($id)
    {
        $agenda = Agenda::with('creator')->where('id', $id)->where('assigned_to', auth()->id())->firstOrFail();
        $alreadySigned = MeetingPresence::where('agenda_id', $id)->where('user_id', auth()->id())->exists();
        return view('meeting.presensi', compact('agenda', 'alreadySigned'));
    }

    public function storePresensiMeeting(Request $request)
    {
        $request->validate(['agenda_id' => 'required|exists:agendas,id', 'signature' => 'required']);
        
        MeetingPresence::updateOrCreate(
            ['agenda_id' => $request->agenda_id, 'user_id' => auth()->id()],
            ['signature_base64' => $request->signature, 'signed_at' => Carbon::now('Asia/Jakarta')]
        );

        return redirect()->route('meeting.index')->with('success', 'Tanda tangan kehadiran berhasil disimpan.');
    }

    /*
    |--------------------------------------------------------------------------
    | MONITORING & PRINT
    |--------------------------------------------------------------------------
    */
    public function monitoringKehadiran($id)
    {
        $meeting = Agenda::with(['creator', 'notulis'])->findOrFail($id);

        $allParticipants = Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->whereIn('activity_type_id', [2, 3])
            ->with('assignee.team')
            ->get();

        $allAgendaIds = $allParticipants->pluck('id')->toArray();
        $presentUserIds = MeetingPresence::whereIn('agenda_id', $allAgendaIds)->pluck('user_id')->toArray();

        $stats = [
            'total' => $allParticipants->count(),
            'hadir' => count($presentUserIds),
            'belum' => $allParticipants->count() - count($presentUserIds),
            'persen' => $allParticipants->count() > 0 ? round((count($presentUserIds) / $allParticipants->count()) * 100) : 0
        ];

        return view('meeting.monitoring', compact('meeting', 'allParticipants', 'presentUserIds', 'stats'));
    }

    public function printPresensi($id)
    {
        $meeting = Agenda::findOrFail($id);
        $kepala = User::where('role', 'Kepala')->first() ?? User::where('role', 'Admin')->first(); 

        $peserta = Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->whereIn('activity_type_id', [2, 3])
            ->with(['assignee.team'])
            ->get();

        $allAgendaIds = $peserta->pluck('id')->toArray();
        $dataPresensi = MeetingPresence::whereIn('agenda_id', $allAgendaIds)->get()->keyBy('agenda_id');

        $pdf = Pdf::loadView('meeting.pdf_presensi', compact('meeting', 'peserta', 'kepala', 'dataPresensi'));
        return $pdf->setPaper('a4', 'portrait')->stream('Presensi_' . $meeting->title . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | NOTULENSI (KHUSUS RAPAT)
    |--------------------------------------------------------------------------
    */
    public function createNotulensi($id)
{
    // Ambil data agenda beserta data terkait
    $meeting = Agenda::findOrFail($id);

    // --- LOGIKA AKSES BARU ---
    if ($meeting->activity_type_id == 2) {
        // Jika Rapat Dinas: Cek apakah user yang login adalah NOTULIS
        if ($meeting->notulis_id != auth()->id() && auth()->user()->role != 'Admin') {
            return redirect()->route('meeting.index')->with('error', 'Akses ditolak. Hanya notulis yang dapat mengisi hasil rapat.');
        }
    } elseif ($meeting->activity_type_id == 3) {
        // Jika Dinas Luar: Cek apakah user yang login adalah orang yang DITUGASKAN
        if ($meeting->assigned_to != auth()->id() && auth()->user()->role != 'Admin') {
            return redirect()->route('meeting.index')->with('error', 'Akses ditolak. Anda tidak ditugaskan dalam dinas luar ini.');
        }
    }

    // Ambil data peserta untuk ditampilkan di sidebar/info (opsional)
    $semuaPeserta = Agenda::where('title', $meeting->title)
        ->where('event_date', $meeting->event_date)
        ->with('assignee')
        ->get();

    $allAgendaIds = $semuaPeserta->pluck('id')->toArray();
    $userSudahHadir = MeetingPresence::whereIn('agenda_id', $allAgendaIds)->pluck('user_id')->toArray();

    // --- REDIRECT VIEW DINAMIS ---
    if ($meeting->activity_type_id == 3) {
        return view('meeting.dinas_luar', compact('meeting', 'semuaPeserta', 'userSudahHadir'));
    }

    return view('meeting.notulensi', compact('meeting', 'semuaPeserta', 'userSudahHadir'));
}

    public function storeNotulensi(Request $request, $id)
{
    $meeting = Agenda::findOrFail($id);

    $request->validate([
        // SEKARANG VALIDASI FILE, BUKAN STRING
        'hasil_rapat_file' => 'required|file|mimes:pdf,doc,docx,txt|max:20480', 
        'materi_path'      => 'nullable|file|mimes:pdf,pptx,ppt|max:20480',
        'foto_dokumentasi' => 'required|array|min:1',
        'foto_dokumentasi.*' => 'image|mimes:jpg,jpeg,png|max:20480', // Sesuaikan ke 20MB
    ]);

    try {
        DB::beginTransaction();

        // 1. Handle File Notulensi Utama
        $notulensiFile = null;
        if ($request->hasFile('hasil_rapat_file')) {
            $notulensiFile = $request->file('hasil_rapat_file')->store('notulensi_rapat', 'public');
        }

        // 2. Handle Materi
        $materiFile = $meeting->materi_path;
        if ($request->hasFile('materi_path')) {
            $materiFile = $request->file('materi_path')->store('materi_rapat', 'public');
        }

        // 3. Handle Foto Dokumentasi
        $paths = [];
        if ($request->hasFile('foto_dokumentasi')) {
            foreach ($request->file('foto_dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_rapat', 'public');
            }
        }

        // 4. Update Massal ke semua peserta (Grup yang sama)
        Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->where('activity_type_id', 2)
            ->update([
                'notulensi_hasil'  => $notulensiFile, // Sekarang menyimpan PATH file
                'materi_path'      => $materiFile,
                'dokumentasi_path' => json_encode($paths), 
                'status_laporan'   => 'Selesai',
                'updated_at'       => now()
            ]);

        DB::commit();
        return redirect()->route('meeting.history')->with('success', 'Notulensi berhasil diunggah!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

    public function updateNotulensi(Request $request, $id)
    {
        $meeting = Agenda::findOrFail($id);

        if ($meeting->notulis_id != Auth::id() && Auth::user()->role != 'Admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'hasil_rapat' => 'required|string|min:20',
            'materi_path' => 'nullable|file|mimes:pdf,pptx,ppt|max:20480',
            'foto_dokumentasi.*' => 'image|mimes:jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            // 1. Handle Update Materi
            $materiFile = $meeting->materi_path;
            if ($request->hasFile('materi_path')) {
                if ($materiFile) Storage::disk('public')->delete($materiFile);
                $materiFile = $request->file('materi_path')->store('materi_rapat', 'public');
            }

            // 2. Handle Update Foto
            $paths = json_decode($meeting->dokumentasi_path, true) ?? [];
            if ($request->hasFile('foto_dokumentasi')) {
                foreach($paths as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
                $paths = [];
                foreach ($request->file('foto_dokumentasi') as $file) {
                    $paths[] = $file->store('dokumentasi_rapat', 'public');
                }
            }

            // 3. Update Massal
            Agenda::where('title', $meeting->title)
                ->where('event_date', $meeting->event_date)
                ->where('activity_type_id', 2)
                ->update([
                    'notulensi_hasil' => $request->hasil_rapat,
                    'materi_path' => $materiFile,
                    'dokumentasi_path' => json_encode($paths),
                    'updated_at' => now()
                ]);

            DB::commit();
            return redirect()->route('meeting.history')->with('success', 'Notulensi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function storeDinasLuar(Request $request, $id)
{
    $meeting = Agenda::findOrFail($id);

    // Pastikan ini benar-benar agenda Dinas Luar
    if ($meeting->activity_type_id != 3) {
        return back()->with('error', 'Tipe agenda tidak valid.');
    }

    $request->validate([
        'lokasi_spesifik'  => 'required|string|max:255',
        'hasil_rapat_file' => 'required|file|mimes:pdf,doc,docx,txt|max:20480',
        'foto_dokumentasi' => 'required|array|min:1',
        'foto_dokumentasi.*' => 'image|mimes:jpg,jpeg,png|max:20480',
    ]);

    try {
        DB::beginTransaction();

        // 1. Handle File Laporan (notulensi_hasil)
        $notulensiFile = $request->file('hasil_rapat_file')->store('notulensi_rapat', 'public');

        // 2. Handle Foto Dokumentasi (dokumentasi_path)
        $paths = [];
        if ($request->hasFile('foto_dokumentasi')) {
            foreach ($request->file('foto_dokumentasi') as $file) {
                $paths[] = $file->store('dokumentasi_rapat', 'public');
            }
        }

        // 3. Update Agenda (Hanya Record Ini Saja)
        // Karena setiap peserta lapor masing-masing
        $meeting->update([
            'location'         => $request->lokasi_spesifik,
            'notulensi_hasil'  => $notulensiFile,
            'dokumentasi_path' => json_encode($paths),
            'status_laporan'   => 'Selesai',
            'updated_at'       => now()
        ]);

        DB::commit();
        return redirect()->route('meeting.history')->with('success', 'Laporan Dinas Luar berhasil dikirim!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
    }
}





    /*
    |--------------------------------------------------------------------------
    | HISTORY & DELETE
    |--------------------------------------------------------------------------
    */
    public function listMeetingHistory(Request $request)
{
    $user = auth()->user();
    $query = Agenda::with(['notulis', 'creator', 'activityType'])
        ->where('activity_type_id', 2) // Khusus Rapat
        ->where('status_laporan', 'Selesai');

    if (!in_array($user->role, ['Admin', 'Kepala'])) {
        $query->where('assigned_to', $user->id);
    }

    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // Pakai unique karena Rapat bersifat kolektif
    $historyMeetings = $query->orderBy('event_date', 'desc')->get()->unique(function ($item) {
        return $item->title . $item->event_date;
    });

    return view('meeting.history', compact('historyMeetings'));
}

    /*
|--------------------------------------------------------------------------
| HISTORY KHUSUS DINAS LUAR
|--------------------------------------------------------------------------
*/
public function listDinasHistory(Request $request)
{
    $user = auth()->user();
    
    $query = Agenda::with(['assignee', 'creator', 'activityType'])
        ->where('activity_type_id', 3)
        ->where('status_laporan', 'Selesai'); // Harus 'Selesai' agar masuk riwayat

    // Filter: Pegawai hanya melihat laporannya sendiri
    if (!in_array($user->role, ['Admin', 'Kepala'])) {
        $query->where('assigned_to', $user->id);
    }

    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    $historyMeetings = $query->orderBy('event_date', 'desc')->get();

    return view('meeting.history', compact('historyMeetings'));
}

    public function destroyHistory($id)
    {
        $meeting = Agenda::findOrFail($id);

        if ($meeting->notulis_id != Auth::id() && !in_array(Auth::user()->role, ['Admin', 'Kepala'])) {
            return back()->with('error', 'Akses ditolak.');
        }

        try {
            DB::beginTransaction();

            if ($meeting->dokumentasi_path) {
                $files = json_decode($meeting->dokumentasi_path, true);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }

            if ($meeting->materi_path) {
                Storage::disk('public')->delete($meeting->materi_path);
            }

            Agenda::where('title', $meeting->title)
                ->where('event_date', $meeting->event_date)
                ->whereIn('activity_type_id', [2, 3])
                ->delete();

            DB::commit();
            return redirect()->route('meeting.history')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function detailHistory($id)
    {
        $meeting = Agenda::with(['creator', 'notulis', 'activityType'])->findOrFail($id);

        $semuaPeserta = Agenda::where('title', $meeting->title)
            ->where('event_date', $meeting->event_date)
            ->whereIn('activity_type_id', [2, 3])
            ->with('assignee.team')
            ->get();

        $userSudahHadir = MeetingPresence::whereIn('agenda_id', $semuaPeserta->pluck('id'))->pluck('user_id')->toArray();

        return view('meeting.detail_history', compact('meeting', 'semuaPeserta', 'userSudahHadir'));
    }
}