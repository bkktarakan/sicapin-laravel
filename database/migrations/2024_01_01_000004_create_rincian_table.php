<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rincian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertifikat_id')->constrained('sertifikat')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->string('tahun');
            $table->integer('j1')->default(0);
            $table->integer('j2')->default(0);
            $table->integer('j3')->default(0);
            $table->integer('j4')->default(0);
            $table->integer('j5')->default(0);
            $table->integer('j6')->default(0);
            $table->integer('j7')->default(0);
            $table->integer('j8')->default(0);
            $table->integer('j9')->default(0);
            $table->integer('j10')->default(0);
            $table->integer('j11')->default(0);
            $table->integer('j12')->default(0);
            $table->integer('j13')->default(0);
            $table->integer('j14')->default(0);
            $table->integer('j15')->default(0);
            $table->integer('j16')->default(0);
            $table->integer('j17')->default(0);
            $table->integer('j18')->default(0);
            $table->integer('j19')->default(0);
            $table->integer('j20')->default(0);
            $table->integer('j21')->default(0);
            $table->integer('j22')->default(0);
            $table->integer('j23')->default(0);
            $table->integer('j24')->default(0);
            $table->integer('j25')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian');
    }
};
