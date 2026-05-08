<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    protected $table = 'm_anggaran';

    protected $fillable = [
        'kode_anggaran',
        'nama_pagu',
        'tim_id',
        'budget_tahunan',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'tim_id');
    }

    public function penugasans()
    {
        return $this->hasMany(Penugasan::class, 'anggaran_id');
    }
}
