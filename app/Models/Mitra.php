<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    protected $table = 'm_mitra';
    protected $primaryKey = 'sobat_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sobat_id',
        'nama_lengkap',
        'posisi',
        'status_seleksi',
        'posisi_daftar',
        'email',
        'no_telp',
        'jenis_kelamin',
        'alamat_detail',
        'alamat_prov',
        'alamat_kab',
        'alamat_kec',
        'alamat_desa',
        'tempat_lahir',
        'tgl_lahir',
        'umur',
        'pendidikan',
        'pekerjaan',
        'deskripsi_pekerjaan_lain',
        'max_honor_bulanan',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'max_honor_bulanan' => 'float',
    ];

    public function penugasans()
    {
        return $this->hasMany(Penugasan::class, 'mitra_id', 'sobat_id');
    }
}
