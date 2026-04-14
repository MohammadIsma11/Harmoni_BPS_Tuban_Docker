<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class HistoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST RIWAYAT LAPORAN LAPANGAN
    |--------------------------------------------------------------------------
    */
    public function historyIndex(Request $request)
    {
        $user = auth()->user();
        
        // Pivot ke AssignmentReport agar muncul per translok/laporan
        $query = \App\Models\AssignmentReport::with(['agenda', 'agenda.assignee', 'agenda.creator', 'agenda.assignee.team'])
                    ->whereHas('agenda', function($q) {
                        $q->where('activity_type_id', 1); // Khusus Lapangan
                    });

        // Filter berdasarkan Role
        if ($user->role == 'Katim') {
            $query->whereHas('agenda', function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            });
        } elseif ($user->role == 'Pegawai') {
            $query->where('user_id', $user->id);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('agenda', function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('lokasi_tujuan', 'like', '%' . $search . '%');
            });
        }

        $riwayat = $query->latest()->paginate(10);
        return view('history.index', compact('riwayat'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL & EXPORT
    |--------------------------------------------------------------------------
    */
    public function historyDetail($id)
    {
        // $id adalah ID AssignmentReport
        $report = \App\Models\AssignmentReport::with(['agenda', 'agenda.assignee', 'agenda.creator', 'agenda.photos'])->findOrFail($id);
        $agenda = $report->agenda;

        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        // Parse isi_laporan dari JSON
        $details = json_decode($report->isi_laporan, true);

        return view('history.detail', compact('report', 'agenda', 'details'));
    }

    public function historyExport($id) 
    {
        Carbon::setLocale('id');
        $report = \App\Models\AssignmentReport::with(['agenda', 'agenda.assignee.team', 'agenda.creator', 'agenda.photos'])->findOrFail($id);
        $agenda = $report->agenda;
        
        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        $details = json_decode($report->isi_laporan, true);
        $kepala = User::where('role', 'Kepala')->first();
        
        $pdf = Pdf::loadView('history.pdf', compact('report', 'agenda', 'details', 'kepala'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Translok_' . $report->id . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT & UPDATE
    |--------------------------------------------------------------------------
    */
    public function historyEdit($id)
    {
        // $id adalah ID AssignmentReport
        $report = \App\Models\AssignmentReport::with(['agenda', 'agenda.activityType', 'agenda.photos'])->findOrFail($id);
        $agenda = $report->agenda;

        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        $details = json_decode($report->isi_laporan, true);

        return view('history.edit', compact('report', 'agenda', 'details'));
    }

    public function historyUpdate(Request $request, $id)
    {
        // $id adalah ID AssignmentReport
        $report = \App\Models\AssignmentReport::with('agenda')->findOrFail($id);
        $agenda = $report->agenda;

        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'kecamatan' => 'required',
            'desa' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'responden' => 'required',
            'aktivitas' => 'required',
            'permasalahan' => 'required',
            'solusi_antisipasi' => 'required',
            'fotos.*' => 'image|mimes:jpeg,png,jpg|max:10240',
        ]);

        try {
            DB::beginTransaction();
            
            $lokasiLengkap = "Desa " . $request->desa . ", Kec. " . $request->kecamatan;
            $reportData = [
                'responden'         => $request->responden,
                'aktivitas'         => $request->aktivitas,
                'permasalahan'      => $request->permasalahan,
                'solusi_antisipasi' => $request->solusi_antisipasi
            ];

            // 1. Update Report
            $report->update([
                'lokasi_tujuan' => $lokasiLengkap,
                'tanggal_lapor' => $request->tanggal_pelaksanaan,
                'isi_laporan'   => json_encode($reportData),
            ]);

            // 2. Update Agenda snapshot (selalu ambil yang terbaru dari laporan)
            $agenda->update([
                'location'            => $lokasiLengkap,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'responden'           => $request->responden,
                'aktivitas'           => $request->aktivitas,
                'permasalahan'        => $request->permasalahan,
                'solusi_antisipasi'   => $request->solusi_antisipasi,
            ]);

            // 3. Update Foto (Jika ada file baru)
            if ($request->hasFile('fotos')) {
                // Hapus yang lama di agenda_photos untuk agenda ini 
                // (Catatan: ini akan menghapus SEMUA dokumentasi agenda tersebut)
                foreach ($agenda->photos as $oldPhoto) {
                    \Storage::disk('public')->delete($oldPhoto->photo_path);
                    $oldPhoto->delete();
                }

                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('dokumentasi_tugas', 'public');
                    \App\Models\AgendaPhoto::create([
                        'agenda_id'  => $agenda->id, 
                        'photo_path' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('history.index')->with('success', 'Laporan translok diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function taskDestroy($id)
    {
        // $id adalah ID AssignmentReport
        $report = \App\Models\AssignmentReport::with('agenda')->findOrFail($id);
        $agenda = $report->agenda;
        
        if (!$this->canAccess($agenda)) {
            abort(403, 'Akses hapus laporan ditolak.');
        }

        try {
            DB::beginTransaction();

            // 1. Hapus Report Terkait
            $report->delete();

            // 2. Jika laporan habis, maka agenda dikembalikan ke Pending atau dihapus?
            // User mau tugas balik ke Daftar Tugas jika belum selesai.
            $reportCount = $agenda->reports()->count();
            if ($reportCount == 0) {
                // Hapus juga foto jika ini laporan terakhir? 
                foreach ($agenda->photos as $photo) {
                    if (\Storage::disk('public')->exists($photo->photo_path)) {
                        \Storage::disk('public')->delete($photo->photo_path);
                    }
                    $photo->delete();
                }
                $agenda->update(['status_laporan' => 'Pending']);
            } else {
                // Masih ada laporan lain, biarkan tetap status sekarang? 
                // Tapi jika report target > count, kembalikan ke Pending.
                if ($reportCount < ($agenda->report_target ?? 1)) {
                    $agenda->update(['status_laporan' => 'Pending']);
                }
            }

            DB::commit();
            return redirect()->route('history.index')->with('success', 'Laporan translok berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    public function exportRekapPDF()
    {
        $riwayat = $this->getFilteredHistoryData();
        $pdf = Pdf::loadView('history.pdf_rekap', compact('riwayat'))->setPaper('f4', 'portrait');
        return $pdf->download('Rekap_Laporan_BPS_'.date('dmy').'.pdf');
    }

    public function exportRekapExcel()
    {
        $riwayat = $this->getFilteredHistoryData();
        $fileName = 'Rekap_Laporan_BPS_'.date('dmy_His').'.xls';
        $html = view('history.excel_rekap', compact('riwayat'))->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"$fileName\"")
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function getFilteredHistoryData()
{
    $user = auth()->user();
    $query = Agenda::with(['assignee.team', 'activityType'])
                ->where('activity_type_id', 1) 
                ->where('status_laporan', 'Selesai');

    if ($user->role == 'Katim') {
        $query->where(function($q) use ($user) {
            $q->where('assigned_to', $user->id)->orWhere('user_id', $user->id);
        });
    } elseif ($user->role == 'Pegawai') {
        $query->where('assigned_to', $user->id);
    }

    return $query->latest('updated_at')->get();
}

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: SECURITY GATE
    |--------------------------------------------------------------------------
    */
    private function canAccess($agenda): bool
{
    $user = Auth::user();

    // Admin & Kepala selalu bisa akses/hapus apapun
    if (in_array($user->role, ['Admin', 'Kepala'])) {
        return true;
    }

    // Katim bisa akses miliknya sendiri atau laporan yang dia buatkan untuk anggotanya
    if ($user->role === 'Katim') {
        return $agenda->assigned_to == $user->id || $agenda->user_id == $user->id;
    }

    // Pegawai HANYA bisa akses/hapus laporan yang ditugaskan ke DIA SENDIRI
    if ($user->role === 'Pegawai') {
        return $agenda->assigned_to == $user->id;
    }

    return false;
}
}