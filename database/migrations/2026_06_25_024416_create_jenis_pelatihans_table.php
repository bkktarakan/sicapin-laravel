<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        $types = [
            'Tugas Belajar', 'Ijin Belajar', 'Pelatihan Struktural/Diklat PIM',
            'Pelatihan Manajerial', 'Pelatihan Teknis', 'Pelatihan Fungsional',
            'Pelatihan Sosial Kultural', 'Seminar/Konferensi', 'Workshop/Lokakarya',
            'Kursus', 'Penataran', 'Bimbingan Teknis', 'Sosialisasi',
            'Coaching', 'Mentoring', 'E-Learning', 'Pelatihan Jarak Jauh',
            'Datasering', 'Pembelajaran Alam Terbuka (OutBond)',
            'Patok Banding (benchmarking)', 'Pertukaran antara PNS dengan Pegawai swasta',
            'Belajar Mandiri', 'Komunitas Belajar', 'Bimbingan ditempat Kerja',
            'Magang/Praktik Kerja',
        ];

        $now = now();
        foreach ($types as $nama) {
            DB::table('jenis_pelatihan')->insert([
                'nama' => $nama,
                'aktif' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pelatihan');
    }
};
