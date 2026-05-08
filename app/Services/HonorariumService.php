<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Pembayaran;
use App\Models\Mitra;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HonorariumService
{
    /**
     * Menghitung dan menjadwalkan pembayaran untuk penugasan baru.
     * Menggunakan logika FIFO dan pemecahan bulan otomatis jika melebihi plafon.
     */
    public function schedulePayments(Penugasan $penugasan)
    {
        return DB::transaction(function () use ($penugasan) {
            $monthString = Carbon::parse($penugasan->tgl_selesai_target)->format('Y-m');
            
            $payment = Pembayaran::create([
                'penugasan_id' => $penugasan->id,
                'nominal_cair' => $penugasan->total_honor_tugas,
                'bulan_bayar'  => $monthString,
                'status_bayar' => 'Antre',
                'keterangan'   => 'Pembayaran penuh/pelunasan',
            ]);

            return [$payment];
        });
    }

    /**
     * Cek sisa kuota mitra untuk bulan tertentu
     */
    public function getRemainingQuota(string $sobatId, string $monthYear)
    {
        $mitra = Mitra::findOrFail($sobatId);
        
        $alreadyScheduled = Pembayaran::whereHas('penugasan', function($q) use ($sobatId) {
                $q->where('mitra_id', $sobatId);
            })
            ->where('bulan_bayar', $monthYear)
            ->sum('nominal_cair');

        return max(0, $mitra->max_honor_bulanan - $alreadyScheduled);
    }

    /**
     * Rangkuman honor seluruh mitra untuk bulan tertentu
     */
    public function getMitraHonorSummaries(string $month)
    {
        return Mitra::select('sobat_id', 'nama_lengkap', 'max_honor_bulanan', 'alamat_kec')
            ->get()
            ->map(function($mitra) use ($month) {
                $totalHonor = Pembayaran::whereHas('penugasan', function($q) use ($mitra) {
                        $q->where('mitra_id', $mitra->sobat_id);
                    })
                    ->where('bulan_bayar', $month)
                    ->sum('nominal_cair');
                
                $mitra->total_honor_month = $totalHonor;
                $mitra->remaining_quota = max(0, $mitra->max_honor_bulanan - $totalHonor);
                return $mitra;
            })
            ->sortBy('total_honor_month'); // Rekomendasi: Terkecil di atas
    }
}
