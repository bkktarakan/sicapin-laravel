@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $jpl = $rekap->jumlah_jpl ?? 0;
    $progress = min(100, round(($jpl / 20) * 100));
    $sisa = max(0, 20 - $jpl);
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Welcome Banner --}}
    <div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 rounded-2xl p-6 sm:p-8 text-white overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <p class="text-primary-200 text-sm">Selamat Datang,</p>
                <h1 class="text-2xl sm:text-3xl font-bold mt-1">{{ $user->nama }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/15 text-white/90 text-xs font-medium">{{ $user->level }}</span>
                    <span class="text-primary-200">&middot;</span>
                    <span class="text-primary-200">{{ $user->jabatan ?? '-' }}</span>
                </div>
            </div>
            {{-- Circular Progress --}}
            <div class="flex-shrink-0 flex items-center gap-4">
                <div class="relative w-24 h-24">
                    <svg class="w-24 h-24 -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" stroke="rgba(255,255,255,0.15)" stroke-width="8" fill="none"/>
                        <circle cx="50" cy="50" r="42" stroke="white" stroke-width="8" fill="none"
                                stroke-dasharray="{{ 2 * 3.14159 * 42 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 42 * (1 - $progress / 100) }}"
                                stroke-linecap="round" class="transition-all duration-1000"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-bold">{{ $jpl }}</span>
                        <span class="text-[10px] text-primary-200">/ 20 JPL</span>
                    </div>
                </div>
                <div class="hidden sm:block text-right">
                    <p class="text-3xl font-bold">{{ $progress }}%</p>
                    <p class="text-xs text-primary-200 mt-0.5">
                        @if($progress >= 100)
                            Target tercapai!
                        @else
                            Sisa {{ $sisa }} JPL lagi
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jumlahSertifikat }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Sertifikat</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jpl }} <span class="text-sm font-normal text-gray-400 dark:text-gray-500">JPL</span></p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Jam</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $progress >= 100 ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-center flex-shrink-0">
                @if($progress >= 100)
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div>
                @if(($rekap->keterangan ?? '') === 'Terpenuhi')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Terpenuhi</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Belum Terpenuhi</span>
                @endif
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Status</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-primary-600">{{ $tahun }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Tahun Aktif</p>
            </div>
        </div>
    </div>

    {{-- Charts + Recent --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- JPL Kumulatif Chart --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Perkembangan JPL Kumulatif ({{ $tahun }})</h3>
            <canvas id="userJplChart" height="140"></canvas>
        </div>

        {{-- Pelatihan Terbaru --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Pelatihan Terbaru</h3>
                <a href="{{ route('sertifikat.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
            </div>
            @if($recentSertifikat->count() > 0)
            <div class="space-y-3">
                @foreach($recentSertifikat as $rs)
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5 w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-primary-600">{{ $rs->jpl }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $rs->nama_pelatihan }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ \Carbon\Carbon::parse($rs->tanggal)->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-6">
                <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada pelatihan</p>
                <a href="{{ route('sertifikat.create') }}" class="mt-2 inline-block text-xs text-primary-600 font-medium">Upload Sertifikat</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Distribusi Jenis Pelatihan --}}
    @if($jenisDist->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Distribusi Jenis Pelatihan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($jenisDist as $jd)
            @php $barWidth = $jpl > 0 ? round(($jd->total_jpl / $jpl) * 100) : 0; @endphp
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">{{ $jd->jenis_pelatihan }}</p>
                    <div class="mt-1.5 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-primary-500" style="width: {{ $barWidth }}%"></div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $jd->total_jpl }}</p>
                    <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $jd->total }}x</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('sertifikat.create') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-primary-100 transition-colors">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Upload Sertifikat</p>
        </a>
        <a href="{{ route('sertifikat.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-green-100 transition-colors">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Daftar Sertifikat</p>
        </a>
        <a href="{{ route('sertifikat.rekap') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-amber-100 transition-colors">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Rekap JPL</p>
        </a>
        <a href="{{ route('profile.riwayat') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-primary-300 hover:shadow-sm transition-all group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-blue-100 transition-colors">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Riwayat</p>
        </a>
    </div>

    {{-- ==================== STAFF INSIGHTS ==================== --}}
    @if($staffInsights)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Rekomendasi Pelatihan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Rekomendasi Pelatihan</h3>
            </div>
            @if(count($staffInsights['missingJenis']) > 0)
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Jenis pelatihan yang belum diikuti tahun {{ $tahun }}:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($staffInsights['missingJenis'] as $jenis)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $jenis }}
                    </span>
                    @endforeach
                </div>
                <p class="mt-3 text-[11px] text-gray-400 dark:text-gray-500">
                    Sudah mengikuti {{ count($staffInsights['completedJenis']) }} dari {{ count($staffInsights['allJenisAktif']) }} jenis pelatihan aktif
                </p>
            @else
                <div class="flex items-center gap-2 py-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-green-700">Semua jenis pelatihan sudah diikuti!</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ count($staffInsights['allJenisAktif']) }} jenis pelatihan aktif tahun {{ $tahun }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Progress Bulanan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Progress Bulanan</h3>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Aktivitas pelatihan per bulan ({{ $tahun }}):</p>
            @php
                $monthInitials = ['J','F','M','A','M','J','J','A','S','O','N','D'];
                $activeCount = count($staffInsights['activeMonths']);
            @endphp
            <div class="flex items-center justify-between gap-1">
                @for($m = 1; $m <= 12; $m++)
                    @php $isActive = in_array($m, $staffInsights['activeMonths']); @endphp
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center {{ $isActive ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600' }}">
                            @if($isActive)
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </div>
                        <span class="text-[10px] font-medium {{ $isActive ? 'text-green-700' : 'text-gray-400' }}">{{ $monthInitials[$m - 1] }}</span>
                    </div>
                @endfor
            </div>
            <div class="mt-4 flex items-center justify-between">
                <p class="text-[11px] text-gray-400 dark:text-gray-500">
                    {{ $activeCount }} dari 12 bulan memiliki aktivitas pelatihan
                </p>
                @if($activeCount > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $activeCount >= 6 ? 'bg-green-100 text-green-700' : ($activeCount >= 3 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                        {{ $activeCount >= 6 ? 'Aktif' : ($activeCount >= 3 ? 'Cukup' : 'Perlu Ditingkatkan') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ==================== ADMIN SECTION ==================== --}}
    @if($adminStats)
    <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-6 space-y-6">

        {{-- Admin Header --}}
        <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Panel Admin</p>
                    <h2 class="text-xl font-bold mt-1">Statistik Organisasi {{ $tahun }}</h2>
                    <p class="text-gray-400 text-sm mt-1">Monitoring capaian kompetensi seluruh ASN BKK Kelas I Tarakan</p>
                </div>
                <a href="{{ route('sertifikat.rekap') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Lihat Rekap Lengkap
                </a>
            </div>
        </div>

        {{-- Stats Cards Row 1 --}}
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold">{{ $adminStats['totalPegawai'] }}</p>
                <p class="text-blue-100 text-xs">Pegawai</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold">{{ $adminStats['totalSertifikatAll'] }}</p>
                <p class="text-purple-100 text-xs">Sertifikat</p>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold">{{ $adminStats['totalJplAll'] }}</p>
                <p class="text-amber-100 text-xs">Total JPL</p>
            </div>
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold">{{ $adminStats['avgJpl'] }}</p>
                <p class="text-cyan-100 text-xs">Rata-rata JPL</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold">{{ $adminStats['terpenuhi'] }}</p>
                <p class="text-green-100 text-xs">Terpenuhi</p>
            </div>
            <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-xl p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="mt-3 text-2xl font-bold">{{ $adminStats['belumTerpenuhi'] }}</p>
                <p class="text-red-100 text-xs">Belum Terpenuhi</p>
            </div>
        </div>

        {{-- Capaian Bar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Capaian Organisasi <span class="font-normal text-gray-400 dark:text-gray-500">{{ ($adminStats['dashPeriode'] ?? 'tahunan') !== 'tahunan' ? '- ' . $adminStats['dashPeriodeLabel'] : '' }}</span></h4>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $adminStats['persenTerpenuhi'] }}%</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-5 overflow-hidden">
                <div class="h-5 rounded-full bg-gradient-to-r from-green-400 via-green-500 to-emerald-500 transition-all duration-700 relative" style="width: {{ max(3, $adminStats['persenTerpenuhi']) }}%">
                </div>
            </div>
            <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>{{ $adminStats['terpenuhi'] }} pegawai terpenuhi</span>
                <span>{{ $adminStats['belumTerpenuhi'] }} belum terpenuhi</span>
            </div>
        </div>

        {{-- Periode Filter --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" action="{{ route('home') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter Periode:</span>
                @php $dp = $adminStats['dashPeriode'] ?? 'tahunan'; @endphp
                <select name="periode" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none">
                    <optgroup label="Periode">
                        <option value="tahunan" {{ $dp === 'tahunan' ? 'selected' : '' }}>Tahunan (Semua)</option>
                    </optgroup>
                    <optgroup label="Semester (Akumulatif)">
                        <option value="semester1" {{ $dp === 'semester1' ? 'selected' : '' }}>s.d. Semester I (Jan - Jun)</option>
                        <option value="semester2" {{ $dp === 'semester2' ? 'selected' : '' }}>s.d. Semester II (Jan - Des)</option>
                    </optgroup>
                    <optgroup label="Triwulan (Akumulatif)">
                        <option value="triwulan1" {{ $dp === 'triwulan1' ? 'selected' : '' }}>s.d. Triwulan I (Jan - Mar)</option>
                        <option value="triwulan2" {{ $dp === 'triwulan2' ? 'selected' : '' }}>s.d. Triwulan II (Jan - Jun)</option>
                        <option value="triwulan3" {{ $dp === 'triwulan3' ? 'selected' : '' }}>s.d. Triwulan III (Jan - Sep)</option>
                        <option value="triwulan4" {{ $dp === 'triwulan4' ? 'selected' : '' }}>s.d. Triwulan IV (Jan - Des)</option>
                    </optgroup>
                    <optgroup label="Bulanan (Akumulatif)">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $mi => $namaBln)
                            <option value="bulan{{ $mi + 1 }}" {{ $dp === 'bulan' . ($mi + 1) ? 'selected' : '' }}>s.d. {{ $namaBln }}</option>
                        @endforeach
                    </optgroup>
                </select>
                @if(($adminStats['dashPeriode'] ?? 'tahunan') !== 'tahunan')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700">{{ $adminStats['dashPeriodeLabel'] }}</span>
                @endif
            </form>
        </div>

        {{-- Charts + Donut --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Aktivitas Pelatihan per Bulan</h4>
                <canvas id="chartCombined" height="160"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Status Capaian <span class="font-normal text-gray-400 dark:text-gray-500">{{ ($adminStats['dashPeriode'] ?? 'tahunan') !== 'tahunan' ? '- ' . $adminStats['dashPeriodeLabel'] : '' }}</span></h4>
                <canvas id="chartDonut" height="160"></canvas>
                <div class="mt-4 flex justify-center gap-6 text-sm">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span> Terpenuhi</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400"></span> Belum</span>
                </div>
            </div>
        </div>

        {{-- Top & Bottom Performers --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Top 5 - JPL Tertinggi <span class="font-normal text-gray-400 dark:text-gray-500">{{ ($adminStats['dashPeriode'] ?? 'tahunan') !== 'tahunan' ? '- ' . $adminStats['dashPeriodeLabel'] : '' }}</span></h4>
                </div>
                <div class="space-y-3">
                    @foreach($adminStats['topPerformers'] as $i => $tp)
                    @php $tpP = min(100, round(($tp->jumlah_jpl / 20) * 100)); @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                            {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-200 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500')) }}">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $tp->pegawai->nama ?? '-' }}</p>
                                <span class="text-sm font-bold flex-shrink-0 ml-2 {{ $tp->jumlah_jpl >= 20 ? 'text-green-600' : 'text-gray-700 dark:text-gray-300' }}">{{ $tp->jumlah_jpl }}</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $tpP >= 100 ? 'bg-green-500' : 'bg-primary-500' }}" style="width: {{ $tpP }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Perlu Perhatian - JPL Terendah <span class="font-normal text-gray-400 dark:text-gray-500">{{ ($adminStats['dashPeriode'] ?? 'tahunan') !== 'tahunan' ? '- ' . $adminStats['dashPeriodeLabel'] : '' }}</span></h4>
                </div>
                <div class="space-y-3">
                    @foreach($adminStats['bottomPerformers'] as $i => $bp)
                    @php $bpP = min(100, round(($bp->jumlah_jpl / 20) * 100)); @endphp
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $bp->pegawai->nama ?? '-' }}</p>
                                <span class="text-sm font-bold flex-shrink-0 ml-2 {{ $bp->jumlah_jpl >= 20 ? 'text-green-600' : 'text-red-600' }}">{{ $bp->jumlah_jpl }}</span>
                            </div>
                            <div class="mt-1 w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $bpP >= 100 ? 'bg-green-500' : 'bg-red-400' }}" style="width: {{ max(3, $bpP) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Jenis Pelatihan + Recent Uploads --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Jenis Pelatihan Terpopuler --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Jenis Pelatihan Terpopuler</h4>
                @if($adminStats['jenisDistOrg']->count() > 0)
                <div class="space-y-3">
                    @php $maxTotal = $adminStats['jenisDistOrg']->max('total'); @endphp
                    @foreach($adminStats['jenisDistOrg'] as $jd)
                    @php $barW = $maxTotal > 0 ? round(($jd->total / $maxTotal) * 100) : 0; @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">{{ $jd->jenis_pelatihan }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-2">{{ $jd->total }}x &middot; {{ $jd->total_jpl }} JPL</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full bg-gradient-to-r from-primary-400 to-primary-600" style="width: {{ $barW }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Belum ada data</p>
                @endif
            </div>

            {{-- Recent Uploads --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Upload Terbaru</h4>
                    <a href="{{ route('sertifikat.index') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
                </div>
                @if($adminStats['recentUploads']->count() > 0)
                <div class="space-y-3">
                    @foreach($adminStats['recentUploads'] as $ru)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($ru->pegawai->nama ?? '?', 0, 2)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ $ru->nama_pelatihan }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $ru->pegawai->nama ?? '-' }} &middot; {{ $ru->jpl }} JPL</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Belum ada upload</p>
                @endif
            </div>
        </div>

        {{-- Admin Quick Links --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('pegawai.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-gray-300 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Kelola Pegawai</p>
            </a>
            <a href="{{ route('sertifikat.download') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-gray-300 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-green-100 transition-colors">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Download Sertifikat</p>
            </a>
            <a href="{{ route('home.comparison') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-gray-300 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-purple-100 transition-colors">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Perbandingan Tahun</p>
            </a>
            <a href="{{ route('activity.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 text-center hover:border-gray-300 hover:shadow-sm transition-all group">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2 group-hover:bg-amber-100 transition-colors">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Log Aktivitas</p>
            </a>
        </div>
    </div>
    @endif
</div>

{{-- Charts JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User JPL Chart
    new Chart(document.getElementById('userJplChart'), {
        type: 'line',
        data: {
            labels: @json($userChartLabels),
            datasets: [{
                label: 'JPL Kumulatif',
                data: @json($userChartJpl),
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#4F46E5',
            }, {
                label: 'Target',
                data: Array(12).fill(20),
                borderColor: '#EF4444',
                borderDash: [6, 4],
                borderWidth: 1.5,
                pointRadius: 0,
                fill: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                datalabels: {
                    display: function(ctx) { return ctx.datasetIndex === 0 && ctx.dataset.data[ctx.dataIndex] > 0; },
                    anchor: 'end', align: 'top', offset: 2,
                    font: { size: 10, weight: 'bold' },
                    color: '#4F46E5',
                }
            },
            scales: { y: { beginAtZero: true, max: Math.max(25, {{ $jpl }} + 5) } }
        },
        plugins: [ChartDataLabels]
    });

    @if($adminStats)
    // Combined bar + line chart
    new Chart(document.getElementById('chartCombined'), {
        type: 'bar',
        data: {
            labels: @json($adminStats['chartLabels']),
            datasets: [{
                type: 'bar',
                label: 'Jumlah Pelatihan',
                data: @json($adminStats['chartJumlah']),
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                borderRadius: 6,
                yAxisID: 'y',
                order: 2,
            }, {
                type: 'line',
                label: 'Total JPL',
                data: @json($adminStats['chartJpl']),
                borderColor: '#F59E0B',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#F59E0B',
                yAxisID: 'y1',
                order: 1,
            }]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                datalabels: {
                    display: function(ctx) { return ctx.dataset.data[ctx.dataIndex] > 0; },
                    anchor: 'end', align: 'top', offset: 2,
                    font: { size: 10, weight: 'bold' },
                    color: function(ctx) { return ctx.datasetIndex === 0 ? '#4338CA' : '#D97706'; },
                }
            },
            scales: {
                y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Pelatihan', font: { size: 10 } }, ticks: { stepSize: 1 } },
                y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'JPL', font: { size: 10 } }, grid: { drawOnChartArea: false } }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Donut chart
    var donutTotal = {{ $adminStats['terpenuhi'] }} + {{ $adminStats['belumTerpenuhi'] }};
    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: ['Terpenuhi', 'Belum Terpenuhi'],
            datasets: [{
                data: [{{ $adminStats['terpenuhi'] }}, {{ $adminStats['belumTerpenuhi'] }}],
                backgroundColor: ['#22C55E', '#F87171'],
                borderWidth: 0,
                spacing: 2,
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(ctx) { return ctx.label + ': ' + ctx.raw + ' pegawai'; } } },
                datalabels: {
                    display: function(ctx) { return ctx.dataset.data[ctx.dataIndex] > 0; },
                    color: '#fff',
                    font: { size: 12, weight: 'bold' },
                    formatter: function(value) { return value; },
                }
            }
        },
        plugins: [ChartDataLabels, {
            id: 'centerText',
            beforeDraw: function(chart) {
                let ctx = chart.ctx;
                let centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                let centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                ctx.save();
                ctx.font = 'bold 24px sans-serif';
                ctx.fillStyle = '#111827';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('{{ $adminStats["persenTerpenuhi"] }}%', centerX, centerY - 8);
                ctx.font = '11px sans-serif';
                ctx.fillStyle = '#9CA3AF';
                ctx.fillText('capaian', centerX, centerY + 14);
                ctx.restore();
            }
        }]
    });
    @endif
});
</script>
@endsection
