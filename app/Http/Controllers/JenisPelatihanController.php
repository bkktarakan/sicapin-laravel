<?php

namespace App\Http\Controllers;

use App\Models\JenisPelatihan;
use App\Models\Sertifikat;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class JenisPelatihanController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $tahun = session('tahun', date('Y'));
        $jenisList = JenisPelatihan::where('tahun', $tahun)->orderBy('id')->get();

        $usageCounts = Sertifikat::where('tahun', $tahun)
            ->selectRaw('jenis_pelatihan, COUNT(*) as total')
            ->groupBy('jenis_pelatihan')
            ->pluck('total', 'jenis_pelatihan');

        $availableYears = JenisPelatihan::distinct()->pluck('tahun')->sort()->values();

        return view('jenis-pelatihan.index', compact('jenisList', 'usageCounts', 'tahun', 'availableYears'));
    }

    public function store(Request $request)
    {
        $tahun = session('tahun', date('Y'));

        $request->validate([
            'nama' => 'required|string|max:255',
        ], [
            'nama.required' => 'Nama jenis pelatihan wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
        ]);

        $exists = JenisPelatihan::where('nama', $request->nama)->where('tahun', $tahun)->exists();
        if ($exists) {
            return back()->with('error', 'Jenis pelatihan "' . $request->nama . '" sudah ada di tahun ' . $tahun . '.');
        }

        JenisPelatihan::create(['nama' => $request->nama, 'tahun' => $tahun, 'aktif' => true]);

        $this->logActivity('create', 'Tambah jenis pelatihan: ' . $request->nama . ' (tahun ' . $tahun . ')');

        return back()->with('success', 'Jenis pelatihan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisPelatihan::findOrFail($id);
        $tahun = $jenis->tahun;

        $request->validate([
            'nama' => 'required|string|max:255',
        ], [
            'nama.required' => 'Nama jenis pelatihan wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
        ]);

        $exists = JenisPelatihan::where('nama', $request->nama)->where('tahun', $tahun)->where('id', '!=', $id)->exists();
        if ($exists) {
            return back()->with('error', 'Jenis pelatihan "' . $request->nama . '" sudah ada di tahun ' . $tahun . '.');
        }

        $oldNama = $jenis->nama;
        $jenis->update(['nama' => $request->nama]);

        if ($oldNama !== $request->nama) {
            Sertifikat::where('jenis_pelatihan', $oldNama)
                ->where('tahun', $tahun)
                ->update(['jenis_pelatihan' => $request->nama]);
        }

        $this->logActivity('update', 'Ubah jenis pelatihan: ' . $oldNama . ' → ' . $request->nama . ' (tahun ' . $tahun . ')');

        return back()->with('success', 'Jenis pelatihan berhasil diperbarui.');
    }

    public function toggleAktif($id)
    {
        $jenis = JenisPelatihan::findOrFail($id);
        $jenis->update(['aktif' => !$jenis->aktif]);

        $status = $jenis->aktif ? 'diaktifkan' : 'dinonaktifkan';
        $this->logActivity('update', 'Jenis pelatihan ' . $status . ': ' . $jenis->nama . ' (tahun ' . $jenis->tahun . ')');

        return back()->with('success', 'Jenis pelatihan berhasil ' . $status . '.');
    }

    public function destroy($id)
    {
        $jenis = JenisPelatihan::findOrFail($id);

        $usageCount = Sertifikat::where('jenis_pelatihan', $jenis->nama)->where('tahun', $jenis->tahun)->count();
        if ($usageCount > 0) {
            return back()->with('error', 'Tidak dapat menghapus karena digunakan oleh ' . $usageCount . ' sertifikat. Nonaktifkan saja.');
        }

        $this->logActivity('delete', 'Hapus jenis pelatihan: ' . $jenis->nama . ' (tahun ' . $jenis->tahun . ')');
        $jenis->delete();

        return back()->with('success', 'Jenis pelatihan berhasil dihapus.');
    }

    public function copyFromYear(Request $request)
    {
        $request->validate(['dari_tahun' => 'required|string']);

        $tahun = session('tahun', date('Y'));
        $dariTahun = $request->dari_tahun;

        if ($dariTahun === $tahun) {
            return back()->with('error', 'Tidak dapat menyalin dari tahun yang sama.');
        }

        $source = JenisPelatihan::where('tahun', $dariTahun)->get();
        if ($source->isEmpty()) {
            return back()->with('error', 'Tidak ada data jenis pelatihan di tahun ' . $dariTahun . '.');
        }

        $existingNames = JenisPelatihan::where('tahun', $tahun)->pluck('nama')->toArray();
        $copied = 0;

        foreach ($source as $item) {
            if (!in_array($item->nama, $existingNames)) {
                JenisPelatihan::create([
                    'nama' => $item->nama,
                    'tahun' => $tahun,
                    'aktif' => $item->aktif,
                ]);
                $copied++;
            }
        }

        if ($copied === 0) {
            return back()->with('info', 'Semua jenis pelatihan dari tahun ' . $dariTahun . ' sudah ada di tahun ' . $tahun . '.');
        }

        $this->logActivity('create', 'Salin ' . $copied . ' jenis pelatihan dari tahun ' . $dariTahun . ' ke ' . $tahun);

        return back()->with('success', $copied . ' jenis pelatihan berhasil disalin dari tahun ' . $dariTahun . '.');
    }
}
