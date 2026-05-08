<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    protected $table = 't_penugasan';

    protected $fillable = [
        'anggaran_id',
        'nama_kegiatan_manual',
        'kategori_kegiatan',
        'mitra_id',
        'user_id',
        'team_id',
        'tgl_mulai',
        'tgl_selesai_target',
        'satuan',
        'volume',
        'harga_satuan',
        'total_honor_tugas',
        'no_spk',
        'no_bast',
        'no_spp',
        'status_tugas',
        'status_dokumen',
        'file_pendukung',
        'lokasi_tugas',
    ];

    public function anggaran()
    {
        return $this->belongsTo(Anggaran::class, 'anggaran_id');
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id', 'sobat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'penugasan_id');
    }

    // Accessor for Activity Name (Dynamic between Manual or Budget)
    public function getNamaKegiatanFullAttribute()
    {
        if ($this->nama_kegiatan_manual) {
            return $this->nama_kegiatan_manual;
        }
        return $this->anggaran ? $this->anggaran->nama_pagu : 'N/A';
    }
}
