<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $migrations = \Illuminate\Support\Facades\DB::table('migrations')->get();
    echo "Migrations in DB:\n";
    foreach($migrations as $m) {
        echo $m->migration . "\n";
    }
} catch (\Exception $e) {
    echo "Gagal: " . $e->getMessage();
}
