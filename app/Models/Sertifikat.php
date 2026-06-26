<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sertifikat extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'sertifikat';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pegawai_id',
        'nama_pelatihan',
        'penyelenggara',
        'tanggal',
        'tanggal_akhir',
        'jpl',
        'jenis_pelatihan',
        'keterangan',
        'pdf',
        'tahun',
        'status',
        'catatan_verifikasi',
        'verified_by',
        'verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tanggal_akhir' => 'date',
            'jpl' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the pegawai that owns the sertifikat.
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'verified_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
