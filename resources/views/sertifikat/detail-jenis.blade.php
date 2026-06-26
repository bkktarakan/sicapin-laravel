@extends('layouts.app')

@section('title', 'Detail Jenis Pelatihan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('sertifikat.rekapJenis', ['periode' => $periode]) }}" class="text-primary-600 hover:text-primary-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-xl font-bold text-gray-900">{{ $jenis }}</h1>
            </div>
            <p class="mt-1 text-sm text-gray-500">Daftar pegawai &mdash; tahun {{ $tahun }} &mdash; {{ $periodeLabel }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 overflow-hidden"
                     x-transition>
                    <a href="{{ route('sertifikat.cetakDetailJenis', ['jenis' => $jenis, 'periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export (.xls)
                    </a>
                    <a href="{{ route('sertifikat.cetakDetailJenis', ['jenis' => $jenis, 'periode' => $periode, 'format' => 'pdf']) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Export (.pdf)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('sertifikat.detailJenis') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIP atau nama pegawai..."
                       class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <select name="periode" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none sm:w-56">
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
            <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">Cari</button>
            @if(request()->hasAny(['search', 'periode']))
                <a href="{{ route('sertifikat.detailJenis', ['jenis' => $jenis]) }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Pegawai List --}}
    @php
        $totalPegawai = $grouped->count();
        $totalSertifikat = $grouped->sum('jumlah_pelatihan');
        $totalJpl = $grouped->sum('total_jpl');
    @endphp
    <div class="space-y-4">
        @forelse($grouped as $index => $row)
        @php
            $pegawai = $row['pegawai'];
            $sertifikats = $row['sertifikat'];
            $jplTotal = $row['total_jpl'];
            $jumlah = $row['jumlah_pelatihan'];
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" x-data="{ open: false }">
            {{-- Pegawai Header --}}
            <button @click="open = !open" class="w-full px-5 py-4 flex items-center gap-4 hover:bg-gray-50 transition-colors text-left">
                {{-- Avatar --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-sm font-bold text-primary-700">{{ strtoupper(substr($pegawai->nama, 0, 1)) }}</span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $pegawai->nama }}</p>
                    <p class="text-xs text-gray-500">{{ $pegawai->nip }} &middot; {{ $pegawai->jabatan ?? '-' }}</p>
                </div>

                {{-- Stats --}}
                <div class="hidden sm:flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-lg font-bold text-primary-700">{{ $jplTotal }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">JPL</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ $jumlah }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">Sertifikat</p>
                    </div>
                </div>

                {{-- Chevron --}}
                <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            {{-- Mobile stats --}}
            <div class="sm:hidden px-5 pb-3 -mt-1 flex items-center gap-3 text-xs text-gray-500">
                <span>{{ $jplTotal }} JPL</span>
                <span>&middot;</span>
                <span>{{ $jumlah }} sertifikat</span>
            </div>

            {{-- Expanded: Sertifikat List --}}
            <div x-show="open" x-cloak x-collapse>
                <div class="border-t border-gray-100">
                    @foreach($sertifikats as $s)
                    <div class="px-5 py-3 flex items-start gap-3 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50">
                        {{-- Dot --}}
                        <div class="flex-shrink-0 mt-1.5 w-2 h-2 rounded-full bg-primary-300"></div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $s->nama_pelatihan }}</p>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                <span>{{ $s->penyelenggara ?? '-' }}</span>
                                <span>&middot;</span>
                                <span>{{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}@if($s->tanggal_akhir && $s->tanggal_akhir->format('Y-m-d') !== $s->tanggal->format('Y-m-d')) - {{ $s->tanggal_akhir->format('d/m/Y') }}@endif</span>
                            </div>
                        </div>

                        {{-- JPL --}}
                        <span class="flex-shrink-0 text-sm font-bold text-primary-700">{{ $s->jpl }} JPL</span>

                        {{-- Preview PDF --}}
                        <div class="flex-shrink-0 flex items-center gap-1">
                            @if($s->pdf)
                            <button @click="$dispatch('open-preview', { url: '{{ route('sertifikat.preview', $s->id) }}' })"
                                    class="p-1.5 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-colors" title="Preview PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-sm text-gray-500">Belum ada pegawai yang mengikuti pelatihan ini pada periode yang dipilih</p>
        </div>
        @endforelse
    </div>

    @if($totalPegawai > 0)
    <div class="text-xs text-gray-400 text-right">
        Menampilkan {{ $totalPegawai }} pegawai &middot; {{ $totalSertifikat }} sertifikat &middot; {{ $totalJpl }} JPL
    </div>
    @endif
</div>

{{-- PDF Preview Modal --}}
<div x-data="{ showPreview: false, previewUrl: '' }"
     @open-preview.window="showPreview = true; previewUrl = $event.detail.url">
    <div x-show="showPreview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showPreview = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 h-[85vh] flex flex-col" @click.stop>
            <div class="flex items-center justify-between px-5 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-900">Preview Sertifikat</h3>
                <button @click="showPreview = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 p-1">
                <iframe :src="previewUrl" class="w-full h-full rounded-lg border-0"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
