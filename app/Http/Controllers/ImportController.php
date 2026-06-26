<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Rekap;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    use LogsActivity;

    public function index()
    {
        return view('import.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'file.required' => 'File CSV wajib diupload.',
            'file.mimes' => 'File harus berformat CSV.',
            'file.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file.');
        }

        // Read header
        $header = fgetcsv($handle, 0, ',');
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        // Normalize header
        $header = array_map(function ($h) {
            return strtolower(trim(str_replace(["\xEF\xBB\xBF", '"'], '', $h)));
        }, $header);

        $requiredColumns = ['nip', 'nama'];
        foreach ($requiredColumns as $col) {
            if (!in_array($col, $header)) {
                fclose($handle);
                return back()->with('error', "Kolom '{$col}' tidak ditemukan di file CSV. Kolom yang tersedia: " . implode(', ', $header));
            }
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $row = 1;
        $tahun = session('tahun', date('Y'));

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $row++;

            if (count($data) < count($header)) {
                $errors[] = "Baris {$row}: kolom tidak lengkap, dilewati.";
                $skipped++;
                continue;
            }

            $rowData = array_combine($header, $data);
            $nip = trim($rowData['nip'] ?? '');
            $nama = trim($rowData['nama'] ?? '');

            if (empty($nip) || empty($nama)) {
                $errors[] = "Baris {$row}: NIP atau Nama kosong, dilewati.";
                $skipped++;
                continue;
            }

            if (Pegawai::where('nip', $nip)->exists()) {
                $errors[] = "Baris {$row}: NIP {$nip} sudah terdaftar, dilewati.";
                $skipped++;
                continue;
            }

            $pegawai = Pegawai::create([
                'nip' => $nip,
                'nama' => $nama,
                'jabatan' => trim($rowData['jabatan'] ?? '-'),
                'pangkat' => trim($rowData['pangkat'] ?? '-'),
                'level' => trim($rowData['level'] ?? 'Staff'),
                'password' => $nip,
            ]);

            Rekap::create([
                'pegawai_id' => $pegawai->id,
                'tahun' => $tahun,
                'jumlah_jpl' => 0,
                'keterangan' => 'Belum Terpenuhi',
            ]);

            $imported++;
        }

        fclose($handle);

        $this->logActivity('create', "Import {$imported} pegawai dari CSV");

        $message = "Berhasil mengimport {$imported} pegawai.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return back()
            ->with('success', $message)
            ->with('importErrors', $errors);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_pegawai.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['nip', 'nama', 'jabatan', 'pangkat', 'level']);
            fputcsv($file, ['198501012010011001', 'Contoh Nama Pegawai', 'Dokter', 'III/a', 'Staff']);
            fputcsv($file, ['197612252005021002', 'Contoh Pegawai Kedua', 'Perawat', 'II/d', 'Staff']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
