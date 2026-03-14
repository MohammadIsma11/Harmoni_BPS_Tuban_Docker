<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['nama_tim'];

    /**
     * Relasi ke Agenda (Tugas yang dibuat oleh anggota tim ini)
     */
    public function agendas()
    {
        // Menghubungkan Team -> User (Creator) -> Agenda
        // Jika di tabel agendas ada kolom user_id yang merujuk ke tabel users, 
        // dan di tabel users ada team_id yang merujuk ke tabel teams.
        return $this->hasManyThrough(
            Agenda::class, 
            User::class, 
            'team_id', // Foreign key di tabel users
            'user_id', // Foreign key di tabel agendas
            'id',      // Local key di tabel teams
            'id'       // Local key di tabel users
        );
    }

    /**
     * Relasi ke User (Anggota Tim)
     */
    public function members()
    {
        return $this->hasMany(User::class, 'team_id');
    }
}