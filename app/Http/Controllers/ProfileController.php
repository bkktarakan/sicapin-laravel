<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use App\Models\Rekap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'nama' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
        ]);

        $user->update([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();
        $years = Sertifikat::where('pegawai_id', $user->id)
            ->selectRaw('DISTINCT tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $selectedYear = $request->get('tahun', session('tahun', date('Y')));

        $sertifikat = Sertifikat::where('pegawai_id', $user->id)
            ->where('tahun', $selectedYear)
            ->orderBy('tanggal', 'desc')
            ->get();

        $rekapPerTahun = Rekap::where('pegawai_id', $user->id)
            ->orderBy('tahun', 'desc')
            ->get();

        return view('profile.riwayat', compact('user', 'years', 'selectedYear', 'sertifikat', 'rekapPerTahun'));
    }
}
