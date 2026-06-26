<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertifikat_id')->constrained('sertifikat')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->string('aksi');
            $table->json('perubahan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat_logs');
    }
};
