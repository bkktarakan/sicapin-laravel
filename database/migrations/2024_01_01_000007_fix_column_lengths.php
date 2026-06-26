<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->text('nama_pelatihan')->change();
            $table->text('penyelenggara')->change();
            $table->text('keterangan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('nama_pelatihan')->change();
            $table->string('penyelenggara')->change();
            $table->text('keterangan')->nullable()->change();
        });
    }
};
