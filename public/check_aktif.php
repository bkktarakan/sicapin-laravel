<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $hasColumn = \Illuminate\Support\Facades\Schema::hasColumn('pegawai', 'aktif');
    echo "Kolom aktif " . ($hasColumn ? "ADA" : "TIDAK ADA") . " di tabel pegawai.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
