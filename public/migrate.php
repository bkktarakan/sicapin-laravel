<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Hapus cache Laravel
$cacheFiles = glob(__DIR__ . '/../bootstrap/cache/*.php');
foreach($cacheFiles as $file){
    if(basename($file) !== '.gitignore') {
        @unlink($file);
    }
}

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    try {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE pegawai ADD COLUMN aktif TINYINT(1) NOT NULL DEFAULT 1 AFTER level");
        echo "Kolom aktif berhasil ditambahkan ke tabel pegawai!<br>";
    } catch (\Throwable $e) {
        echo "Kolom aktif sudah ada atau gagal ditambahkan (Aman).<br>";
    }

    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "Migrasi Selesai: " . nl2br(\Illuminate\Support\Facades\Artisan::output());
} catch (\Throwable $e) {
    echo "Gagal migrasi: " . $e->getMessage();
}
