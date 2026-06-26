<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Backup database dan file sertifikat ke ZIP';

    public function handle()
    {
        $this->info('=== Backup SICAPIN ===');

        $backupDir = storage_path('app/backups');
        if (!File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_His');
        $zipName = "sicapin_backup_{$timestamp}.zip";
        $zipPath = "{$backupDir}/{$zipName}";

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->error('Gagal membuat file ZIP.');
            return Command::FAILURE;
        }

        // Backup database using mysqldump
        $this->info('Backup database...');
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $sqlFile = storage_path("app/temp/db_backup_{$timestamp}.sql");
        $tempDir = storage_path('app/temp');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $dumpCmd = "mysqldump -h{$dbHost} -u{$dbUser}";
        if (!empty($dbPass)) {
            $dumpCmd .= " -p{$dbPass}";
        }
        $dumpCmd .= " {$dbName} > \"{$sqlFile}\"";

        exec($dumpCmd, $output, $returnVar);

        if ($returnVar === 0 && file_exists($sqlFile)) {
            $zip->addFile($sqlFile, 'database.sql');
            $this->info('  -> Database berhasil di-backup');
        } else {
            $this->warn('  -> mysqldump gagal, skip backup database');
        }

        // Backup PDF files
        $this->info('Backup file PDF...');
        $pdfDir = storage_path('app/public/sertifikat');
        $fileCount = 0;
        if (File::isDirectory($pdfDir)) {
            $files = File::files($pdfDir);
            foreach ($files as $file) {
                $zip->addFile($file->getPathname(), 'sertifikat/' . $file->getFilename());
                $fileCount++;
            }
        }
        $this->info("  -> {$fileCount} file PDF berhasil di-backup");

        $zip->close();

        // Cleanup temp SQL
        if (file_exists($sqlFile)) {
            unlink($sqlFile);
        }

        $size = round(filesize($zipPath) / 1024 / 1024, 2);
        $this->newLine();
        $this->info("=== Backup Selesai ===");
        $this->info("File: {$zipPath}");
        $this->info("Ukuran: {$size} MB");

        // Keep only last 5 backups
        $backups = collect(File::files($backupDir))
            ->filter(fn($f) => str_ends_with($f->getFilename(), '.zip'))
            ->sortByDesc(fn($f) => $f->getMTime());

        foreach ($backups->slice(5) as $old) {
            unlink($old->getPathname());
            $this->info("Hapus backup lama: {$old->getFilename()}");
        }

        return Command::SUCCESS;
    }
}
