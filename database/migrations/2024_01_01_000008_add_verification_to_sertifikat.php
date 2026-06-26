<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('tahun'); // pending, approved, rejected
            $table->text('catatan_verifikasi')->nullable()->after('status');
            $table->foreignId('verified_by')->nullable()->after('catatan_verifikasi')->constrained('pegawai')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });

        // Set existing sertifikat as approved (data lama)
        DB::table('sertifikat')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['status', 'catatan_verifikasi', 'verified_by', 'verified_at']);
        });
    }
};
