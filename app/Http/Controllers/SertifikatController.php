<?php

namespace App\Http\Controllers;

use App\Models\JenisPelatihan;
use App\Models\Notifikasi;
use App\Models\Pegawai;
use App\Models\Rekap;
use App\Models\Rincian;
use App\Models\Sertifikat;
use App\Models\SertifikatLog;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    use LogsActivity;

    private function getJenisPelatihan($tahun = null)
    {
        $tahun = $tahun ?? session('tahun', date('Y'));
        return JenisPelatihan::aktifByTahun($tahun);
    }

    private function getAllJenisPelatihan($tahun = null)
    {
        $tahun = $tahun ?? session('tahun', date('Y'));
        return JenisPelatihan::allByTahun($tahun);
    }

    /**
     * Tampilkan daftar sertifikat.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahun = session('tahun', date('Y'));
        $jenisPelatihan = $this->getJenisPelatihan();

        if ($user->level === 'Staff') {
            $query = Sertifikat::with('pegawai')
                ->where('pegawai_id', $user->id)
                ->where('tahun', $tahun);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pelatihan', 'like', "%{$search}%")
                      ->orWhere('penyelenggara', 'like', "%{$search}%");
                });
            }
            if ($request->filled('jenis')) {
                $query->where('jenis_pelatihan', $request->jenis);
            }
            if ($request->filled('dari')) {
                $query->where('tanggal', '>=', $request->dari);
            }
            if ($request->filled('sampai')) {
                $query->where('tanggal', '<=', $request->sampai);
            }

            $sertifikat = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

            return view('sertifikat.index-staff', compact('sertifikat', 'tahun', 'jenisPelatihan'));
        }

        // Admin / Kepala Kantor / Ka. Subbag Adum: grouped by pegawai
        $pegawaiQuery = Pegawai::aktif()->orderBy('nama');

        if ($request->filled('search')) {
            $search = $request->search;
            $pegawaiQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $pegawaiList = $pegawaiQuery->paginate(15)->withQueryString();

        $pegawaiIds = $pegawaiList->pluck('id');
        $sertifikatByPegawai = Sertifikat::whereIn('pegawai_id', $pegawaiIds)
            ->where('tahun', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->groupBy('pegawai_id');

        $rekapByPegawai = Rekap::whereIn('pegawai_id', $pegawaiIds)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('pegawai_id');

        return view('sertifikat.index', compact('pegawaiList', 'sertifikatByPegawai', 'rekapByPegawai', 'tahun', 'jenisPelatihan'));
    }

    public function liveSearch(Request $request)
    {
        $user = Auth::user();
        $tahun = session('tahun', date('Y'));
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $query = Sertifikat::with('pegawai')->where('tahun', $tahun);

        if ($user->isStaff()) {
            $query->where('pegawai_id', $user->id);
        }

        $query->where(function ($qb) use ($q) {
            $qb->where('nama_pelatihan', 'like', "%{$q}%")
               ->orWhere('penyelenggara', 'like', "%{$q}%")
               ->orWhere('jenis_pelatihan', 'like', "%{$q}%")
               ->orWhere('keterangan', 'like', "%{$q}%")
               ->orWhere('jpl', 'like', "%{$q}%")
               ->orWhere('tanggal', 'like', "%{$q}%")
               ->orWhere('status', 'like', "%{$q}%")
               ->orWhereHas('pegawai', function ($pq) use ($q) {
                   $pq->where('nama', 'like', "%{$q}%")
                      ->orWhere('nip', 'like', "%{$q}%")
                      ->orWhere('jabatan', 'like', "%{$q}%");
               });
        });

        $results = $query->orderBy('tanggal', 'desc')->limit(20)->get();

        return response()->json($results->map(function ($s) {
            return [
                'id' => $s->id,
                'nama_pelatihan' => $s->nama_pelatihan,
                'penyelenggara' => $s->penyelenggara,
                'jenis_pelatihan' => $s->jenis_pelatihan,
                'jpl' => $s->jpl,
                'tanggal' => $s->tanggal ? $s->tanggal->format('d/m/Y') : '-',
                'tanggal_akhir' => $s->tanggal_akhir ? $s->tanggal_akhir->format('d/m/Y') : null,
                'status' => $s->status,
                'keterangan' => $s->keterangan,
                'pegawai_nama' => $s->pegawai->nama ?? '-',
                'pegawai_nip' => $s->pegawai->nip ?? '-',
                'has_pdf' => (bool) $s->pdf,
                'preview_url' => $s->pdf ? route('sertifikat.preview', $s->id) : null,
                'pdf_url' => $s->pdf ? route('sertifikat.pdf', $s->id) : null,
            ];
        }));
    }

    /**
     * Tampilkan form upload sertifikat.
     */
    public function create()
    {
        $user = Auth::user();
        $jenisPelatihan = $this->getJenisPelatihan();

        if ($user->level === 'Staff') {
            $pegawai = Pegawai::where('id', $user->id)->get();
        } else {
            $pegawai = Pegawai::aktif()->orderBy('nama', 'asc')->get();
        }

        return view('sertifikat.create', compact('jenisPelatihan', 'pegawai'));
    }

    public function bulkCreate()
    {
        $jenisPelatihan = $this->getJenisPelatihan();
        $pegawai = Pegawai::aktif()->orderBy('nama', 'asc')->get();
        return view('sertifikat.bulk-create', compact('jenisPelatihan', 'pegawai'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.pegawai_id' => 'required|exists:pegawai,id',
            'items.*.nama_pelatihan' => 'required|string|max:255',
            'items.*.penyelenggara' => 'required|string|max:255',
            'items.*.tanggal' => 'required|date',
            'items.*.tanggal_akhir' => 'required|date',
            'items.*.jpl' => 'required|integer|min:1|max:10000',
            'items.*.jenis_pelatihan' => 'required|string',
        ]);

        $tahun = session('tahun', date('Y'));
        $count = 0;

        foreach ($request->items as $item) {
            $sertifikat = Sertifikat::create([
                'pegawai_id' => $item['pegawai_id'],
                'nama_pelatihan' => $item['nama_pelatihan'],
                'penyelenggara' => $item['penyelenggara'],
                'tanggal' => $item['tanggal'],
                'tanggal_akhir' => $item['tanggal_akhir'],
                'jpl' => $item['jpl'],
                'jenis_pelatihan' => $item['jenis_pelatihan'],
                'keterangan' => $item['keterangan'] ?? null,
                'pdf' => null,
                'tahun' => $tahun,
                'status' => 'approved',
            ]);

            $rincian = Rincian::create([
                'sertifikat_id' => $sertifikat->id,
                'pegawai_id' => $item['pegawai_id'],
                'tahun' => $tahun,
            ]);

            $jenisIndex = array_search($item['jenis_pelatihan'], $this->getAllJenisPelatihan());
            if ($jenisIndex !== false && $jenisIndex < 25) {
                $rincian->{'j' . ($jenisIndex + 1)} = $item['jpl'];
                $rincian->save();
            }

            $this->recalculateRekap($item['pegawai_id'], $tahun);
            $count++;
        }

        $this->logActivity('create', 'Bulk upload ' . $count . ' sertifikat');

        return redirect()->route('sertifikat.index')
            ->with('success', $count . ' sertifikat berhasil ditambahkan.');
    }

    /**
     * Simpan sertifikat baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nama_pelatihan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal',
            'jpl' => 'required|integer|min:1|max:10000',
            'jenis_pelatihan' => 'required|string',
            'keterangan' => 'nullable|string|max:500',
            'pdf' => 'required|file|mimetypes:application/pdf|max:2048|min:1',
        ], [
            'pegawai_id.required' => 'Pegawai wajib dipilih.',
            'pegawai_id.exists' => 'Pegawai tidak ditemukan.',
            'nama_pelatihan.required' => 'Nama pelatihan wajib diisi.',
            'nama_pelatihan.max' => 'Nama pelatihan maksimal 255 karakter.',
            'penyelenggara.required' => 'Penyelenggara wajib diisi.',
            'penyelenggara.max' => 'Penyelenggara maksimal 255 karakter.',
            'tanggal.required' => 'Tanggal mulai wajib diisi.',
            'tanggal.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.date' => 'Format tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
            'jpl.required' => 'JPL wajib diisi.',
            'jpl.integer' => 'JPL harus berupa angka.',
            'jpl.max' => 'JPL maksimal 10000.',
            'jpl.min' => 'JPL minimal 1.',
            'jenis_pelatihan.required' => 'Jenis pelatihan wajib dipilih.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'pdf.required' => 'File PDF sertifikat wajib diupload.',
            'pdf.mimes' => 'File harus berformat PDF.',
            'pdf.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        $tahun = session('tahun', date('Y'));

        // Duplicate detection
        $duplicate = Sertifikat::where('pegawai_id', $request->pegawai_id)
            ->where('nama_pelatihan', $request->nama_pelatihan)
            ->where('tanggal', $request->tanggal)
            ->where('tahun', $tahun)
            ->first();

        if ($duplicate) {
            return back()->withInput()->with('error',
                'Sertifikat "' . $request->nama_pelatihan . '" dengan tanggal yang sama sudah pernah diupload untuk pegawai ini.'
            );
        }

        // Upload file PDF
        $pdfFile = $request->file('pdf');
        $pdfName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $pdfFile->getClientOriginalName());
        $stored = $pdfFile->storeAs('sertifikat', $pdfName, 'public');

        if (!$stored) {
            return back()->withInput()->with('error', 'Gagal menyimpan file PDF. Silakan coba lagi.');
        }

        // Simpan sertifikat
        $sertifikat = Sertifikat::create([
            'pegawai_id' => $request->pegawai_id,
            'nama_pelatihan' => $request->nama_pelatihan,
            'penyelenggara' => $request->penyelenggara,
            'tanggal' => $request->tanggal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'jpl' => $request->jpl,
            'jenis_pelatihan' => $request->jenis_pelatihan,
            'keterangan' => $request->keterangan,
            'pdf' => $pdfName,
            'tahun' => $tahun,
            'status' => Auth::user()->isAdmin() ? 'approved' : 'pending',
        ]);

        // Buat/pastikan rekap ada untuk pegawai+tahun ini
        $this->recalculateRekap($request->pegawai_id, $tahun);

        // Buat rincian dan set kolom jenis yang sesuai
        $rincian = Rincian::create([
            'sertifikat_id' => $sertifikat->id,
            'pegawai_id' => $request->pegawai_id,
            'tahun' => $tahun,
        ]);

        $jenisIndex = array_search($request->jenis_pelatihan, $this->getAllJenisPelatihan());
        if ($jenisIndex !== false) {
            $column = 'j' . ($jenisIndex + 1);
            $rincian->$column = $request->jpl;
            $rincian->save();
        }

        $this->logActivity('create', 'Upload sertifikat: ' . $request->nama_pelatihan, $sertifikat);
        SertifikatLog::catat($sertifikat->id, 'Upload', ['data' => $sertifikat->only(['nama_pelatihan','penyelenggara','jpl','jenis_pelatihan','tanggal'])]);
        $this->checkAndNotify($request->pegawai_id, $tahun);

        if ($sertifikat->status === 'pending') {
            $pegawaiNama = Auth::user()->nama;
            $this->notifyAdmins('Sertifikat Baru Perlu Verifikasi', $pegawaiNama . ' mengupload sertifikat "' . $request->nama_pelatihan . '" dan menunggu verifikasi.', 'warning');
        }

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil diupload.');
    }

    /**
     * Update data sertifikat.
     */
    public function update(Request $request, $id)
    {
        $sertifikat = Sertifikat::findOrFail($id);

        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nama_pelatihan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal',
            'jpl' => 'required|integer|min:1|max:10000',
            'jenis_pelatihan' => 'required|string',
            'keterangan' => 'nullable|string|max:500',
            'pdf' => 'nullable|file|mimetypes:application/pdf|max:2048',
        ], [
            'pegawai_id.required' => 'Pegawai wajib dipilih.',
            'pegawai_id.exists' => 'Pegawai tidak ditemukan.',
            'nama_pelatihan.required' => 'Nama pelatihan wajib diisi.',
            'nama_pelatihan.max' => 'Nama pelatihan maksimal 255 karakter.',
            'penyelenggara.required' => 'Penyelenggara wajib diisi.',
            'penyelenggara.max' => 'Penyelenggara maksimal 255 karakter.',
            'tanggal.required' => 'Tanggal mulai wajib diisi.',
            'tanggal.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_akhir.required' => 'Tanggal akhir wajib diisi.',
            'tanggal_akhir.date' => 'Format tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
            'jpl.required' => 'JPL wajib diisi.',
            'jpl.integer' => 'JPL harus berupa angka.',
            'jpl.min' => 'JPL minimal 1.',
            'jenis_pelatihan.required' => 'Jenis pelatihan wajib dipilih.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
            'pdf.mimes' => 'File harus berformat PDF.',
            'pdf.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        $tahun = session('tahun', date('Y'));

        // Simpan jenis pelatihan lama untuk reset kolom rincian
        $oldJenisPelatihan = $sertifikat->jenis_pelatihan;
        $oldJpl = $sertifikat->jpl;

        $data = [
            'pegawai_id' => $request->pegawai_id,
            'nama_pelatihan' => $request->nama_pelatihan,
            'penyelenggara' => $request->penyelenggara,
            'tanggal' => $request->tanggal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'jpl' => $request->jpl,
            'jenis_pelatihan' => $request->jenis_pelatihan,
            'keterangan' => $request->keterangan,
        ];

        $user = Auth::user();
        if ($user->isStaff()) {
            $data['status'] = 'pending';
            $data['catatan_verifikasi'] = null;
            $data['verified_by'] = null;
            $data['verified_at'] = null;
        }

        // Upload file PDF baru jika ada
        if ($request->hasFile('pdf')) {
            // Hapus file PDF lama
            if ($sertifikat->pdf && Storage::disk('public')->exists('sertifikat/' . $sertifikat->pdf)) {
                Storage::disk('public')->delete('sertifikat/' . $sertifikat->pdf);
            }

            $pdfFile = $request->file('pdf');
            $pdfName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $pdfFile->getClientOriginalName());
            $stored = $pdfFile->storeAs('sertifikat', $pdfName, 'public');

            if (!$stored) {
                return back()->withInput()->with('error', 'Gagal menyimpan file PDF. Silakan coba lagi.');
            }

            $data['pdf'] = $pdfName;
        }

        $sertifikat->update($data);

        // Update rincian: reset kolom lama, set kolom baru
        $rincian = Rincian::where('sertifikat_id', $sertifikat->id)->first();
        if ($rincian) {
            // Reset kolom jenis pelatihan lama
            $oldIndex = array_search($oldJenisPelatihan, $this->getAllJenisPelatihan());
            if ($oldIndex !== false) {
                $oldColumn = 'j' . ($oldIndex + 1);
                $rincian->$oldColumn = 0;
            }

            // Set kolom jenis pelatihan baru
            $newIndex = array_search($request->jenis_pelatihan, $this->getAllJenisPelatihan());
            if ($newIndex !== false) {
                $newColumn = 'j' . ($newIndex + 1);
                $rincian->$newColumn = $request->jpl;
            }

            $rincian->save();
        }

        // Recalculate rekap
        $this->recalculateRekap($request->pegawai_id, $tahun);

        $this->logActivity('update', 'Update sertifikat: ' . $request->nama_pelatihan, $sertifikat);
        SertifikatLog::catat($sertifikat->id, 'Edit', $sertifikat->getChanges());

        $message = 'Sertifikat berhasil diperbarui.';
        if ($user->isStaff()) {
            $message = 'Sertifikat berhasil diperbarui dan dikirim ulang untuk verifikasi.';
            $this->notifyAdmins('Sertifikat Diedit Perlu Verifikasi', $user->nama . ' mengedit sertifikat "' . $request->nama_pelatihan . '" dan menunggu verifikasi ulang.', 'warning');
        }

        return redirect()->route('sertifikat.index')
            ->with('success', $message);
    }

    public function resubmit(Request $request, $id)
    {
        $sertifikat = Sertifikat::findOrFail($id);
        $user = Auth::user();

        if (!$user->isStaff() || $sertifikat->pegawai_id !== $user->id || $sertifikat->status !== 'rejected') {
            abort(403);
        }

        $request->validate([
            'nama_pelatihan' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal',
            'jpl' => 'required|integer|min:1|max:10000',
            'jenis_pelatihan' => 'required|string',
            'keterangan' => 'nullable|string|max:500',
            'pdf' => 'nullable|file|mimetypes:application/pdf|max:2048',
        ]);

        $oldJenisPelatihan = $sertifikat->jenis_pelatihan;

        $data = [
            'nama_pelatihan' => $request->nama_pelatihan,
            'penyelenggara' => $request->penyelenggara,
            'tanggal' => $request->tanggal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'jpl' => $request->jpl,
            'jenis_pelatihan' => $request->jenis_pelatihan,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
            'catatan_verifikasi' => null,
            'verified_by' => null,
            'verified_at' => null,
        ];

        if ($request->hasFile('pdf')) {
            if ($sertifikat->pdf && Storage::disk('public')->exists('sertifikat/' . $sertifikat->pdf)) {
                Storage::disk('public')->delete('sertifikat/' . $sertifikat->pdf);
            }

            $pdfFile = $request->file('pdf');
            $pdfName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $pdfFile->getClientOriginalName());
            $stored = $pdfFile->storeAs('sertifikat', $pdfName, 'public');

            if (!$stored) {
                return back()->withInput()->with('error', 'Gagal menyimpan file PDF. Silakan coba lagi.');
            }

            $data['pdf'] = $pdfName;
        }

        $sertifikat->update($data);

        $rincian = Rincian::where('sertifikat_id', $sertifikat->id)->first();
        if ($rincian) {
            $oldIndex = array_search($oldJenisPelatihan, $this->getAllJenisPelatihan());
            if ($oldIndex !== false) {
                $rincian->{'j' . ($oldIndex + 1)} = 0;
            }
            $newIndex = array_search($request->jenis_pelatihan, $this->getAllJenisPelatihan());
            if ($newIndex !== false) {
                $rincian->{'j' . ($newIndex + 1)} = $request->jpl;
            }
            $rincian->save();
        }

        $this->recalculateRekap($sertifikat->pegawai_id, $sertifikat->tahun);
        $this->logActivity('update', 'Kirim ulang sertifikat: ' . $request->nama_pelatihan, $sertifikat);
        SertifikatLog::catat($sertifikat->id, 'Kirim Ulang', $sertifikat->getChanges());

        Notifikasi::create([
            'pegawai_id' => $sertifikat->pegawai_id,
            'judul' => 'Sertifikat Dikirim Ulang',
            'pesan' => 'Sertifikat "' . $request->nama_pelatihan . '" telah dikirim ulang untuk diverifikasi.',
            'tipe' => 'info',
        ]);

        $this->notifyAdmins('Sertifikat Dikirim Ulang', Auth::user()->nama . ' mengirim ulang sertifikat "' . $request->nama_pelatihan . '" untuk diverifikasi.', 'warning');

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil dikirim ulang untuk verifikasi.');
    }

    /**
     * Hapus sertifikat.
     */
    public function destroy($id)
    {
        $sertifikat = Sertifikat::findOrFail($id);
        $tahun = $sertifikat->tahun;
        $pegawaiId = $sertifikat->pegawai_id;

        // Hapus file PDF
        if ($sertifikat->pdf && Storage::disk('public')->exists('sertifikat/' . $sertifikat->pdf)) {
            Storage::disk('public')->delete('sertifikat/' . $sertifikat->pdf);
        }

        // Hapus rincian terkait
        Rincian::where('sertifikat_id', $id)->delete();

        // Hapus sertifikat
        $sertifikat->delete();

        // Recalculate rekap
        $this->recalculateRekap($pegawaiId, $tahun);

        $this->logActivity('delete', 'Hapus sertifikat: ' . $sertifikat->nama_pelatihan);

        return redirect()->route('sertifikat.index')
            ->with('success', 'Sertifikat berhasil dihapus.');
    }

    /**
     * Tampilkan rekap JPL semua pegawai.
     */
    public function rekap(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);

        $rekap = $this->getRekapByPeriode($tahun, $periode, $request->search, $request->status, $request->jabatan);

        return view('sertifikat.rekap', compact('rekap', 'tahun', 'periode', 'periodeLabel'));
    }

    public function cetakPribadi()
    {
        $user = Auth::user();
        $tahun = session('tahun', date('Y'));

        $sertifikatList = Sertifikat::where('pegawai_id', $user->id)
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->orderBy('tanggal')
            ->get();

        $totalJpl = $sertifikatList->sum('jpl');
        $keterangan = $totalJpl >= 20 ? 'Terpenuhi' : 'Belum Terpenuhi';

        $pdf = Pdf::loadView('sertifikat.cetak-pribadi', compact('user', 'sertifikatList', 'tahun', 'totalJpl', 'keterangan'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('Rekap_JPL_' . $user->nama . '_' . $tahun . '.pdf');
    }

    /**
     * Export rekap ke Excel (HTML table dengan content-type header).
     */
    public function cetak(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);

        $rekap = $this->getRekapByPeriode($tahun, $periode);

        $fileName = 'Rekap_JPL_' . $tahun . '_' . $periode . '.xls';

        return response()->view('sertifikat.cetak', compact('rekap', 'tahun', 'periodeLabel'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export rincian ke Excel (HTML table dengan content-type header).
     */
    public function cetakRincian(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenisPelatihan = $this->getJenisPelatihan();

        $data = $this->getRincianByPeriode($tahun, $periode);

        $fileName = 'Rincian_JPL_' . $tahun . '_' . $periode . '.xls';

        return response()->view('sertifikat.cetak-rincian', compact('data', 'tahun', 'jenisPelatihan', 'periodeLabel'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Tampilkan file PDF sertifikat.
     */
    public function showPdf($id)
    {
        $sertifikat = Sertifikat::findOrFail($id);

        if (!$sertifikat->pdf || !Storage::disk('public')->exists('sertifikat/' . $sertifikat->pdf)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path('sertifikat/' . $sertifikat->pdf));
    }

    /**
     * Export rekap ke PDF.
     */
    public function cetakPdf(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);

        $rekap = $this->getRekapByPeriode($tahun, $periode);

        $pdf = Pdf::loadView('sertifikat.cetak', compact('rekap', 'tahun', 'periodeLabel'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('Rekap_JPL_' . $tahun . '_' . $periode . '.pdf');
    }

    /**
     * Export rincian ke PDF.
     */
    public function cetakRincianPdf(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenisPelatihan = $this->getJenisPelatihan();

        $data = $this->getRincianByPeriode($tahun, $periode);

        $pdf = Pdf::loadView('sertifikat.cetak-rincian', compact('data', 'tahun', 'jenisPelatihan', 'periodeLabel'));
        $pdf->setPaper('legal', 'landscape');
        return $pdf->download('Rincian_JPL_' . $tahun . '_' . $periode . '.pdf');
    }

    /**
     * Preview file PDF sertifikat.
     */
    public function previewPdf($id)
    {
        $sertifikat = Sertifikat::findOrFail($id);

        if (!$sertifikat->pdf || !Storage::disk('public')->exists('sertifikat/' . $sertifikat->pdf)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path('sertifikat/' . $sertifikat->pdf), ['Content-Type' => 'application/pdf']);
    }

    /**
     * Setujui sertifikat.
     */
    public function approve(Request $request, $id)
    {
        $sertifikat = Sertifikat::findOrFail($id);

        $sertifikat->update([
            'status' => 'approved',
            'catatan_verifikasi' => $request->catatan,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $this->recalculateRekap($sertifikat->pegawai_id, $sertifikat->tahun);

        Notifikasi::create([
            'pegawai_id' => $sertifikat->pegawai_id,
            'judul' => 'Sertifikat Disetujui',
            'pesan' => 'Sertifikat "' . $sertifikat->nama_pelatihan . '" telah disetujui.' . ($request->catatan ? ' Catatan: ' . $request->catatan : ''),
            'tipe' => 'success',
        ]);

        $this->logActivity('update', 'Setujui sertifikat: ' . $sertifikat->nama_pelatihan, $sertifikat);
        SertifikatLog::catat($sertifikat->id, 'Disetujui', ['catatan' => $request->catatan]);
        $this->checkAndNotify($sertifikat->pegawai_id, $sertifikat->tahun);

        return back()->with('success', 'Sertifikat berhasil disetujui.');
    }

    /**
     * Tolak sertifikat.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $sertifikat = Sertifikat::findOrFail($id);

        $sertifikat->update([
            'status' => 'rejected',
            'catatan_verifikasi' => $request->catatan,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $this->recalculateRekap($sertifikat->pegawai_id, $sertifikat->tahun);

        Notifikasi::create([
            'pegawai_id' => $sertifikat->pegawai_id,
            'judul' => 'Sertifikat Ditolak',
            'pesan' => 'Sertifikat "' . $sertifikat->nama_pelatihan . '" ditolak. Catatan: ' . $request->catatan,
            'tipe' => 'danger',
        ]);

        $this->logActivity('update', 'Tolak sertifikat: ' . $sertifikat->nama_pelatihan, $sertifikat);
        SertifikatLog::catat($sertifikat->id, 'Ditolak', ['catatan' => $request->catatan]);

        return back()->with('success', 'Sertifikat ditolak.');
    }

    /**
     * Tampilkan daftar sertifikat pending.
     */
    public function pending()
    {
        $tahun = session('tahun', date('Y'));
        $sertifikat = Sertifikat::with('pegawai')
            ->where('tahun', $tahun)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('sertifikat.pending', compact('sertifikat', 'tahun'));
    }

    public function batchApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:1']);

        $count = 0;
        foreach ($request->ids as $id) {
            $s = Sertifikat::find($id);
            if ($s && $s->status === 'pending') {
                $s->update(['status' => 'approved', 'verified_by' => Auth::id(), 'verified_at' => now()]);
                $this->recalculateRekap($s->pegawai_id, $s->tahun);
                Notifikasi::create(['pegawai_id' => $s->pegawai_id, 'judul' => 'Sertifikat Disetujui', 'pesan' => 'Sertifikat "' . $s->nama_pelatihan . '" telah disetujui.', 'tipe' => 'success']);
                $this->checkAndNotify($s->pegawai_id, $s->tahun);
                $count++;
            }
        }

        $this->logActivity('update', "Batch approve {$count} sertifikat");
        return back()->with('success', "{$count} sertifikat berhasil disetujui.");
    }

    public function batchReject(Request $request)
    {
        $request->validate(['ids' => 'required|array|min:1', 'catatan' => 'required|string|max:500']);

        $count = 0;
        foreach ($request->ids as $id) {
            $s = Sertifikat::find($id);
            if ($s && $s->status === 'pending') {
                $s->update(['status' => 'rejected', 'catatan_verifikasi' => $request->catatan, 'verified_by' => Auth::id(), 'verified_at' => now()]);
                $this->recalculateRekap($s->pegawai_id, $s->tahun);
                Notifikasi::create(['pegawai_id' => $s->pegawai_id, 'judul' => 'Sertifikat Ditolak', 'pesan' => 'Sertifikat "' . $s->nama_pelatihan . '" ditolak. Catatan: ' . $request->catatan, 'tipe' => 'danger']);
                $count++;
            }
        }

        $this->logActivity('update', "Batch reject {$count} sertifikat");
        return back()->with('success', "{$count} sertifikat ditolak.");
    }

    /**
     * Halaman download sertifikat per pegawai.
     */
    public function downloadPage(Request $request)
    {
        $user = Auth::user();

        if ($user->level === 'Staff') {
            $pegawaiList = Pegawai::where('id', $user->id)->get();
        } else {
            $pegawaiList = Pegawai::aktif()->orderBy('nama')->get();
        }

        $results = null;
        $selectedPegawaiIds = [];
        $selectedPegawaiNames = [];

        if ($request->filled('pegawai_id')) {
            $ids = $request->pegawai_id;
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (in_array('all', $ids)) {
                $ids = $pegawaiList->pluck('id')->toArray();
            }

            $selectedPegawaiIds = $ids;
            $selectedPegawaiNames = Pegawai::whereIn('id', $ids)->pluck('nama', 'id');

            $query = Sertifikat::with('pegawai')->whereIn('pegawai_id', $ids);

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal', $request->bulan);
            }

            $results = $query->orderBy('pegawai_id')->orderBy('tanggal', 'desc')->get();
        }

        return view('sertifikat.download', compact('pegawaiList', 'results', 'selectedPegawaiIds', 'selectedPegawaiNames'));
    }

    public function downloadZip(Request $request)
    {
        $pegawaiIds = $request->pegawai_id;
        if (is_string($pegawaiIds)) {
            $pegawaiIds = explode(',', $pegawaiIds);
        }

        if (empty($pegawaiIds)) {
            return back()->with('error', 'Pilih minimal satu pegawai.');
        }

        if (in_array('all', $pegawaiIds)) {
            $pegawaiIds = Pegawai::aktif()->pluck('id')->toArray();
        }

        $query = Sertifikat::with('pegawai')->whereIn('pegawai_id', $pegawaiIds);

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }

        $sertifikats = $query->orderBy('pegawai_id')->orderBy('tanggal', 'desc')->get();

        if ($sertifikats->isEmpty()) {
            return back()->with('error', 'Tidak ada sertifikat untuk didownload.');
        }

        $isMulti = count($pegawaiIds) > 1;
        $zipName = 'Sertifikat';
        if (!$isMulti) {
            $pegawai = Pegawai::find($pegawaiIds[0]);
            $zipName .= '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $pegawai->nama ?? 'pegawai');
        } else {
            $zipName .= '_' . count($pegawaiIds) . '_Pegawai';
        }
        if ($request->filled('tahun')) $zipName .= '_' . $request->tahun;
        $zipName .= '.zip';

        $zipPath = storage_path('app/temp/' . $zipName);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        $count = 0;
        foreach ($sertifikats as $s) {
            if (!$s->pdf) continue;
            if (!Storage::disk('public')->exists('sertifikat/' . $s->pdf)) continue;
            $filePath = Storage::disk('public')->path('sertifikat/' . $s->pdf);
            if (file_exists($filePath)) {
                $folderName = $isMulti ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $s->pegawai->nama ?? 'unknown') . '/' : '';
                $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $s->nama_pelatihan);
                $fileName = $folderName . mb_substr($safeName, 0, 80) . '.pdf';
                $zip->addFile($filePath, $fileName);
                $count++;
            }
        }

        $zip->close();

        if ($count === 0) {
            @unlink($zipPath);
            return back()->with('error', 'Tidak ada file PDF yang ditemukan.');
        }

        $this->logActivity('download', "Download {$count} sertifikat dari " . count($pegawaiIds) . " pegawai");

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Cek dan kirim notifikasi jika target JPL tercapai.
     */
    private function checkAndNotify($pegawaiId, $tahun)
    {
        $rekap = Rekap::where('pegawai_id', $pegawaiId)->where('tahun', $tahun)->first();
        if ($rekap && $rekap->jumlah_jpl >= 20 && $rekap->keterangan === 'Terpenuhi') {
            $exists = Notifikasi::where('pegawai_id', $pegawaiId)
                ->where('judul', 'Target JPL Tercapai')
                ->where('tipe', 'success')
                ->whereYear('created_at', $tahun)
                ->exists();
            if (!$exists) {
                Notifikasi::create([
                    'pegawai_id' => $pegawaiId,
                    'judul' => 'Target JPL Tercapai',
                    'pesan' => 'Selamat! Anda telah mencapai target 20 JPL pada tahun ' . $tahun . '. Total JPL Anda: ' . $rekap->jumlah_jpl,
                    'tipe' => 'success',
                ]);
            }
        }
    }

    private function getPeriodeLabel($periode)
    {
        $labels = [
            'tahunan' => 'Tahunan',
            'triwulan1' => 'Triwulan I (Januari - Maret)',
            'triwulan2' => 'Triwulan II (April - Juni)',
            'triwulan3' => 'Triwulan III (Juli - September)',
            'triwulan4' => 'Triwulan IV (Oktober - Desember)',
            'semester1' => 'Semester I (Januari - Juni)',
            'semester2' => 'Semester II (Juli - Desember)',
        ];
        return $labels[$periode] ?? 'Tahunan';
    }

    private function getPeriodeDateRange($periode, $tahun)
    {
        return match ($periode) {
            'triwulan1' => ["{$tahun}-01-01", "{$tahun}-03-31"],
            'triwulan2' => ["{$tahun}-04-01", "{$tahun}-06-30"],
            'triwulan3' => ["{$tahun}-07-01", "{$tahun}-09-30"],
            'triwulan4' => ["{$tahun}-10-01", "{$tahun}-12-31"],
            'semester1'  => ["{$tahun}-01-01", "{$tahun}-06-30"],
            'semester2'  => ["{$tahun}-07-01", "{$tahun}-12-31"],
            default      => null,
        };
    }

    private function getRekapByPeriode($tahun, $periode, $search = null, $status = null, $jabatan = null)
    {
        $dateRange = $this->getPeriodeDateRange($periode, $tahun);

        $pegawaiQuery = Pegawai::aktif();
        if ($search) {
            $pegawaiQuery->where(fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%"));
        }
        if ($jabatan) {
            $pegawaiQuery->where('jabatan', $jabatan);
        }
        $pegawaiList = $pegawaiQuery->orderBy('nama')->get();

        $rekapByPegawai = Rekap::where('tahun', $tahun)->get()->keyBy('pegawai_id');

        $result = collect();
        foreach ($pegawaiList as $pegawai) {
            if ($dateRange) {
                $totalJpl = Sertifikat::where('pegawai_id', $pegawai->id)
                    ->where('tahun', $tahun)
                    ->where('status', 'approved')
                    ->whereBetween('tanggal', $dateRange)
                    ->sum('jpl');
            } else {
                $rekap = $rekapByPegawai->get($pegawai->id);
                $totalJpl = $rekap ? $rekap->jumlah_jpl : 0;
            }

            $keterangan = $totalJpl >= 20 ? 'Terpenuhi' : 'Belum Terpenuhi';

            if ($status && $keterangan !== $status) {
                continue;
            }

            $item = new \stdClass();
            $item->pegawai = $pegawai;
            $item->jumlah_jpl = $totalJpl;
            $item->keterangan = $keterangan;
            $result->push($item);
        }

        return $result->sortByDesc('jumlah_jpl')->values();
    }

    private function getRincianByPeriode($tahun, $periode)
    {
        $dateRange = $this->getPeriodeDateRange($periode, $tahun);
        $pegawaiList = Pegawai::aktif()->orderBy('nama', 'asc')->get();
        $jenisPelatihan = $this->getJenisPelatihan();

        $data = [];
        foreach ($pegawaiList as $pegawai) {
            $sertifikatQuery = Sertifikat::where('pegawai_id', $pegawai->id)
                ->where('tahun', $tahun)
                ->where('status', 'approved');

            if ($dateRange) {
                $sertifikatQuery->whereBetween('tanggal', $dateRange);
            }

            $sertifikatList = $sertifikatQuery->get();

            $jplPerJenis = array_fill(0, 25, 0);
            foreach ($sertifikatList as $sertifikat) {
                $index = array_search($sertifikat->jenis_pelatihan, $jenisPelatihan);
                if ($index !== false) {
                    $jplPerJenis[$index] += (int) $sertifikat->jpl;
                }
            }

            $totalJpl = array_sum($jplPerJenis);

            $data[] = [
                'pegawai' => $pegawai,
                'jpl_per_jenis' => $jplPerJenis,
                'jumlah_jpl' => $totalJpl,
                'keterangan' => $totalJpl >= 20 ? 'Terpenuhi' : 'Belum Terpenuhi',
            ];
        }

        return $data;
    }

    public function rekapJenis(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenisPelatihan = $this->getJenisPelatihan();

        $data = $this->getRekapPerJenis($tahun, $periode);

        return view('sertifikat.rekap-jenis', compact('data', 'tahun', 'periode', 'periodeLabel', 'jenisPelatihan'));
    }

    public function cetakJenis(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenisPelatihan = $this->getJenisPelatihan();

        $data = $this->getRekapPerJenis($tahun, $periode);

        $fileName = 'Rekap_Jenis_Pelatihan_' . $tahun . '_' . $periode . '.xls';

        return response()->view('sertifikat.cetak-jenis', compact('data', 'tahun', 'periodeLabel', 'jenisPelatihan'))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function cetakJenisPdf(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenisPelatihan = $this->getJenisPelatihan();

        $data = $this->getRekapPerJenis($tahun, $periode);

        $pdf = Pdf::loadView('sertifikat.cetak-jenis', compact('data', 'tahun', 'periodeLabel', 'jenisPelatihan'));
        $pdf->setPaper('a4', 'landscape');
        return $pdf->download('Rekap_Jenis_Pelatihan_' . $tahun . '_' . $periode . '.pdf');
    }

    public function detailJenis(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenis = $request->get('jenis', '');

        if (!JenisPelatihan::where('nama', $jenis)->exists()) {
            return redirect()->route('sertifikat.rekapJenis')->with('error', 'Jenis pelatihan tidak valid.');
        }

        $dateRange = $this->getPeriodeDateRange($periode, $tahun);

        $query = Sertifikat::with('pegawai')
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->where('jenis_pelatihan', $jenis);

        if ($dateRange) {
            $query->whereBetween('tanggal', $dateRange);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%"));
        }

        $sertifikatList = $query->orderBy('tanggal', 'desc')->get();

        $grouped = $sertifikatList->groupBy('pegawai_id')->map(function ($items) {
            return [
                'pegawai' => $items->first()->pegawai,
                'sertifikat' => $items,
                'total_jpl' => $items->sum('jpl'),
                'jumlah_pelatihan' => $items->count(),
            ];
        })->sortByDesc('total_jpl')->values();

        return view('sertifikat.detail-jenis', compact('grouped', 'tahun', 'periode', 'periodeLabel', 'jenis'));
    }

    public function cetakDetailJenis(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $periode = $request->get('periode', 'tahunan');
        $periodeLabel = $this->getPeriodeLabel($periode);
        $jenis = $request->get('jenis', '');

        if (!JenisPelatihan::where('nama', $jenis)->exists()) {
            return redirect()->route('sertifikat.rekapJenis');
        }

        $dateRange = $this->getPeriodeDateRange($periode, $tahun);

        $query = Sertifikat::with('pegawai')
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->where('jenis_pelatihan', $jenis);

        if ($dateRange) {
            $query->whereBetween('tanggal', $dateRange);
        }

        $sertifikatList = $query->orderBy('tanggal', 'desc')->get();

        $grouped = $sertifikatList->groupBy('pegawai_id')->map(function ($items) {
            return [
                'pegawai' => $items->first()->pegawai,
                'sertifikat' => $items,
                'total_jpl' => $items->sum('jpl'),
                'jumlah_pelatihan' => $items->count(),
            ];
        })->sortByDesc('total_jpl')->values();

        $view = 'sertifikat.cetak-detail-jenis';
        $data = compact('grouped', 'tahun', 'periodeLabel', 'jenis');

        if ($request->get('format') === 'pdf') {
            $pdf = Pdf::loadView($view, $data);
            $pdf->setPaper('a4', 'landscape');
            return $pdf->download('Detail_' . str_replace(['/', ' '], '_', $jenis) . '_' . $tahun . '_' . $periode . '.pdf');
        }

        $fileName = 'Detail_' . str_replace(['/', ' '], '_', $jenis) . '_' . $tahun . '_' . $periode . '.xls';
        return response()->view($view, $data)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function getRekapPerJenis($tahun, $periode)
    {
        $dateRange = $this->getPeriodeDateRange($periode, $tahun);
        $jenisPelatihan = $this->getJenisPelatihan();

        $query = Sertifikat::with('pegawai')->where('tahun', $tahun)->where('status', 'approved');
        if ($dateRange) {
            $query->whereBetween('tanggal', $dateRange);
        }
        $sertifikatList = $query->orderBy('tanggal', 'desc')->get();

        $data = [];
        foreach ($jenisPelatihan as $jenis) {
            $filtered = $sertifikatList->where('jenis_pelatihan', $jenis);
            $data[] = [
                'jenis' => $jenis,
                'jumlah_sertifikat' => $filtered->count(),
                'jumlah_pegawai' => $filtered->unique('pegawai_id')->count(),
                'total_jpl' => $filtered->sum('jpl'),
                'sertifikat' => $filtered->values(),
            ];
        }

        return $data;
    }

    /**
     * Hitung ulang rekap JPL untuk pegawai dan tahun tertentu.
     */
    private function notifyAdmins($judul, $pesan, $tipe = 'info')
    {
        $admins = Pegawai::aktif()->where('level', '!=', 'Staff')->pluck('id');
        foreach ($admins as $adminId) {
            if ($adminId === Auth::id()) continue;
            Notifikasi::create([
                'pegawai_id' => $adminId,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
            ]);
        }
    }

    private function recalculateRekap($pegawaiId, $tahun)
    {
        $totalJpl = Sertifikat::where('pegawai_id', $pegawaiId)
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->sum('jpl');

        Rekap::updateOrCreate(
            ['pegawai_id' => $pegawaiId, 'tahun' => $tahun],
            [
                'jumlah_jpl' => $totalJpl,
                'keterangan' => $totalJpl >= 20 ? 'Terpenuhi' : 'Belum Terpenuhi',
            ]
        );
    }
}
