<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAccessController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();

    $query = Agenda::with(['assignee', 'notulis', 'creator', 'activityType'])
                   ->where('status_laporan', 'Selesai');

    // LOGIKA FILTER DATA BERDASARKAN ROLE
    if ($user->role == 'Kepala' || $user->has_super_access == 1) {
        // POV Kepala / Pegawai (Super Access): Lihat semua riwayat tanpa filter
    } elseif ($user->role == 'Katim') {
        // POV Katim: Hanya melihat riwayat tugas yang dia berikan
        // Kita gunakan kolom 'user_id' sesuai struktur databasemu
        $query->where('user_id', $user->id); 
    } else {
        // Pegawai yang belum mengaktifkan has_super_access di profil
        abort(403, 'Silahkan aktifkan Akses Monitoring di Profil Anda.');
    }

    // Filter Pencarian Judul
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // Filter Tipe Kegiatan
    if ($request->filled('type')) {
        $query->where('activity_type_id', $request->type);
    }

    $allActivities = $query->orderBy('event_date', 'desc')->paginate(20);

    return view('super_access.index', compact('allActivities'));
}
}