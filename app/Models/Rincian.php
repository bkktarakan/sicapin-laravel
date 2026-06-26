<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rincian extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'rincian';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sertifikat_id',
        'pegawai_id',
        'tahun',
        'j1', 'j2', 'j3', 'j4', 'j5',
        'j6', 'j7', 'j8', 'j9', 'j10',
        'j11', 'j12', 'j13', 'j14', 'j15',
        'j16', 'j17', 'j18', 'j19', 'j20',
        'j21', 'j22', 'j23', 'j24', 'j25',
    ];

    /**
     * Get the sertifikat that owns the rincian.
     */
    public function sertifikat(): BelongsTo
    {
        return $this->belongsTo(Sertifikat::class);
    }

    /**
     * Get the pegawai that owns the rincian.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }
}
