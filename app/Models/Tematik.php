<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tematik extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'pic',
        'member',
        'kecamatan',
        'desa',
        'sls',
        'judul',
        'tanggal',
        'status',
        'lat',
        'lng'
    ];
}
