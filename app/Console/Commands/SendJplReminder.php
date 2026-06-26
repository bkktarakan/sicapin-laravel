<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Rekap;
use Illuminate\Console\Command;

class SendJplReminder extends Command
{
    protected $signature = 'jpl:remind';
    protected $description = 'Kirim notifikasi pengingat ke pegawai yang JPL-nya di bawah target';

    public function handle()
    {
        $tahun = date('Y');
        $bulan = (int) date('m');

        if (!in_array($bulan, [3, 6, 9, 11])) {
            $this->info('Bukan bulan pengingat (Maret, Juni, September, November). Dilewati.');
            return;
        }

        $labels = [3 => 'Triwulan I', 6 => 'Semester I', 9 => 'Triwulan III', 11 => 'menjelang akhir tahun'];
        $label = $labels[$bulan];

        $belumTerpenuhi = Rekap::with('pegawai')
            ->where('tahun', $tahun)
            ->where('keterangan', 'Belum Terpenuhi')
            ->get();

        $count = 0;
        foreach ($belumTerpenuhi as $rekap) {
            $sisa = max(0, 20 - $rekap->jumlah_jpl);
            Notifikasi::create([
                'pegawai_id' => $rekap->pegawai_id,
                'judul' => 'Pengingat Target JPL',
                'pesan' => "Capaian JPL Anda saat {$label}: {$rekap->jumlah_jpl}/20 JPL. Masih kurang {$sisa} JPL untuk memenuhi target tahun {$tahun}.",
                'tipe' => 'warning',
            ]);
            $count++;
        }

        $this->info("Terkirim {$count} notifikasi pengingat JPL.");
    }
}
