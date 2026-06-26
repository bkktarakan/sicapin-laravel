<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['sertifikat_id', 'user_id', 'aksi', 'perubahan', 'created_at'];

    protected function casts(): array
    {
        return ['perubahan' => 'array', 'created_at' => 'datetime'];
    }

    public function sertifikat()
    {
        return $this->belongsTo(Sertifikat::class);
    }

    public function user()
    {
        return $this->belongsTo(Pegawai::class, 'user_id');
    }

    public static function catat($sertifikatId, $aksi, $perubahan = null)
    {
        return static::create([
            'sertifikat_id' => $sertifikatId,
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'perubahan' => $perubahan,
            'created_at' => now(),
        ]);
    }
}
