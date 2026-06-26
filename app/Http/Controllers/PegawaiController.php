<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Rekap;
use App\Models\Rincian;
use App\Models\Sertifikat;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class PegawaiController extends Controller
{
    use LogsActivity;
    /**
     * Tampilkan daftar pegawai.
     */
    public function index()
    {
        $pegawai = Pegawai::orderBy('nama', 'asc')->get();

        return view('pegawai.index', compact('pegawai'));
    }

    /**
     * Simpan pegawai baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:30|unique:pegawai,nip',
            'nama' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'pangkat' => 'required|string|max:100',
            'level' => 'required|in:Admin,Kepala Kantor,Ka. Subbag Adum,Staff',
            'password' => 'required|string|min:8',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nip.max' => 'NIP maksimal 30 karakter.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'pangkat.required' => 'Pangkat wajib diisi.',
            'level.required' => 'Level wajib diisi.',
            'level.in' => 'Level tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $pegawai = Pegawai::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'level' => $request->level,
            'password' => $request->password,
        ]);

        // Buat rekap untuk tahun saat ini
        $tahun = session('tahun', date('Y'));
        Rekap::create([
            'pegawai_id' => $pegawai->id,
            'tahun' => $tahun,
            'jumlah_jpl' => 0,
            'keterangan' => 'Belum Terpenuhi',
        ]);

        $this->logActivity('create', 'Tambah pegawai: ' . $pegawai->nama, $pegawai);

        return redirect()->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    /**
     * Update data pegawai.
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nip' => 'required|string|max:30|unique:pegawai,nip,' . $id,
            'nama' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'pangkat' => 'required|string|max:100',
            'level' => 'required|in:Admin,Kepala Kantor,Ka. Subbag Adum,Staff',
            'password' => 'nullable|string|min:8',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nip.max' => 'NIP maksimal 30 karakter.',
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'pangkat.required' => 'Pangkat wajib diisi.',
            'level.required' => 'Level wajib diisi.',
            'level.in' => 'Level tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $data = [
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'pangkat' => $request->pangkat,
            'level' => $request->level,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $pegawai->update($data);

        $this->logActivity('update', 'Update pegawai: ' . $pegawai->nama, $pegawai);

        return redirect()->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $newPassword = $pegawai->nip;
        $pegawai->update(['password' => $newPassword]);

        $this->logActivity('update', 'Reset password pegawai: ' . $pegawai->nama, $pegawai);

        return back()->with('success', 'Password ' . $pegawai->nama . ' berhasil direset ke NIP (' . $newPassword . ').');
    }

    public function toggleAktif($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if ($pegawai->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $pegawai->update(['aktif' => !$pegawai->aktif]);

        $status = $pegawai->aktif ? 'diaktifkan' : 'dinonaktifkan';
        $this->logActivity('update', "Pegawai {$status}: {$pegawai->nama}", $pegawai);

        return back()->with('success', "Pegawai {$pegawai->nama} berhasil {$status}.");
    }

    /**
     * Hapus pegawai beserta data terkait.
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $this->logActivity('delete', 'Nonaktifkan pegawai: ' . $pegawai->nama);

        $pegawai->delete();

        return redirect()->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }
}
