<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStatusCuti
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 2. Cek apakah hari ini user sedang cuti di tabel Absensi
        // Kita gunakan query builder yang lebih clean
        $isCuti = Absensi::where('user_id', $user->id)
            ->where('status', 'Cuti')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->exists();

        if ($isCuti) {
            // 3. Jika akses lewat AJAX/API (saat tekan tombol simpan laporan)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak. Anda sedang dalam masa cuti.'
                ], 403);
            }

            // 4. Jika akses halaman via browser (GET), lempar balik ke daftar tugas atau dashboard
            // Saya arahkan ke task.index agar user tahu dia balik ke halaman list tugas
            return redirect()->route('task.index')->with('error', 'Akses Terblokir! Anda tidak dapat mengisi laporan karena status Anda hari ini adalah CUTI.');
        }

        return $next($request);
    }
}