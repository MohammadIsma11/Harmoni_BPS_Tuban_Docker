<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'nip',
        'username',
        'password',
        'role',
        'has_super_access',
        'signature',
        'team_id',
        'email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke Tim
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Relasi ke Absensi / Data Cuti
     * Ini penting untuk mengecek apakah user sedang cuti atau tidak
     */
    public function absences()
    {
        return $this->hasMany(Absensi::class, 'user_id');
    }

    /**
     * Agenda yang Diterima (oleh Pegawai)
     * Alias 'agendas' sering dipakai untuk timeline
     */
    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'assigned_to');
    }

    /**
     * Agenda yang Dibuat (oleh Admin/Katim)
     */
    public function createdAgendas()
    {
        return $this->hasMany(Agenda::class, 'user_id');
    }

    /**
     * Multi-Role Switcher Logic
     */
    public function isPejabatAsli()
    {
        $daftarPejabat = [
            'kepala.bps', 'ketua.tim', 'dodik.hendarto', 'respati.yekti', 
            'umdatul.ummah', 'ika.rahmawati', 'arif.suroso', 'triana.puji', 
            'yudhi.prasetyono', 'wicaksono'
        ];
        return in_array($this->username, $daftarPejabat);
    }

    public function getAvailableRoles()
    {
        $roles = [];

        if ($this->role === 'Admin') {
            return [['value' => 'Admin', 'label' => 'Administrator', 'icon' => 'fa-user-shield']];
        }

        if ($this->isPejabatAsli()) {
            $originalRole = ($this->team_id == 8) ? 'Kepala' : 'Katim';
            $roles[] = [
                'value' => $originalRole, 
                'label' => $originalRole,
                'icon'  => ($originalRole == 'Kepala') ? 'fa-user-tie' : 'fa-users-cog'
            ];
            $roles[] = [
                'value' => 'Pegawai', 
                'label' => 'Pegawai', 
                'icon' => 'fa-user'
            ];
        } else {
            $roles[] = [
                'value' => 'Pegawai', 
                'label' => 'Pegawai', 
                'icon' => 'fa-user'
            ];
        }

        return $roles;
    }

}