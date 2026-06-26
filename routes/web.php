<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\JenisPelatihanController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth', \App\Http\Middleware\CheckTahun::class])->group(function () {

    // Dashboard
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/comparison', [HomeController::class, 'comparison'])->name('home.comparison');
    Route::get('/kalender', [HomeController::class, 'kalender'])->name('home.kalender');

    // Ganti Tahun
    Route::post('/ganti-tahun', [HomeController::class, 'gantiTahun'])->name('ganti.tahun');
    Route::get('/search', [HomeController::class, 'search'])->name('search')->middleware('throttle:60,1');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/riwayat', [ProfileController::class, 'riwayat'])->name('profile.riwayat');

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.readAll');

    // Sertifikat approval (non-Staff only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin,Kepala Kantor,Ka. Subbag Adum'])->group(function () {
        Route::get('/sertifikat/pending', [SertifikatController::class, 'pending'])->name('sertifikat.pending');
        Route::post('/sertifikat/{id}/approve', [SertifikatController::class, 'approve'])->name('sertifikat.approve');
        Route::post('/sertifikat/{id}/reject', [SertifikatController::class, 'reject'])->name('sertifikat.reject');
        Route::post('/sertifikat/batch-approve', [SertifikatController::class, 'batchApprove'])->name('sertifikat.batchApprove');
        Route::post('/sertifikat/batch-reject', [SertifikatController::class, 'batchReject'])->name('sertifikat.batchReject');
    });

    // Import (Admin only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin'])->group(function () {
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import', [ImportController::class, 'import'])->name('import.store');
        Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
    });

    // Bulk Upload (Admin only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin,Kepala Kantor,Ka. Subbag Adum'])->group(function () {
        Route::get('/sertifikat/bulk-upload', [SertifikatController::class, 'bulkCreate'])->name('sertifikat.bulkCreate');
        Route::post('/sertifikat/bulk-upload', [SertifikatController::class, 'bulkStore'])->name('sertifikat.bulkStore');
    });

    // Sertifikat
    Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
    Route::get('/sertifikat/live-search', [SertifikatController::class, 'liveSearch'])->name('sertifikat.liveSearch')->middleware('throttle:60,1');
    Route::get('/sertifikat/upload', [SertifikatController::class, 'create'])->name('sertifikat.create');
    Route::post('/sertifikat', [SertifikatController::class, 'store'])->name('sertifikat.store');
    Route::put('/sertifikat/{id}', [SertifikatController::class, 'update'])->name('sertifikat.update');
    Route::post('/sertifikat/{id}/resubmit', [SertifikatController::class, 'resubmit'])->name('sertifikat.resubmit');
    Route::delete('/sertifikat/{id}', [SertifikatController::class, 'destroy'])->name('sertifikat.destroy');
    Route::get('/sertifikat/cetak-pribadi', [SertifikatController::class, 'cetakPribadi'])->name('sertifikat.cetakPribadi');
    Route::get('/sertifikat/rekap', [SertifikatController::class, 'rekap'])->name('sertifikat.rekap');
    // Export & Rekap Jenis (Admin, Kepala Kantor, Ka. Subbag Adum only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin,Kepala Kantor,Ka. Subbag Adum'])->group(function () {
        Route::get('/sertifikat/cetak', [SertifikatController::class, 'cetak'])->name('sertifikat.cetak');
        Route::get('/sertifikat/cetak-pdf', [SertifikatController::class, 'cetakPdf'])->name('sertifikat.cetakPdf');
        Route::get('/sertifikat/cetak-rincian', [SertifikatController::class, 'cetakRincian'])->name('sertifikat.cetakRincian');
        Route::get('/sertifikat/cetak-rincian-pdf', [SertifikatController::class, 'cetakRincianPdf'])->name('sertifikat.cetakRincianPdf');
        Route::get('/sertifikat/rekap-jenis', [SertifikatController::class, 'rekapJenis'])->name('sertifikat.rekapJenis');
        Route::get('/sertifikat/cetak-jenis', [SertifikatController::class, 'cetakJenis'])->name('sertifikat.cetakJenis');
        Route::get('/sertifikat/cetak-jenis-pdf', [SertifikatController::class, 'cetakJenisPdf'])->name('sertifikat.cetakJenisPdf');
        Route::get('/sertifikat/detail-jenis', [SertifikatController::class, 'detailJenis'])->name('sertifikat.detailJenis');
        Route::get('/sertifikat/cetak-detail-jenis', [SertifikatController::class, 'cetakDetailJenis'])->name('sertifikat.cetakDetailJenis');
    });
    Route::get('/sertifikat/{id}/pdf', [SertifikatController::class, 'showPdf'])->name('sertifikat.pdf');
    Route::get('/sertifikat/{id}/preview', [SertifikatController::class, 'previewPdf'])->name('sertifikat.preview');
    Route::get('/sertifikat/download', [SertifikatController::class, 'downloadPage'])->name('sertifikat.download');
    Route::get('/sertifikat/download/zip', [SertifikatController::class, 'downloadZip'])->name('sertifikat.downloadZip');

    // Jenis Pelatihan (Admin only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin'])->group(function () {
        Route::get('/jenis-pelatihan', [JenisPelatihanController::class, 'index'])->name('jenis-pelatihan.index');
        Route::post('/jenis-pelatihan', [JenisPelatihanController::class, 'store'])->name('jenis-pelatihan.store');
        Route::put('/jenis-pelatihan/{id}', [JenisPelatihanController::class, 'update'])->name('jenis-pelatihan.update');
        Route::post('/jenis-pelatihan/{id}/toggle', [JenisPelatihanController::class, 'toggleAktif'])->name('jenis-pelatihan.toggle');
        Route::post('/jenis-pelatihan/salin-tahun', [JenisPelatihanController::class, 'copyFromYear'])->name('jenis-pelatihan.copy');
        Route::delete('/jenis-pelatihan/{id}', [JenisPelatihanController::class, 'destroy'])->name('jenis-pelatihan.destroy');
    });

    // Pegawai (Admin, Kepala Kantor, Ka. Subbag Adum only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin,Kepala Kantor,Ka. Subbag Adum'])->group(function () {
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::post('/pegawai/{id}/reset-password', [PegawaiController::class, 'resetPassword'])->name('pegawai.resetPassword');
        Route::post('/pegawai/{id}/toggle-aktif', [PegawaiController::class, 'toggleAktif'])->name('pegawai.toggleAktif');
        Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
    });

    // Activity Log (Admin only)
    Route::middleware([\App\Http\Middleware\CheckRole::class . ':Admin'])->group(function () {
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');
    });
});
