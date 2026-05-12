<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Anggaran;
use App\Models\Mitra;
use App\Models\Pembayaran;
use App\Models\Penugasan;
use App\Services\HonorariumService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PenugasanController extends Controller
{
    protected $honorService;

    public function __construct(HonorariumService $honorService)
    {
        $this->honorService = $honorService;
    }

    public function index()
    {
        $penugasans = Penugasan::with(['mitra', 'anggaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('penugasan-mitra.index', compact('penugasans'));
    }

    public function create()
    {
        $month = now()->format('Y-m');
        $mitras = $this->honorService->getMitraHonorSummaries($month);
        $anggarans = Anggaran::with('team')->orderBy('nama_pagu', 'asc')->get();
        // Ambil semua tim kecuali Kepala BPS dan Subbagian Umum (ID 1)
        $teams = \App\Models\Team::where('id', '!=', 1)
            ->where('nama_tim', 'not like', '%Kepala%')
            ->orderBy('nama_tim', 'asc')->get();
        
        return view('penugasan-mitra.create', compact('mitras', 'anggarans', 'teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'anggaran_id' => 'nullable|exists:m_anggaran,id',
            'nama_kegiatan_manual' => 'nullable|required_without:anggaran_id|string|max:200',
            'kategori_kegiatan' => 'required|in:Pendataan,Pemeriksaan,Pengolahan',
            'team_id' => 'required|exists:teams,id',
            'tgl_mulai' => 'required|date',
            'tgl_selesai_target' => 'required|date|after_or_equal:tgl_mulai',
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:0',
            'no_spk' => 'nullable|string|max:100',
            'no_bast' => 'nullable|string|max:100',
            'mitra_ids' => 'required|array',
            'mitra_ids.*' => 'required|exists:m_mitra,sobat_id',
            'volumes' => 'required|array',
            'file_pendukung' => 'nullable|file|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $request->file('file_pendukung')->store('honor_dokumen', 'public');
        }

        $count = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $filePath, &$count) {
            foreach ($request->mitra_ids as $mitraId) {
                $volume = $request->volumes[$mitraId] ?? 0;
                $totalHonor = $volume * $request->harga_satuan;

                $penugasan = Penugasan::create([
                    'anggaran_id' => $request->anggaran_id,
                    'nama_kegiatan_manual' => $request->nama_kegiatan_manual,
                    'kategori_kegiatan' => $request->kategori_kegiatan,
                    'mitra_id' => $mitraId,
                    'user_id' => auth()->id(),
                    'team_id' => $request->team_id,
                    'tgl_mulai' => $request->tgl_mulai,
                    'tgl_selesai_target' => $request->tgl_selesai_target,
                    'satuan' => $request->satuan,
                    'volume' => $volume,
                    'harga_satuan' => $request->harga_satuan,
                    'total_honor_tugas' => $totalHonor,
                    'no_spk' => $request->no_spk,
                    'no_bast' => $request->no_bast,
                    'status_tugas' => 'Progres',
                    'status_dokumen' => 'Pending',
                    'file_pendukung' => $filePath,
                    'lokasi_tugas' => $request->lokasi_tugas,
                ]);

                // Automatis buat jadwal pembayaran
                $this->honorService->schedulePayments($penugasan);
                $count++;
            }
        });

        return redirect()->route('penugasan-mitra.index')->with('success', $count . ' penugasan mitra berhasil dibuat.');
    }

    public function checkQuota(Request $request)
    {
        $sobatId = $request->sobat_id;
        $month = $request->month ?: now()->format('Y-m');
        
        $quota = $this->honorService->getRemainingQuota($sobatId, $month);
        
        return response()->json([
            'remaining_quota' => $quota,
            'formatted_quota' => 'Rp ' . number_format($quota, 0, ',', '.')
        ]);
    }

    /**
     * WORKFLOW GATEKEEPER (UMUM)
     */
    public function gatekeeperIndex()
    {
        $penugasans = Penugasan::with(['mitra', 'anggaran', 'user'])
            ->where('status_dokumen', '!=', 'Lengkap')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('honorarium.verifikasi', compact('penugasans'));
    }

    public function updateStatusDokumen(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $updateData = ['status_dokumen' => $request->status];
        
        // Fitur Jalur Cepat: Jika di-set 'Lengkap' dan ada request 'finish_task', set status_tugas juga
        if ($request->status == 'Lengkap' && $request->finish_task) {
            $updateData['status_tugas'] = 'Selesai';
        }
        
        $penugasan->update($updateData);
        
        return back()->with('success', 'Status dokumen ' . ($request->finish_task ? '& penugasan ' : '') . 'diperbarui.');
    }

    public function updateStatusTugas(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $penugasan->update(['status_tugas' => $request->status]);
        
        return back()->with('success', 'Status progres penugasan diperbarui menjadi ' . $request->status . '.');
    }

    /**
     * Delete Penugasan and related Payments
     */
    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);
        
        // Hapus pembayaran terkait jika belum lunas
        \App\Models\Pembayaran::where('penugasan_id', $id)
            ->where('status_bayar', 'Antre')
            ->delete();

        $penugasan->delete();

        return redirect()->route('penugasan-mitra.index')->with('success', 'Penugasan berhasil dihapus.');
    }

    public function paymentIndex()
    {
        $payments = Pembayaran::with(['penugasan.mitra', 'penugasan.anggaran'])
            ->where('status_bayar', 'Antre')
            ->orderBy('bulan_bayar', 'asc')
            ->get();
        return view('honorarium.pembayaran', compact('payments'));
    }

    public function bulkConfirmPayment(Request $request)
    {
        $paymentIds = $request->ids; // Array of IDs from checkbox
        if (!$paymentIds || count($paymentIds) == 0) {
            return back()->with('error', 'Pilih minimal satu data untuk dikonfirmasi.');
        }

        Pembayaran::whereIn('id', $paymentIds)->update(['status_bayar' => 'Lunas']);
        
        return back()->with('success', count($paymentIds) . ' pembayaran berhasil dikonfirmasi Lunas.');
    }

    /**
     * Generate SPK (Support multiple activities grouped by no_spk)
     */
    public function generateSPK($id)
    {
        $primary = Penugasan::with(['mitra', 'anggaran.team'])->findOrFail($id);
        
        // Cari semua kegiatan untuk mitra ini yang memiliki nomor SPK yang sama
        $penugasans = Penugasan::with(['mitra', 'anggaran.team'])
            ->where('mitra_id', $primary->mitra_id)
            ->where('no_spk', $primary->no_spk)
            ->whereNotNull('no_spk')
            ->get();

        // Jika nomor SPK kosong, tampilkan yang ini saja
        if ($penugasans->isEmpty()) {
            $penugasans = collect([$primary]);
        }

        // Hitung total honor dari semua kegiatan dalam SPK ini
        $totalHonor = $penugasans->sum('total_honor_tugas');
        $totalHonorTerbilang = $this->terbilang($totalHonor) . " Rupiah";
        $currentYear = 2026; 

        // Ambil Katim dari tim yang bersangkutan (Pihak Pertama)
        $teamId = $primary->team_id ?? ($primary->anggaran->team_id ?? null);
        $katim = \App\Models\User::where('team_id', $teamId)
            ->where('role', 'Katim')
            ->first() ?? \App\Models\User::where('role', 'Kepala')->first() 
                   ?? \App\Models\User::where('role', 'Admin')->first();

        return view('penugasan-mitra.spk', [
            'penugasan' => $primary, // Untuk header data mitra
            'penugasans' => $penugasans, // List kegiatan
            'katim' => $katim,
            'totalHonor' => $totalHonor,
            'totalHonorTerbilang' => $totalHonorTerbilang,
            'currentYear' => $currentYear
        ]);
    }

    /**
     * Generate BAST (Support multiple activities grouped by no_bast)
     */
    public function generateBAST($id)
    {
        $primary = Penugasan::with(['mitra', 'anggaran.team'])->findOrFail($id);
        
        // Cari semua kegiatan untuk mitra ini yang memiliki nomor BAST yang sama
        $penugasans = Penugasan::with(['mitra', 'anggaran.team'])
            ->where('mitra_id', $primary->mitra_id)
            ->where('no_bast', $primary->no_bast)
            ->whereNotNull('no_bast')
            ->get();

        if ($penugasans->isEmpty()) {
            $penugasans = collect([$primary]);
        }

        $totalHonor = $penugasans->sum('total_honor_tugas');
        $totalHonorTerbilang = $this->terbilang($totalHonor) . " Rupiah";
        $currentYear = 2026;

        $teamId = $primary->team_id ?? ($primary->anggaran->team_id ?? null);
        $katim = \App\Models\User::where('team_id', $teamId)
            ->where('role', 'Katim')
            ->first() ?? \App\Models\User::where('role', 'Kepala')->first() 
                   ?? \App\Models\User::where('role', 'Admin')->first();

        return view('penugasan-mitra.bast', [
            'penugasan' => $primary,
            'penugasans' => $penugasans,
            'katim' => $katim,
            'totalHonor' => $totalHonor,
            'totalHonorTerbilang' => $totalHonorTerbilang,
            'currentYear' => $currentYear
        ]);
    }

    /**
     * Helper to convert number to Indonesian words
     */
    private function terbilang($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang($nilai/10)." Puluh". $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang($nilai/100) . " Ratus" . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang($nilai/1000) . " Ribu" . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang($nilai/1000000) . " Juta" . $this->terbilang($nilai % 1000000);
        }
        return trim($temp);
    }
}
