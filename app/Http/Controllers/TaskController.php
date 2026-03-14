<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\AgendaPhoto;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST TASK (HANYA YANG SUDAH HARI-H)
    |--------------------------------------------------------------------------
    */
    public function taskIndex()
{
    $user = auth()->user();
    
    // Kita hapus filter 'event_date' <= $today agar tugas muncul seketika setelah di-assign
    $tugas = Agenda::with(['activityType', 'creator'])
        ->where('assigned_to', $user->id)
        ->where('activity_type_id', 1) // Khusus Tugas Lapangan
        ->where('status_laporan', 'Pending')
        // Filter tanggal dihapus: "Begitu di-plot, langsung muncul"
        ->orderBy('event_date', 'asc') // Urutkan dari yang paling dekat tanggalnya
        ->get();

    return view('task.index', compact('tugas'));
}

    /*
    |--------------------------------------------------------------------------
    | FORM INPUT LAPORAN
    |--------------------------------------------------------------------------
    */
   public function taskCreate($id) 
{
    $user = auth()->user();
    $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

    // 1. Cek Proteksi Cuti (Tetap Pertahankan)
    $sedangCuti = Absensi::where('user_id', $user->id)
        ->where('status', 'Cuti')
        ->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)
        ->exists();

    if ($sedangCuti) {
        return redirect()->route('task.index')->with('error', 'Akses lapor ditolak karena Anda sedang dalam masa CUTI.');
    }

    // 2. Ambil Agenda
    // KUNCI: Hapus filter 'event_date' <= $today agar pegawai bisa mencicil laporan 
    // atau setidaknya membuka form begitu tugas diberikan.
    $agenda = Agenda::with(['activityType', 'team'])
        ->where('id', $id)
        ->where('assigned_to', $user->id)
        // ->where('event_date', '<=', $today) // BARIS INI DIHAPUS agar tidak 404
        ->firstOrFail();

    return view('task.create', compact('agenda'));
}

    /*
    |--------------------------------------------------------------------------
    | SIMPAN LAPORAN
    |--------------------------------------------------------------------------
    */
    public function taskStore(Request $request, $id) 
{
    $user = auth()->id();
    // Pastikan agenda memang milik user yang login dan bertipe tugas lapangan
    $agenda = Agenda::where('id', $id)->where('assigned_to', $user)->firstOrFail();
    
    $request->validate([
        'kecamatan' => ['required', 'string'],
        'desa' => ['required', 'string'],
        'tanggal_pelaksanaan' => ['required', 'date'],
        'responden' => ['required', 'string', 'max:255'],
        'aktivitas' => ['required', 'string'],
        'permasalahan' => ['required', 'string'],
        'solusi_antisipasi' => ['required', 'string'],
        'fotos' => ['required', 'array', 'min:1', 'max:6'], // Minimal 1, maksimal 6 foto
        'fotos.*' => ['image', 'mimes:jpeg,png,jpg', 'max:10240'], // Max 10MB per foto
    ], [
        'fotos.required' => 'Wajib mengunggah minimal 1 foto dokumentasi.',
        'fotos.max' => 'Maksimal unggah 6 foto saja.',
        'fotos.*.max' => 'Ukuran setiap foto tidak boleh lebih dari 10MB.'
    ]);

    try {
        DB::beginTransaction();

        // 1. LOGIKA KUNCI: Gabung Desa & Kecamatan ke dalam kolom 'location'
        // Format: "Desa [Nama Desa], Kec. [Nama Kecamatan]"
        $lokasiLengkap = "Desa " . $request->desa . ", Kec. " . $request->kecamatan;

        // 2. Upload Foto Dokumentasi
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if ($foto->isValid()) {
                    $path = $foto->store('dokumentasi_tugas', 'public');
                    
                    // Simpan ke tabel relasi AgendaPhoto
                    \App\Models\AgendaPhoto::create([
                        'agenda_id' => $agenda->id, 
                        'photo_path' => $path
                    ]);
                }
            }
        }

        // 3. Update Data Agenda Utama
        $agenda->update([
            'location' => $lokasiLengkap, // Menyimpan format gabungan ke kolom location
            'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan, // Pastikan nama kolom di DB sesuai (tanggal_riil / tanggal_pelaksanaan)
            'responden' => $request->responden,
            'aktivitas' => $request->aktivitas,
            'permasalahan' => $request->permasalahan,
            'solusi_antisipasi' => $request->solusi_antisipasi,
            'status_laporan' => 'Selesai',
            'updated_at' => now()
        ]);

        DB::commit();
        return redirect()->route('history.index')->with('success', 'Laporan tugas berhasil dikirim dan diarsipkan!');

    } catch (\Exception $e) {
        DB::rollback();
        // Log error bisa ditambahkan di sini jika perlu: \Log::error($e->getMessage());
        return back()->withInput()->with('error', 'Gagal menyimpan laporan: ' . $e->getMessage());
    }
}
}