<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrateOldData extends Command
{
    protected $signature = 'migrate:old-data';
    protected $description = 'Migrate data from old SICAPIN tables (tbl_*) to new Laravel tables';

    public function handle()
    {
        $this->info('=== Migrasi Data SICAPIN ===');

        // Clear new tables first (in case of re-run)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('rincian')->truncate();
        DB::table('sertifikat')->truncate();
        DB::table('rekap')->truncate();
        DB::table('activity_logs')->truncate();
        DB::table('pegawai')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->info('Tabel baru dibersihkan untuk migrasi ulang.');

        // 1. Migrate Pegawai
        $this->info('Migrasi pegawai...');
        $oldPegawai = DB::table('tbl_pegawai')->get();
        $pegawaiMap = []; // old_id => new_id

        DB::table('pegawai')->where('nip', 'admin')->delete(); // remove seeder admin

        foreach ($oldPegawai as $p) {
            $newId = DB::table('pegawai')->insertGetId([
                'nip' => $p->nip,
                'nama' => $p->nama,
                'jabatan' => $p->jabatan,
                'pangkat' => $p->pangkat,
                'level' => $p->level,
                'password' => $p->password, // already hashed with bcrypt
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $pegawaiMap[$p->id_pegawai] = $newId;
        }
        $this->info("  -> {$oldPegawai->count()} pegawai berhasil dimigrasikan");

        // 2. Copy PDF files
        $this->info('Menyalin file PDF...');
        $oldPdfPath = base_path('../sicapin/public/pdf');
        $newPdfPath = storage_path('app/public/sertifikat');

        if (!File::isDirectory($newPdfPath)) {
            File::makeDirectory($newPdfPath, 0755, true);
        }

        $copiedFiles = 0;
        if (File::isDirectory($oldPdfPath)) {
            $files = File::files($oldPdfPath);
            foreach ($files as $file) {
                File::copy($file->getPathname(), $newPdfPath . '/' . $file->getFilename());
                $copiedFiles++;
            }
        }
        $this->info("  -> {$copiedFiles} file PDF berhasil disalin");

        // 3. Migrate Sertifikat
        $this->info('Migrasi sertifikat...');
        $oldSertifikat = DB::table('tbl_sertifikat')->get();
        $sertifikatMap = []; // old_id => new_id

        foreach ($oldSertifikat as $s) {
            if (!isset($pegawaiMap[$s->id_pegawai])) {
                continue;
            }

            $tanggal = $s->tanggal;
            if ($tanggal === '0000-00-00' || empty($tanggal)) {
                $tanggal = $s->tahun . '-01-01';
            }

            $tanggalAkhir = $s->tanggal_akhir;
            if ($tanggalAkhir === '0000-00-00' || empty($tanggalAkhir)) {
                $tanggalAkhir = $tanggal;
            }

            $newId = DB::table('sertifikat')->insertGetId([
                'pegawai_id' => $pegawaiMap[$s->id_pegawai],
                'nama_pelatihan' => $s->nama_pelatihan,
                'penyelenggara' => $s->penyelenggara ?: '-',
                'tanggal' => $tanggal,
                'tanggal_akhir' => $tanggalAkhir,
                'jpl' => $s->jpl,
                'jenis_pelatihan' => $s->jenis_pelatihan,
                'keterangan' => $s->keterangan ?: null,
                'pdf' => $s->pdf,
                'tahun' => $s->tahun,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $sertifikatMap[$s->id_sertifikat] = $newId;
        }
        $this->info("  -> {$oldSertifikat->count()} sertifikat berhasil dimigrasikan");

        // 4. Migrate Rekap
        $this->info('Migrasi rekap...');
        $oldRekap = DB::table('tbl_rekap')->get();
        $rekapCount = 0;

        foreach ($oldRekap as $r) {
            if (!isset($pegawaiMap[$r->id_pegawai])) {
                continue;
            }

            $exists = DB::table('rekap')
                ->where('pegawai_id', $pegawaiMap[$r->id_pegawai])
                ->where('tahun', $r->tahun)
                ->exists();

            if (!$exists) {
                DB::table('rekap')->insert([
                    'pegawai_id' => $pegawaiMap[$r->id_pegawai],
                    'tahun' => $r->tahun,
                    'jumlah_jpl' => $r->jumlah_jpl ?? 0,
                    'keterangan' => $r->keterangan ?: 'Belum Terpenuhi',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $rekapCount++;
            }
        }
        $this->info("  -> {$rekapCount} rekap berhasil dimigrasikan");

        // 5. Migrate Rincian
        $this->info('Migrasi rincian...');
        $oldRincian = DB::table('tbl_rincian')->get();
        $rincianCount = 0;

        foreach ($oldRincian as $r) {
            if (!isset($pegawaiMap[$r->id_pegawai]) || !isset($sertifikatMap[$r->id_sertifikat])) {
                continue;
            }

            $data = [
                'sertifikat_id' => $sertifikatMap[$r->id_sertifikat],
                'pegawai_id' => $pegawaiMap[$r->id_pegawai],
                'tahun' => $r->tahun,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            for ($i = 1; $i <= 25; $i++) {
                $col = 'j' . $i;
                $data[$col] = $r->$col ?? 0;
            }

            DB::table('rincian')->insert($data);
            $rincianCount++;
        }
        $this->info("  -> {$rincianCount} rincian berhasil dimigrasikan");

        $this->newLine();
        $this->info('=== Migrasi Selesai ===');
        $this->info("Pegawai: {$oldPegawai->count()}");
        $this->info("Sertifikat: {$oldSertifikat->count()}");
        $this->info("Rekap: {$rekapCount}");
        $this->info("Rincian: {$rincianCount}");
        $this->info("File PDF: {$copiedFiles}");

        return Command::SUCCESS;
    }
}
