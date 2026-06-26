@extends('layouts.app')

@section('title', 'Download Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Download Sertifikat</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unduh file sertifikat pegawai</p>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5" x-data="{ showAll: {{ in_array('all', request('pegawai_id', [])) ? 'true' : 'false' }} }">
        <form method="GET" action="{{ route('sertifikat.download') }}" class="space-y-4">

            {{-- Quick Select --}}
            @if(auth()->user()->isAdmin())
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="showAll" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Semua Pegawai</span>
                </label>
            </div>
            @endif

            <div x-show="showAll" x-cloak>
                <input type="hidden" name="pegawai_id[]" value="all" :disabled="!showAll">
            </div>

            {{-- Multi Pegawai Select --}}
            <div x-show="!showAll">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Pegawai</label>
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto p-2 space-y-1">
                    @foreach($pegawaiList as $p)
                    <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                        <input type="checkbox" name="pegawai_id[]" value="{{ $p->id }}"
                               {{ in_array($p->id, $selectedPegawaiIds) ? 'checked' : '' }}
                               :disabled="showAll"
                               class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $p->nama }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500 ml-auto">{{ $p->nip }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bulan <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional)</span></label>
                    <select name="bulan" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Semua Bulan</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $namaBulan)
                            <option value="{{ $i + 1 }}" {{ request('bulan') == ($i + 1) ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tahun <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional)</span></label>
                    <select name="tahun" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Tampilkan
                </button>
                @if(request()->hasAny(['pegawai_id', 'bulan', 'tahun']))
                    <a href="{{ route('sertifikat.download') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if($results !== null)
    @php
        $grouped = $results->groupBy('pegawai_id');
        $allIds = $results->pluck('id')->toArray();
    @endphp
    <div x-data="{ selectedIds: [], selectAll: false }"
         x-init="$watch('selectAll', val => { selectedIds = val ? {{ json_encode($allIds) }} : [] })">

        {{-- Result Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    @if(count($selectedPegawaiIds) === 1)
                        {{ $selectedPegawaiNames->first() }}
                    @else
                        {{ count($selectedPegawaiIds) }} pegawai dipilih
                    @endif
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $results->count() }} sertifikat ditemukan
                    @if(request('tahun')) &middot; Tahun {{ request('tahun') }} @endif
                </p>
            </div>

            @if($results->count() > 0)
            <div class="flex items-center gap-2">
                <template x-if="selectedIds.length > 0">
                    <a :href="'{{ route('sertifikat.downloadZip') }}?pegawai_id={{ implode(',', $selectedPegawaiIds) }}&tahun={{ request('tahun') }}&bulan={{ request('bulan') }}&ids=' + selectedIds.join(',')"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download (<span x-text="selectedIds.length"></span>)
                    </a>
                </template>

                <a href="{{ route('sertifikat.downloadZip', ['pegawai_id' => implode(',', $selectedPegawaiIds), 'tahun' => request('tahun'), 'bulan' => request('bulan')]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Semua (.zip)
                </a>
            </div>
            @endif
        </div>

        {{-- Select All --}}
        @if($results->count() > 0)
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                <input type="checkbox" x-model="selectAll" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <label for="selectAll" class="text-xs font-medium text-gray-500 dark:text-gray-400">Pilih Semua</label>
            </div>

            @foreach($grouped as $pegawaiId => $serts)
            @php $pegawaiNama = $selectedPegawaiNames[$pegawaiId] ?? '-'; @endphp

            {{-- Pegawai Header --}}
            @if(count($selectedPegawaiIds) > 1)
            <div class="px-5 py-2.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ $pegawaiNama }}</p>
            </div>
            @endif

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($serts as $s)
                <div class="px-5 py-3 flex items-start gap-3 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                    <input type="checkbox" value="{{ $s->id }}" x-model.number="selectedIds"
                           class="mt-1 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $s->nama_pelatihan }}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                            @if(count($selectedPegawaiIds) === 1)
                                <span>{{ $s->penyelenggara }}</span>
                                <span>&middot;</span>
                            @endif
                            <span>{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-[10px] font-medium text-gray-600 dark:text-gray-300">{{ $s->jenis_pelatihan }}</span>
                        </div>
                    </div>
                    <span class="flex-shrink-0 text-sm font-bold text-primary-700">{{ $s->jpl }} JPL</span>
                    @if($s->pdf)
                        <a href="{{ route('sertifikat.pdf', $s->id) }}" target="_blank"
                           class="flex-shrink-0 p-1.5 text-green-600 hover:text-green-800 rounded-lg hover:bg-green-50 transition-colors" title="Lihat PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </a>
                    @else
                        <span class="flex-shrink-0 p-1.5 text-gray-300" title="File tidak tersedia">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </span>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @else
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-10 text-center">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada sertifikat ditemukan untuk filter ini</p>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
