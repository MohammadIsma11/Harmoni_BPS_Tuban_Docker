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
        
        $query = Agenda::with(['assignee', 'activityType', 'creator', 'assignee.team'])
                    ->where('activity_type_id', 1) 
                    ->where('status_laporan', 'Selesai');

        // Filter berdasarkan Role
        if ($user->role == 'Katim') {
            $query->where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            });
        } elseif ($user->role == 'Pegawai') {
            $query->where('assigned_to', $user->id);
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhereHas('assignee', function($userQuery) use ($search) {
                      $userQuery->where('nama_lengkap', 'like', '%' . $search . '%');
                  });
            });
        }

        $riwayat = $query->latest('updated_at')->paginate(10);
        return view('history.index', compact('riwayat'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL & EXPORT
    |--------------------------------------------------------------------------
    */
    public function historyDetail($id)
    {
        $agenda = Agenda::with(['assignee', 'activityType', 'photos'])->findOrFail($id);

        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        return view('history.detail', compact('agenda'));
    }

    public function historyExport($id) 
    {
        Carbon::setLocale('id');
        $agenda = Agenda::with(['assignee.team', 'activityType', 'photos'])->findOrFail($id);
        
        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        $kepala = User::where('role', 'Kepala')->first();
        $pdf = Pdf::loadView('history.pdf', compact('agenda', 'kepala'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Lapangan_' . $agenda->id . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT & UPDATE
    |--------------------------------------------------------------------------
    */
    public function historyEdit($id)
    {
        $agenda = Agenda::with(['activityType', 'photos'])->findOrFail($id);

        if (!$this->canAccess($agenda)) {
            return redirect()->route('history.index')->with('error', 'Akses ditolak.');
        }

        return view('history.edit', compact('agenda'));
    }

    public function historyUpdate(Request $request, $id)
    {
        $agenda = Agenda::findOrFail($id);

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
            
            // Gabung lokasi
            $lokasiLengkap = "Desa " . $request->desa . ", Kec. " . $request->kecamatan;

            // Update data (Pastikan nama kolom database adalah tanggal_riil)
            $agenda->update([
                'location' => $lokasiLengkap,
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan, // <--- SYNC INI
                'responden' => $request->responden,
                'aktivitas' => $request->aktivitas,
                'permasalahan' => $request->permasalahan,
                'solusi_antisipasi' => $request->solusi_antisipasi,
            ]);

            // Ganti Foto jika ada file baru
            if ($request->hasFile('fotos')) {
                // Hapus yang lama dulu
                foreach ($agenda->photos as $oldPhoto) {
                    \Storage::disk('public')->delete($oldPhoto->photo_path);
                    $oldPhoto->delete();
                }

                // Simpan yang baru
                foreach ($request->file('fotos') as $foto) {
                    $path = $foto->store('dokumentasi', 'public');
                    AgendaPhoto::create([
                        'agenda_id' => $agenda->id, 
                        'photo_path' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('history.index')->with('success', 'Laporan diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

       public function taskDestroy($id)
{
    $agenda = Agenda::with('photos')->findOrFail($id);
    
    // Keamanan: Cek apakah user boleh menghapus laporan ini
    if (!$this->canAccess($agenda)) {
        abort(403, 'Akses hapus laporan ditolak.');
    }

    try {
        DB::beginTransaction();

        // 1. Hapus Foto dari Storage & Database
        foreach ($agenda->photos as $photo) {
            if (\Storage::disk('public')->exists($photo->photo_path)) {
                \Storage::disk('public')->delete($photo->photo_path);
            }
            $photo->delete();
        }

        // 2. Opsi A: Hapus Total Baris Agenda
        $agenda->delete(); 
        
        // ATAU Opsi B: Reset ke Status 'Pending' (jika ingin tugasnya muncul lagi di daftar tugas)
        /*
        $agenda->update([
            'status_laporan' => 'Pending',
            'tanggal_riil' => null,
            'responden' => null,
            'aktivitas' => null,
            // ... reset field lainnya
        ]);
        */

        DB::commit();
        return redirect()->route('history.index')->with('success', 'Laporan berhasil dihapus.');

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