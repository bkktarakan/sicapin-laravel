<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_pelatihan', function (Blueprint $table) {
            $table->string('tahun')->after('nama')->default('');
        });

        $existing = DB::table('jenis_pelatihan')->get();
        $years = DB::table('rekap')->distinct()->pluck('tahun')->toArray();

        if (empty($years)) {
            $years = [date('Y')];
        }

        $now = now();
        foreach ($existing as $item) {
            foreach ($years as $tahun) {
                if ($tahun === '') continue;
                DB::table('jenis_pelatihan')->insert([
                    'nama' => $item->nama,
                    'tahun' => $tahun,
                    'aktif' => $item->aktif,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        DB::table('jenis_pelatihan')->where('tahun', '')->delete();

        Schema::table('jenis_pelatihan', function (Blueprint $table) {
            $table->unique(['nama', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::table('jenis_pelatihan', function (Blueprint $table) {
            $table->dropUnique(['nama', 'tahun']);
            $table->dropColumn('tahun');
        });
    }
};
