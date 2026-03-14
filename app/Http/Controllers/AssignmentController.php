<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agenda;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * DAFTAR PENUGASAN (Monitoring)
     */
    public function assignmentIndex()
    {
        $user = Auth::user();
        $query = Agenda::with(['assignee', 'activityType']);

        if ($user->role !== 'Kepala' && $user->role !== 'Admin') {
            $query->where('user_id', $user->id);
        }

        $assignments = $query->orderBy('event_date', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->title . $item->event_date . $item->nomor_surat_tugas;
            });

        return view('assignment.index', compact('assignments'));
    }

    /**
     * FORM BUAT PENUGASAN
     */
    public function assignmentCreate()
    {
        $types = ActivityType::all();
        $kepalas = User::where('role', 'Kepala')->orderBy('nama_lengkap', 'asc')->get();
        $katims = User::where('role', 'Katim')->orderBy('nama_lengkap', 'asc')->get();
        $pegawais = User::where('role', 'Pegawai')->orderBy('nama_lengkap', 'asc')->get();

        return view('assignment.create', compact('kepalas', 'katims', 'pegawais', 'types'));
    }

    /**
     * SIMPAN PENUGASAN (Support 3 Jenis Kegiatan)
     */
    public function assignmentStore(Request $request)
    {
        $type = $request->activity_type_id;

        // 1. Aturan Validasi Dasar
        $rules = [
            'activity_type_id'  => 'required|exists:activity_types,id',
            'title'             => 'required|string|max:255',
            'assigned_to'       => 'required|array|min:1',
            'event_date'        => 'required|date|after_or_equal:today',
            'description'       => 'nullable|string',
            'surat_tugas'       => 'required|file|mimes:pdf|max:20480',
        ];

        // 2. Validasi Dinamis Berdasarkan Jenis (Sesuai Permintaan)
        if ($type == 1) { // TUGAS LAPANGAN
            $rules['nomor_surat_tugas'] = 'required|string';
            $rules['end_date']          = 'required|date|after_or_equal:event_date';
        } elseif ($type == 2) { // RAPAT DINAS
            $rules['start_time']        = 'required';
            $rules['notulis_id']        = 'required|exists:users,id';
        } elseif ($type == 3) { // DINAS LUAR
            $rules['start_time']        = 'required';
            $rules['notulis_id']        = 'nullable';
        }

        $request->validate($rules);

        // 3. Handle File Upload
        $stPath = $request->file('surat_tugas')->store('dokumen_tugas', 'public');

        try {
            DB::transaction(function () use ($request, $stPath, $type) {
                foreach ($request->assigned_to as $pegawai_id) {
                    Agenda::create([
                        'user_id'           => auth()->id(),
                        'assigned_to'       => $pegawai_id,
                        'activity_type_id'  => $type,
                        'title'             => $request->title,
                        'description'       => $request->description,
                        // Jika bukan lapangan, No ST diisi '-' agar tidak null
                        'nomor_surat_tugas' => ($type == 1) ? $request->nomor_surat_tugas : ($request->nomor_surat_tugas ?? '-'),
                        'event_date'        => $request->event_date,
                        // Jika bukan lapangan, end_date disamakan dengan event_date
                        'end_date'          => ($type == 1) ? $request->end_date : $request->event_date,
                        'start_time'        => ($type == 1) ? null : $request->start_time,
                        'notulis_id'        => ($type == 2) ? $request->notulis_id : null,
                        'surat_tugas_path'  => $stPath,
                        'status_laporan'    => 'Pending',
                    ]);
                }
            });

            return redirect()->route('assignment.index')->with('success', 'Penugasan berhasil disimpan!');
        } catch (\Exception $e) {
            if ($stPath) Storage::disk('public')->delete($stPath);
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * FORM EDIT
     */
    public function assignmentEdit($id)
{
    // 1. Ambil data dengan Eager Loading
    $assignment = Agenda::with(['assignee', 'activityType'])->findOrFail($id);
    
    // 2. Keamanan Akses
    if ($assignment->user_id !== Auth::id() && !in_array(Auth::user()->role, ['Admin', 'Kepala'])) {
        return back()->with('error', 'Akses ditolak.');
    }

    // 3. Pastikan format tanggal benar untuk HTML5 Date Input (Y-m-d)
    // Jika kolom event_date sudah di-cast sebagai date di Model, gunakan format()
    // Jika masih string, kita parse dulu.
    $assignment->event_date = \Carbon\Carbon::parse($assignment->event_date)->format('Y-m-d');
    if ($assignment->end_date) {
        $assignment->end_date = \Carbon\Carbon::parse($assignment->end_date)->format('Y-m-d');
    }

    // 4. Ambil Master Data
    $types = ActivityType::all();
    
    // Ambil User berdasarkan Role (Sesuaikan huruf besar kecilnya dengan database)
    $kepalas = User::whereIn('role', ['Kepala', 'Kepala BPS'])->orderBy('nama_lengkap', 'asc')->get();
    $katims = User::whereIn('role', ['Katim', 'Ketua Tim'])->orderBy('nama_lengkap', 'asc')->get();
    
    // Pegawai/Staf
    $pegawais = User::whereIn('role', ['Pegawai', 'Staf', 'Pegawai Biasa'])->orderBy('nama_lengkap', 'asc')->get();

    return view('assignment.edit', compact('assignment', 'types', 'kepalas', 'katims', 'pegawais'));
}

/**
 * UPDATE PENUGASAN (Re-Assign System)
 */
public function assignmentUpdate(Request $request, $id)
{
    $assignment = Agenda::findOrFail($id);
    $type = $request->activity_type_id;

    $rules = [
        'activity_type_id'  => 'required|exists:activity_types,id',
        'title'             => 'required|string|max:255',
        'assigned_to'       => 'required|array|min:1',
        'event_date'        => 'required|date',
        'surat_tugas'       => 'nullable|file|mimes:pdf|max:20480',
    ];

    // Aturan update dinamis sesuai jenis kegiatan
    if ($type == 1) { // Tugas Lapangan
        $rules['nomor_surat_tugas'] = 'required';
        $rules['end_date'] = 'required|date|after_or_equal:event_date';
    } elseif ($type == 2) { // Rapat
        $rules['start_time'] = 'required';
        $rules['notulis_id'] = 'required';
    } elseif ($type == 3) { // Dinas Luar
        $rules['start_time'] = 'required';
    }

    $request->validate($rules);

    try {
        \DB::transaction(function () use ($request, $assignment, $type) {
            
            // 1. Ambil semua orang di rangkaian yang sama sebelum dihapus
            // Mencocokkan berdasarkan Title, Tanggal, dan Nomor ST yang lama
            $oldRecords = Agenda::where('title', $assignment->getOriginal('title'))
                                ->where('event_date', $assignment->getOriginal('event_date'))
                                ->where('nomor_surat_tugas', $assignment->getOriginal('nomor_surat_tugas'))
                                ->get();

            // 2. Handle File (Gunakan yang lama jika tidak upload baru)
            $stPath = $assignment->surat_tugas_path;
            if ($request->hasFile('surat_tugas')) {
                // Hapus file lama jika ada
                if ($stPath && \Storage::disk('public')->exists($stPath)) {
                    \Storage::disk('public')->delete($stPath);
                }
                $stPath = $request->file('surat_tugas')->store('dokumen_tugas', 'public');
            }

            // 3. Hapus record lama agar tidak duplikat saat plotting ulang
            foreach ($oldRecords as $rec) {
                $rec->delete();
            }

            // 4. Buat Record Baru untuk setiap petugas yang dipilih
            foreach ($request->assigned_to as $pegawai_id) {
                Agenda::create([
                    'user_id'           => auth()->id(),
                    'assigned_to'       => $pegawai_id,
                    'activity_type_id'  => $type,
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'nomor_surat_tugas' => ($type == 1) ? $request->nomor_surat_tugas : ($request->nomor_surat_tugas ?? '-'),
                    'event_date'        => $request->event_date,
                    'end_date'          => ($type == 1) ? $request->end_date : $request->event_date,
                    'start_time'        => ($type == 1) ? null : $request->start_time,
                    'notulis_id'        => ($type == 2) ? $request->notulis_id : null,
                    'surat_tugas_path'  => $stPath,
                    'status_laporan'    => 'Pending',
                ]);
            }
        });

        return redirect()->route('assignment.index')->with('success', 'Rangkaian penugasan berhasil diperbarui!');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
    }
}

    /**
     * HAPUS RANGKAIAN TUGAS
     */
    public function assignmentDestroy($id)
    {
        $assignment = Agenda::findOrFail($id);
        
        try {
            $group = Agenda::where('title', $assignment->title)
                           ->where('event_date', $assignment->event_date)
                           ->where('nomor_surat_tugas', $assignment->nomor_surat_tugas)
                           ->get();

            foreach ($group as $item) {
                if ($item->surat_tugas_path) Storage::disk('public')->delete($item->surat_tugas_path);
                $item->delete();
            }
            return back()->with('success', 'Seluruh rangkaian penugasan dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus.');
        }
    }

    /**
     * CHECK AVAILABILITY AJAX
     */
    public function checkAvailability(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date ?? $start;
        $busyUsers = Agenda::where(function($query) use ($start, $end) {
                $query->where('event_date', '<=', $end)->where('end_date', '>=', $start);
            })
            ->where('status_laporan', '!=', 'Selesai') 
            ->pluck('assigned_to')->unique()->toArray();

        return response()->json(['busy_users' => array_values(array_map('intval', $busyUsers))]);
    }
}