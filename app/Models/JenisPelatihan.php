<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class JenisPelatihan extends Model
{
    protected $table = 'jenis_pelatihan';

    protected $fillable = ['nama', 'tahun', 'aktif'];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn($m) => Cache::forget('jenis_pelatihan_' . $m->tahun));
        static::deleted(fn($m) => Cache::forget('jenis_pelatihan_' . $m->tahun));
    }

    public static function aktifByTahun($tahun)
    {
        return Cache::remember('jenis_pelatihan_' . $tahun, 3600, function () use ($tahun) {
            return static::where('tahun', $tahun)->where('aktif', true)->orderBy('nama')->pluck('nama')->toArray();
        });
    }

    public static function allByTahun($tahun)
    {
        return static::where('tahun', $tahun)->orderBy('id')->pluck('nama')->toArray();
    }
}
