<?php

namespace App\Imports;

use App\Models\Mitra;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MitraImport implements ToModel, WithStartRow
{
    private $hashedPassword;

    public function __construct()
    {
        // Pre-hash password cukup sekali di awal untuk menghemat CPU & waktu
        $this->hashedPassword = Hash::make('sobat123');
    }

    /**
     * Start from row 3
     */
    public function startRow(): int
    {
        return 3;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Mapping Kolom (A=0, B=1, ..., R=17)
        $nama_lengkap       = isset($row[1]) ? trim((string)$row[1]) : null;
        $posisi_raw         = isset($row[2]) ? trim((string)$row[2]) : null;
        $status_seleksi     = isset($row[3]) ? trim((string)$row[3]) : null;
        $posisi_daftar      = isset($row[4]) ? trim((string)$row[4]) : null;
        $alamat_detail      = isset($row[5]) ? trim((string)$row[5]) : null;
        $alamat_prov        = isset($row[6]) ? trim((string)$row[6]) : null; // G
        $alamat_kab         = isset($row[7]) ? trim((string)$row[7]) : null; // H
        $alamat_kec         = isset($row[8]) ? trim((string)$row[8]) : null; // I
        $alamat_desa        = isset($row[9]) ? trim((string)$row[9]) : null; // J
        $birth_info_raw     = isset($row[10]) ? trim((string)$row[10]) : null; // K
        $jk_raw             = isset($row[11]) ? trim((string)$row[11]) : null; // L
        $pendidikan         = isset($row[12]) ? trim((string)$row[12]) : null; // M
        $pekerjaan          = isset($row[13]) ? trim((string)$row[13]) : null; // N
        $pekerjaan_lain     = isset($row[14]) ? trim((string)$row[14]) : null; // O
        $no_telp            = isset($row[15]) ? trim((string)$row[15]) : null; // P
        $sobat_id           = isset($row[16]) ? trim((string)$row[16]) : null; // Q
        $email              = isset($row[17]) ? trim((string)$row[17]) : null; // R

        // FILTER: Hanya status 'Diterima' yang masuk database
        if (strtolower($status_seleksi) !== 'diterima') {
            return null;
        }

        // Penanganan Format Sobat ID (Scientific Notation)
        if (!empty($sobat_id) && (strpos(strtoupper($sobat_id), 'E') !== false || is_numeric($sobat_id))) {
             $sobat_id = number_format((float)$sobat_id, 0, '.', '');
        }

        // Validasi minimal
        if (empty($sobat_id) || empty($nama_lengkap) || $sobat_id == "0") {
            return null;
        }

        // 1. Robust Position Mapping (Keyword Based)
        $posisi_array = [];
        if (!empty($posisi_raw)) {
            if (str_contains(strtolower($posisi_raw), 'pendataan'))   $posisi_array[] = 'Mitra Pendataan';
            if (str_contains(strtolower($posisi_raw), 'pengawasan'))  $posisi_array[] = 'Mitra Pengawasan';
            if (str_contains(strtolower($posisi_raw), 'pengolahan'))  $posisi_array[] = 'Mitra Pengolahan';
        }
        $posisi = !empty($posisi_array) ? implode(', ', $posisi_array) : $posisi_raw;

        // 2. Robust Birth Info Parsing (SURABAYA, 07-01-1976 (49))
        $tempat_lahir = null;
        $tgl_lahir = null;
        $umur = null;
        
        if (!empty($birth_info_raw)) {
            // Split by comma for Place
            if (str_contains($birth_info_raw, ',')) {
                $parts = explode(',', $birth_info_raw);
                $tempat_lahir = trim($parts[0] ?? '');
                
                if (isset($parts[1])) {
                    $rem = trim($parts[1]);
                    
                    // Extract Age inside brackets
                    if (preg_match('/\((\d+)\)/', $rem, $age_matches)) {
                        $umur = (int)$age_matches[1];
                        $date_part = trim(preg_replace('/\((\d+)\)/', '', $rem));
                    } else {
                        $date_part = $rem;
                    }

                    // Handle Date separators
                    $date_part = str_replace(['/', '.'], '-', $date_part);
                    
                    // Map Indonesian Months (if any)
                    $months = [
                        'januari' => 'January', 'februari' => 'February', 'maret' => 'March',
                        'april' => 'April', 'mei' => 'May', 'juni' => 'June',
                        'juli' => 'July', 'agustus' => 'August', 'september' => 'September',
                        'oktober' => 'October', 'november' => 'November', 'desember' => 'December'
                    ];
                    $date_en = str_ireplace(array_keys($months), array_values($months), $date_part);

                    try {
                        $tgl_lahir = \Carbon\Carbon::parse($date_en)->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                             $tgl_lahir = \Carbon\Carbon::createFromFormat('d-m-Y', $date_en)->format('Y-m-d');
                        } catch (\Exception $e2) {}
                    }
                }
            } else {
                $tempat_lahir = $birth_info_raw;
            }
        }

        // 3. Mapping Jenis Kelamin
        $mappedJK = 'L';
        if (!empty($jk_raw)) {
            $firstChar = strtoupper(substr(trim($jk_raw), 0, 1));
            $mappedJK = ($firstChar === 'P') ? 'P' : 'L';
        }

        try {
            // Create/Update Mitra Record
            $mitra = Mitra::updateOrCreate(
                ['sobat_id' => $sobat_id],
                [
                    'nama_lengkap'             => $nama_lengkap,
                    'posisi'                   => $posisi,
                    'status_seleksi'           => $status_seleksi,
                    'posisi_daftar'            => $posisi_daftar,
                    'email'                    => $email,
                    'no_telp'                  => $no_telp,
                    'jenis_kelamin'            => $mappedJK,
                    'alamat_detail'            => $alamat_detail,
                    'alamat_prov'              => $alamat_prov,
                    'alamat_kab'               => $alamat_kab,
                    'alamat_kec'               => $alamat_kec,
                    'alamat_desa'              => $alamat_desa,
                    'tempat_lahir'             => $tempat_lahir,
                    'tgl_lahir'                => $tgl_lahir,
                    'umur'                     => $umur,
                    'pendidikan'               => $pendidikan,
                    'pekerjaan'                => $pekerjaan,
                    'deskripsi_pekerjaan_lain' => $pekerjaan_lain,
                    'max_honor_bulanan'        => 3200000,
                ]
            );

            // Create/Update User Account for Mitra
            $user = User::where('username', $sobat_id)->first();
            if (!$user) {
                User::create([
                    'username'     => $sobat_id,
                    'nama_lengkap' => $nama_lengkap,
                    'email'        => $email,
                    'password'     => $this->hashedPassword,
                    'role'         => 'Mitra',
                ]);
            } else {
                $user->update([
                    'nama_lengkap' => $nama_lengkap,
                    'email'        => $email,
                    'role'         => 'Mitra',
                ]);
            }

            return $mitra;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error importing row ID ' . $sobat_id . ': ' . $e->getMessage());
            return null;
        }
    }
}
