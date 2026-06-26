<?php

namespace App\Http\Controllers;

use App\Models\JenisPelatihan;
use App\Models\Pegawai;
use App\Models\Rekap;
use App\Models\Sertifikat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tahun = session('tahun', date('Y'));

        $rekap = Rekap::where('pegawai_id', $user->id)
            ->where('tahun', $tahun)
            ->first();

        $jumlahSertifikat = Sertifikat::where('pegawai_id', $user->id)
            ->where('tahun', $tahun)
            ->count();

        $recentSertifikat = Sertifikat::where('pegawai_id', $user->id)
            ->where('tahun', $tahun)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        $jenisDist = Sertifikat::where('pegawai_id', $user->id)
            ->where('tahun', $tahun)
            ->selectRaw('jenis_pelatihan, COUNT(*) as total, SUM(jpl) as total_jpl')
            ->groupBy('jenis_pelatihan')
            ->orderByDesc('total_jpl')
            ->get();

        $userMonthly = Sertifikat::where('pegawai_id', $user->id)
            ->where('tahun', $tahun)
            ->selectRaw('MONTH(tanggal) as bulan, SUM(jpl) as total_jpl')
            ->groupByRaw('MONTH(tanggal)')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $userChartLabels = [];
        $userChartJpl = [];
        $bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        $cumulative = 0;
        for ($i = 1; $i <= 12; $i++) {
            $userChartLabels[] = $bulanNames[$i];
            $cumulative += $userMonthly->get($i)->total_jpl ?? 0;
            $userChartJpl[] = $cumulative;
        }

        // Staff insights: rekomendasi pelatihan & progress bulanan
        $staffInsights = null;
        if ($user->isStaff()) {
            // Training types staff has completed this year
            $completedJenis = Sertifikat::where('pegawai_id', $user->id)
                ->where('tahun', $tahun)
                ->where('status', 'approved')
                ->distinct()
                ->pluck('jenis_pelatihan')
                ->toArray();

            // Active training types for the year
            $allJenisAktif = JenisPelatihan::aktifByTahun($tahun);

            // Missing types = active types minus completed types
            $missingJenis = array_values(array_diff($allJenisAktif, $completedJenis));

            // Monthly activity: which months have approved sertifikat
            $activeMonths = Sertifikat::where('pegawai_id', $user->id)
                ->where('tahun', $tahun)
                ->where('status', 'approved')
                ->selectRaw('DISTINCT MONTH(tanggal) as bulan')
                ->pluck('bulan')
                ->toArray();

            $staffInsights = compact('missingJenis', 'completedJenis', 'allJenisAktif', 'activeMonths');
        }

        $adminStats = null;
        $dashPeriode = request('periode', 'tahunan');
        if ($user->isAdmin()) {
            $totalPegawai = Pegawai::aktif()->count();

            $totalSertifikatAll = Sertifikat::where('tahun', $tahun)->where('status', 'approved')->count();
            $totalJplAll = Sertifikat::where('tahun', $tahun)->where('status', 'approved')->sum('jpl');

            $bulanFullNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $periodeLabels = [
                'tahunan' => 'Tahunan',
                'triwulan1' => 's.d. Triwulan I', 'triwulan2' => 's.d. Triwulan II',
                'triwulan3' => 's.d. Triwulan III', 'triwulan4' => 's.d. Triwulan IV',
                'semester1' => 's.d. Semester I', 'semester2' => 's.d. Semester II',
            ];
            for ($m = 1; $m <= 12; $m++) {
                $periodeLabels['bulan' . $m] = 's.d. ' . $bulanFullNames[$m];
            }
            $dashPeriodeLabel = $periodeLabels[$dashPeriode] ?? 'Tahunan';

            $dateRange = match (true) {
                $dashPeriode === 'triwulan1' => ["{$tahun}-01-01", "{$tahun}-03-31"],
                $dashPeriode === 'triwulan2' => ["{$tahun}-01-01", "{$tahun}-06-30"],
                $dashPeriode === 'triwulan3' => ["{$tahun}-01-01", "{$tahun}-09-30"],
                $dashPeriode === 'triwulan4' => ["{$tahun}-01-01", "{$tahun}-12-31"],
                $dashPeriode === 'semester1'  => ["{$tahun}-01-01", "{$tahun}-06-30"],
                $dashPeriode === 'semester2'  => ["{$tahun}-01-01", "{$tahun}-12-31"],
                str_starts_with($dashPeriode, 'bulan') => (function() use ($dashPeriode, $tahun) {
                    $m = (int) str_replace('bulan', '', $dashPeriode);
                    $end = date('Y-m-t', strtotime(sprintf('%s-%02d-01', $tahun, $m)));
                    return ["{$tahun}-01-01", $end];
                })(),
                default => null,
            };

            $pegawaiAll = Pegawai::aktif()->orderBy('nama')->get();
            $rekapByPegawai = Rekap::where('tahun', $tahun)->get()->keyBy('pegawai_id');
            $periodeRekap = collect();

            foreach ($pegawaiAll as $p) {
                if ($dateRange) {
                    $jplPeriode = Sertifikat::where('pegawai_id', $p->id)->where('tahun', $tahun)
                        ->where('status', 'approved')->whereBetween('tanggal', $dateRange)->sum('jpl');
                } else {
                    $rekap = $rekapByPegawai->get($p->id);
                    $jplPeriode = $rekap ? $rekap->jumlah_jpl : 0;
                }
                $item = new \stdClass();
                $item->pegawai = $p;
                $item->jumlah_jpl = $jplPeriode;
                $item->keterangan = $jplPeriode >= 20 ? 'Terpenuhi' : 'Belum Terpenuhi';
                $periodeRekap->push($item);
            }

            $totalRekapCount = $periodeRekap->count();
            $avgJpl = $totalRekapCount > 0 ? round($periodeRekap->avg('jumlah_jpl'), 1) : 0;
            $terpenuhi = $periodeRekap->where('keterangan', 'Terpenuhi')->count();
            $belumTerpenuhi = $totalRekapCount - $terpenuhi;
            $topPerformers = $periodeRekap->sortByDesc('jumlah_jpl')->take(5)->values();
            $bottomPerformers = $periodeRekap->sortBy('jumlah_jpl')->take(5)->values();

            $persenTerpenuhi = $totalRekapCount > 0 ? round(($terpenuhi / $totalRekapCount) * 100) : 0;

            $recentUploads = Sertifikat::with('pegawai')->where('tahun', $tahun)->orderBy('created_at', 'desc')->limit(6)->get();

            $jenisDistOrg = Sertifikat::where('tahun', $tahun)->where('status', 'approved')
                ->selectRaw('jenis_pelatihan, COUNT(*) as total, SUM(jpl) as total_jpl')
                ->groupBy('jenis_pelatihan')->orderByDesc('total')->limit(8)->get();

            $monthlyData = Sertifikat::where('tahun', $tahun)->where('status', 'approved')
                ->selectRaw('MONTH(tanggal) as bulan, COUNT(*) as jumlah, SUM(jpl) as total_jpl')
                ->groupByRaw('MONTH(tanggal)')->orderBy('bulan')->get()->keyBy('bulan');

            $chartLabels = [];
            $chartJumlah = [];
            $chartJpl = [];
            $bulanNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($i = 1; $i <= 12; $i++) {
                $chartLabels[] = $bulanNames[$i];
                $chartJumlah[] = $monthlyData->get($i)->jumlah ?? 0;
                $chartJpl[] = $monthlyData->get($i)->total_jpl ?? 0;
            }

            $adminStats = compact(
                'totalPegawai', 'avgJpl', 'terpenuhi', 'belumTerpenuhi', 'persenTerpenuhi',
                'totalSertifikatAll', 'totalJplAll',
                'topPerformers', 'bottomPerformers', 'recentUploads', 'jenisDistOrg',
                'chartLabels', 'chartJumlah', 'chartJpl', 'dashPeriode', 'dashPeriodeLabel'
            );
        }

        return view('home.index', compact('user', 'tahun', 'rekap', 'jumlahSertifikat', 'recentSertifikat', 'jenisDist', 'userChartLabels', 'userChartJpl', 'staffInsights', 'adminStats'));
    }

    public function gantiTahun(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
        ]);

        session(['tahun' => $request->tahun]);

        return back()->with('success', 'Tahun berhasil diubah ke ' . $request->tahun . '.');
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $tahun = session('tahun', date('Y'));
        $results = [];

        $pegawai = Pegawai::aktif()->where('nama', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->limit(5)->get();

        foreach ($pegawai as $p) {
            $results[] = [
                'type' => 'pegawai',
                'id' => $p->id,
                'title' => $p->nama,
                'subtitle' => $p->nip . ' - ' . ($p->jabatan ?? '-'),
                'initial' => strtoupper(substr($p->nama, 0, 1)),
                'url' => Auth::user()->isAdmin() ? route('pegawai.index', ['search' => $p->nip]) : '#',
            ];
        }

        $sertifikat = Sertifikat::with('pegawai')
            ->where('tahun', $tahun)
            ->where(function ($query) use ($q) {
                $query->where('nama_pelatihan', 'like', "%{$q}%")
                      ->orWhere('penyelenggara', 'like', "%{$q}%");
            })
            ->limit(5)->get();

        foreach ($sertifikat as $s) {
            $results[] = [
                'type' => 'sertifikat',
                'id' => $s->id,
                'title' => $s->nama_pelatihan,
                'subtitle' => ($s->pegawai->nama ?? '-') . ' - ' . $s->jpl . ' JPL',
                'initial' => strtoupper(substr($s->nama_pelatihan, 0, 1)),
                'url' => route('sertifikat.index', ['search' => $s->pegawai->nip ?? '']),
            ];
        }

        return response()->json($results);
    }

    public function comparison()
    {
        $user = Auth::user();

        $years = Rekap::selectRaw('DISTINCT tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $comparisonData = [];
        foreach ($years as $year) {
            $rekapYear = Rekap::where('tahun', $year);
            $count = (clone $rekapYear)->count();
            $comparisonData[] = [
                'tahun' => $year,
                'avg_jpl' => $count > 0 ? round((clone $rekapYear)->avg('jumlah_jpl'), 1) : 0,
                'terpenuhi' => (clone $rekapYear)->where('keterangan', 'Terpenuhi')->count(),
                'total_pegawai' => $count,
                'total_sertifikat' => Sertifikat::where('tahun', $year)->count(),
            ];
        }

        // Personal comparison for current user
        $personalData = [];
        $personalYears = Rekap::where('pegawai_id', $user->id)
            ->orderBy('tahun', 'desc')
            ->get();
        foreach ($personalYears as $r) {
            $personalData[] = [
                'tahun' => $r->tahun,
                'jpl' => $r->jumlah_jpl,
                'sertifikat' => Sertifikat::where('pegawai_id', $user->id)->where('tahun', $r->tahun)->count(),
                'keterangan' => $r->keterangan,
            ];
        }

        return view('home.comparison', compact('comparisonData', 'personalData', 'years'));
    }

    public function kalender(Request $request)
    {
        $tahun = session('tahun', date('Y'));
        $bulan = (int) $request->get('bulan', date('n'));

        // Clamp to 1-12
        if ($bulan < 1) $bulan = 1;
        if ($bulan > 12) $bulan = 12;

        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        // Query approved sertifikat for this year & month, with pegawai
        $sertifikat = Sertifikat::with('pegawai')
            ->where('tahun', $tahun)
            ->where('status', 'approved')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                  ->orWhereBetween('tanggal_akhir', [$startOfMonth, $endOfMonth])
                  ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                      $q2->where('tanggal', '<=', $startOfMonth)
                         ->where('tanggal_akhir', '>=', $endOfMonth);
                  });
            })
            ->orderBy('tanggal')
            ->get();

        // Build per-day mapping: for multi-day trainings, list them on each day they span
        $dayData = [];
        foreach ($sertifikat as $s) {
            $from = $s->tanggal->copy()->max($startOfMonth);
            $to   = ($s->tanggal_akhir ?? $s->tanggal)->copy()->min($endOfMonth);
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $dayData[$d->day][] = $s;
            }
        }

        $bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $namaBulan = $bulanNames[$bulan];

        // Calendar grid info
        $daysInMonth    = $startOfMonth->daysInMonth;
        // dayOfWeekIso: 1=Monday ... 7=Sunday
        $firstDayOfWeek = $startOfMonth->dayOfWeekIso;

        // Prev/next month params
        $prevBulan = $bulan - 1;
        $nextBulan = $bulan + 1;

        return view('home.kalender', compact(
            'tahun', 'bulan', 'namaBulan',
            'daysInMonth', 'firstDayOfWeek',
            'dayData', 'sertifikat',
            'prevBulan', 'nextBulan'
        ));
    }
}
