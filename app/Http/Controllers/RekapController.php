<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REKAP HONOR MITRA (Pivot Table)
    |--------------------------------------------------------------------------
    */

    public function rekapHonor(Request $request)
    {
        $selectedYear = $request->get('tahun', date('Y'));
        
        // 1. Ambil semua mitra
        $mitras = Mitra::orderBy('nama_lengkap', 'asc')->get();

        // 2. Ambil data pembayaran per bulan yang di-pivot
        // Kita gunakan query grouping agar efisien
        $payments = DB::table('t_pembayaran')
            ->join('t_penugasan', 't_pembayaran.penugasan_id', '=', 't_penugasan.id')
            ->where('t_pembayaran.bulan_bayar', 'like', $selectedYear . '-%')
            ->select(
                't_penugasan.mitra_id',
                't_pembayaran.bulan_bayar',
                DB::raw('SUM(t_pembayaran.nominal_cair) as total_honor')
            )
            ->groupBy('t_penugasan.mitra_id', 't_pembayaran.bulan_bayar')
            ->get();

        // 3. Transformasi data agar mudah diakses di Blade: [mitra_id][bulan] = total
        $pivotData = [];
        foreach ($payments as $p) {
            $month = substr($p->bulan_bayar, 5, 2); // Ambil 'MM' dari 'YYYY-MM'
            $pivotData[$p->mitra_id][$month] = $p->total_honor;
        }

        // 4. Ambil list tahun yang tersedia di data pembayaran untuk filter
        $availableYears = DB::table('t_pembayaran')
            ->select(DB::raw('SUBSTRING(bulan_bayar, 1, 4) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('rekap.honor', compact('mitras', 'pivotData', 'selectedYear', 'availableYears'));
    }

    /**
     * AJAX: Ambil detail penugasan yang membentuk honor mitra di bulan tertentu
     */
    public function getDetailHonor(Request $request)
    {
        $sobatId = $request->sobat_id;
        $month = $request->month; // Format: 'YYYY-MM'

        $details = Pembayaran::with(['penugasan.anggaran', 'penugasan.team'])
            ->whereHas('penugasan', function($q) use ($sobatId) {
                $q->where('mitra_id', $sobatId);
            })
            ->where('bulan_bayar', $month)
            ->get()
            ->map(function($p) {
                return [
                    'kegiatan' => $p->penugasan->nama_kegiatan_manual ?? ($p->penugasan->anggaran->nama_pagu ?? '-'),
                    'tim' => $p->penugasan->team->nama_tim ?? '-',
                    'volume' => $p->penugasan->volume . ' ' . $p->penugasan->satuan,
                    'total_honor_tugas' => $p->penugasan->total_honor_tugas,
                    'nominal_cair_bulan_ini' => $p->nominal_cair,
                ];
            });

        return response()->json($details);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPER
    |--------------------------------------------------------------------------
    */

    private function generateFileName(string $extension): string
    {
        $timestamp = Carbon::now()->format('dmy_His');
        return "Rekap_Laporan_BPS_{$timestamp}.{$extension}";
    }
}