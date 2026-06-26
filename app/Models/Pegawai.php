<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     */
    protected $table = 'pegawai';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'pangkat',
        'level',
        'password',
        'aktif',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Get the sertifikat records for the pegawai.
     */
    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    /**
     * Get the rekap records for the pegawai.
     */
    public function rekap(): HasMany
    {
        return $this->hasMany(Rekap::class);
    }

    /**
     * Check if the pegawai has admin-level access (non-Staff).
     */
    public function isAdmin(): bool
    {
        return $this->level !== 'Staff';
    }

    public function isKepala(): bool
    {
        return $this->level === 'Kepala Kantor';
    }

    public function isKasubbag(): bool
    {
        return $this->level === 'Ka. Subbag Adum';
    }

    public function isStaff(): bool
    {
        return $this->level === 'Staff';
    }

    public function hasRole(...$roles): bool
    {
        return in_array($this->level, $roles);
    }

    public function notifikasi()
    {
        return $this->hasMany(\App\Models\Notifikasi::class);
    }

    public function unreadNotifikasi()
    {
        return $this->notifikasi()->where('dibaca', false);
    }

    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class);
    }
}
