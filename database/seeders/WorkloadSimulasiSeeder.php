<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agenda;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkloadSimulasiSeeder extends Seeder
{
    public function run()
    {
        echo "\nMembersihkan data tahun 2026 dan membuat simulasi setahun...\n";
        
        // Hapus data 2026 agar tidak tumpang tindih
        Agenda::whereYear('event_date', 2026)->delete();

        $pegawai = User::whereNotIn('role', ['Admin', 'Kepala'])->get();
        $teams = Team::all();
        $types = [1, 2, 3]; // 1: Lapangan, 2: Rapat, 3: Dinas Luar

        $currentMonth = 5; // Mei 2026 sesuai sistem
        $year = 2026;

        foreach ($pegawai as $index => $p) {
            // --- BAGIAN 1: DATA BASELINE (JANUARI - DESEMBER) ---
            for ($m = 1; $m <= 12; $m++) {
                // Jangan isi terlalu banyak di bulan lain, cukup 2-3 agenda agar kalender terisi
                $numAgendas = rand(2, 4);
                
                // Jika bulan ini adalah bulan berjalan, kita gunakan skenario khusus di Bagian 2
                if ($m == $currentMonth) continue;

                for ($i = 0; $i < $numAgendas; $i++) {
                    $day = rand(1, 28);
                    $date = Carbon::create($year, $m, $day);
                    
                    Agenda::create([
                        'title' => 'Kegiatan Rutin ' . ($m < 10 ? '0'.$m : $m) . ' - ' . ($i+1),
                        'activity_type_id' => $types[array_rand($types)],
                        'team_id' => $p->team_id ?? $teams->random()->id,
                        'assigned_to' => $p->id,
                        'event_date' => $date->toDateString(),
                        'end_date' => $date->toDateString(),
                        'status_laporan' => 'Selesai',
                        'location' => 'Kantor BPS Tuban',
                        'mode_surat' => 'upload'
                    ]);
                }
            }

            // --- BAGIAN 2: SKENARIO KHUSUS BULAN INI (UNTUK ANALISIS AI) ---
            // Kita bagi pegawai menjadi 3 kelompok beban
            if ($index < 3) {
                // KELOMPOK BEBAN TINGGI (15+ Kegiatan)
                $count = rand(16, 20);
                $label = "TINGGI";
            } elseif ($index < 8) {
                // KELOMPOK BEBAN IDEAL (8-15 Kegiatan)
                $count = rand(9, 14);
                $label = "IDEAL";
            } else {
                // KELOMPOK BEBAN RENDAH (0-7 Kegiatan)
                $count = rand(2, 5);
                $label = "RENDAH";
            }

            for ($i = 0; $i < $count; $i++) {
                $day = rand(1, 28);
                $date = Carbon::create($year, $currentMonth, $day);
                $type = $types[array_rand($types)];
                
                // Pegawai beban tinggi kita kasih lebih banyak tugas lapangan (Poin 5)
                if ($label == "TINGGI") {
                    $type = (rand(1, 10) > 4) ? 1 : $type; // 60% peluang Lapangan
                }

                Agenda::create([
                    'title' => "Tugas Utama - " . $label . " (" . ($i+1) . ")",
                    'activity_type_id' => $type,
                    'team_id' => $p->team_id ?? $teams->random()->id,
                    'assigned_to' => $p->id,
                    'event_date' => $date->toDateString(),
                    'end_date' => $date->toDateString(),
                    // Sisakan beberapa yang Pending untuk simulasi variabel ke-3
                    'status_laporan' => ($i % 4 == 0) ? 'Pending' : 'Selesai',
                    'location' => 'Lokasi Penugasan Mei',
                    'mode_surat' => 'upload'
                ]);
            }
        }

        echo "Selesai! Data simulasi setahun telah dibuat.\n";
    }
}
