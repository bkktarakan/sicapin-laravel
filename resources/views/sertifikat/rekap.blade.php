@extends('layouts.app')

@section('title', 'Rekap Sertifikat')

@section('breadcrumb')
<span class="text-gray-900 dark:text-white font-medium">Rekap JPL</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Rekap Sertifikat</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitoring capaian JPL pegawai tahun {{ $tahun }} &mdash; {{ $periodeLabel }}</p>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="flex flex-wrap gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Rekap
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10 overflow-hidden"
                     x-transition>
                    <a href="{{ route('sertifikat.cetak', ['periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Rekap (.xls)
                    </a>
                    <a href="{{ route('sertifikat.cetakPdf', ['periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Rekap (.pdf)
                    </a>
                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                    <a href="{{ route('sertifikat.cetakRincian', ['periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Rincian (.xls)
                    </a>
                    <a href="{{ route('sertifikat.cetakRincianPdf', ['periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Rincian (.pdf)
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Summary Cards --}}
    @php
        $totalPegawai = $rekap->count();
        $totalTerpenuhi = $rekap->where('keterangan', 'Terpenuhi')->count();
        $totalBelum = $totalPegawai - $totalTerpenuhi;
        $avgJpl = $totalPegawai > 0 ? round($rekap->avg('jumlah_jpl'), 1) : 0;
        $persenTerpenuhi = $totalPegawai > 0 ? round(($totalTerpenuhi / $totalPegawai) * 100) : 0;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pegawai</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPegawai }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rata-rata JPL</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $avgJpl }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-200 bg-green-50/50 p-4">
            <p class="text-xs font-medium text-green-600 uppercase tracking-wider">Terpenuhi</p>
            <p class="mt-1.5 text-2xl font-bold text-green-700">{{ $totalTerpenuhi }} <span class="text-sm font-normal text-green-500">pegawai</span></p>
        </div>
        <div class="bg-white rounded-xl border border-red-200 bg-red-50/50 p-4">
            <p class="text-xs font-medium text-red-600 uppercase tracking-wider">Belum Terpenuhi</p>
            <p class="mt-1.5 text-2xl font-bold text-red-700">{{ $totalBelum }} <span class="text-sm font-normal text-red-500">pegawai</span></p>
        </div>
    </div>

    {{-- Progress Overview --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Capaian Keseluruhan</span>
            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $persenTerpenuhi }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500 {{ $persenTerpenuhi >= 80 ? 'bg-green-500' : ($persenTerpenuhi >= 50 ? 'bg-primary-500' : 'bg-amber-500') }}" style="width: {{ $persenTerpenuhi }}%"></div>
        </div>
        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ $totalTerpenuhi }} dari {{ $totalPegawai }} pegawai telah memenuhi target 20 JPL</p>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('sertifikat.rekap') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP atau nama pegawai..."
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
            </div>
            <select name="periode" class="px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none sm:w-56">
                <optgroup label="Periode">
                    <option value="tahunan" {{ $periode === 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </optgroup>
                <optgroup label="Triwulan">
                    <option value="triwulan1" {{ $periode === 'triwulan1' ? 'selected' : '' }}>Triwulan I (Jan - Mar)</option>
                    <option value="triwulan2" {{ $periode === 'triwulan2' ? 'selected' : '' }}>Triwulan II (Apr - Jun)</option>
                    <option value="triwulan3" {{ $periode === 'triwulan3' ? 'selected' : '' }}>Triwulan III (Jul - Sep)</option>
                    <option value="triwulan4" {{ $periode === 'triwulan4' ? 'selected' : '' }}>Triwulan IV (Okt - Des)</option>
                </optgroup>
                <optgroup label="Semester">
                    <option value="semester1" {{ $periode === 'semester1' ? 'selected' : '' }}>Semester I (Jan - Jun)</option>
                    <option value="semester2" {{ $periode === 'semester2' ? 'selected' : '' }}>Semester II (Jul - Des)</option>
                </optgroup>
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none sm:w-44">
                <option value="">Semua Status</option>
                <option value="Terpenuhi" {{ request('status') === 'Terpenuhi' ? 'selected' : '' }}>Terpenuhi</option>
                <option value="Belum Terpenuhi" {{ request('status') === 'Belum Terpenuhi' ? 'selected' : '' }}>Belum Terpenuhi</option>
            </select>
            <select name="jabatan" class="px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none sm:w-44">
                <option value="">Semua Jabatan</option>
                @php $jabatanList = \App\Models\Pegawai::distinct()->whereNotNull('jabatan')->pluck('jabatan')->sort(); @endphp
                @foreach($jabatanList as $jbt)
                    <option value="{{ $jbt }}" {{ request('jabatan') === $jbt ? 'selected' : '' }}>{{ $jbt }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Cari
            </button>
            @if(request()->hasAny(['search', 'status', 'periode', 'jabatan']))
                <a href="{{ route('sertifikat.rekap') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-12">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama Pegawai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Jabatan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-24">JPL</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-40">Progress</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-36">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($rekap as $index => $r)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-mono text-xs whitespace-nowrap">{{ $r->pegawai->nip ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $r->pegawai->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 lg:hidden">{{ $r->pegawai->jabatan ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs hidden lg:table-cell">{{ $r->pegawai->jabatan ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-base font-bold {{ $r->jumlah_jpl >= 20 ? 'text-green-600' : 'text-gray-900 dark:text-white' }}">{{ $r->jumlah_jpl }}</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">/20</span>
                        </td>
                        <td class="px-4 py-3">
                            @php $p = min(100, round(($r->jumlah_jpl / 20) * 100)); @endphp
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300 {{ $p >= 100 ? 'bg-green-500' : ($p >= 50 ? 'bg-primary-500' : 'bg-amber-500') }}" style="width: {{ $p }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-right">{{ $p }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($r->keterangan === 'Terpenuhi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Terpenuhi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Belum
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data rekap untuk tahun {{ $tahun }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Total footer --}}
    @if($rekap->count() > 0)
    <div class="text-xs text-gray-400 dark:text-gray-500 text-right">
        Menampilkan {{ $rekap->count() }} pegawai
    </div>
    @endif
</div>
@endsection
