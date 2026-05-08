<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 't_pembayaran';

    protected $fillable = [
        'penugasan_id',
        'nominal_cair',
        'bulan_bayar',
        'status_bayar',
        'keterangan',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id');
    }
}
