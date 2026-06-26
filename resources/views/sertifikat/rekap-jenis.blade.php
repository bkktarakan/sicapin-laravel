@extends('layouts.app')

@section('title', 'Rekap Per Jenis Pelatihan')

@section('breadcrumb')
<a href="{{ route('sertifikat.rekap') }}" class="hover:text-primary-600">Rekap</a>
<svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 dark:text-white font-medium">Per Jenis Pelatihan</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Rekap Per Jenis Pelatihan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tahun {{ $tahun }} &mdash; {{ $periodeLabel }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak @click.outside="open = false"
                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10 overflow-hidden" x-transition>
                    <a href="{{ route('sertifikat.cetakJenis', ['periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export (.xls)
                    </a>
                    <a href="{{ route('sertifikat.cetakJenisPdf', ['periode' => $periode]) }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Export (.pdf)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('sertifikat.rekapJenis') }}" class="flex flex-col sm:flex-row gap-3">
            <select name="periode" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none sm:max-w-xs">
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
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            @if(request('periode'))
                <a href="{{ route('sertifikat.rekapJenis') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Summary Cards --}}
    @php
        $totalSertifikat = collect($data)->sum('jumlah_sertifikat');
        $totalJpl = collect($data)->sum('total_jpl');
        $totalPegawai = collect($data)->sum('jumlah_pegawai');
        $jenisAktif = collect($data)->where('jumlah_sertifikat', '>', 0)->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Sertifikat</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSertifikat }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total JPL</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJpl }}</p>
        </div>
        <div class="bg-white rounded-xl border border-blue-200 bg-blue-50/50 p-4">
            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Jenis Aktif</p>
            <p class="mt-1.5 text-2xl font-bold text-blue-700">{{ $jenisAktif }} <span class="text-sm font-normal text-blue-500">/ {{ count($jenisPelatihan) }}</span></p>
        </div>
        <div class="bg-white rounded-xl border border-purple-200 bg-purple-50/50 p-4">
            <p class="text-xs font-medium text-purple-600 uppercase tracking-wider">Partisipasi Pegawai</p>
            <p class="mt-1.5 text-2xl font-bold text-purple-700">{{ $totalPegawai }}</p>
        </div>
    </div>

    {{-- Card List per Jenis --}}
    <div class="space-y-4">
        @forelse($data as $index => $row)
        @if($row['jumlah_sertifikat'] > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ open: false }">
            {{-- Jenis Header --}}
            <button @click="open = !open" class="w-full px-5 py-4 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-left">
                {{-- Icon --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-sm font-bold text-primary-700">{{ $index + 1 }}</span>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $row['jenis'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $row['jumlah_pegawai'] }} pegawai</p>
                </div>

                {{-- Stats --}}
                <div class="hidden sm:flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-lg font-bold text-primary-700">{{ $row['total_jpl'] }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">JPL</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $row['jumlah_sertifikat'] }}</p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">Sertifikat</p>
                    </div>
                    @if($totalJpl > 0)
                    <div class="w-16">
                        @php $persen = round(($row['total_jpl'] / $totalJpl) * 100, 1); @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-primary-500" style="width: {{ $persen }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 text-center mt-0.5">{{ $persen }}%</p>
                    </div>
                    @endif
                </div>

                {{-- Chevron --}}
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform flex-shrink-0" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            {{-- Mobile stats --}}
            <div class="sm:hidden px-5 pb-3 -mt-1 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span>{{ $row['total_jpl'] }} JPL</span>
                <span>&middot;</span>
                <span>{{ $row['jumlah_sertifikat'] }} sertifikat</span>
            </div>

            {{-- Expanded: Sertifikat List --}}
            <div x-show="open" x-cloak x-collapse>
                <div class="border-t border-gray-100 dark:border-gray-700">
                    @foreach($row['sertifikat'] as $s)
                    <div class="px-5 py-3 flex items-start gap-3 border-b border-gray-50 dark:border-gray-700 last:border-b-0 hover:bg-gray-50/50 dark:hover:bg-gray-700/50" x-data="{ editOpen: false }">
                        {{-- Dot --}}
                        <div class="flex-shrink-0 mt-1.5 w-2 h-2 rounded-full bg-primary-300"></div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $s->nama_pelatihan }}</p>
                            <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $s->pegawai->nama ?? '-' }}</span>
                                <span>&middot;</span>
                                <span>{{ $s->penyelenggara ?? '-' }}</span>
                                <span>&middot;</span>
                                <span>{{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>

                        {{-- JPL --}}
                        <span class="flex-shrink-0 text-sm font-bold text-primary-700">{{ $s->jpl }} JPL</span>

                        {{-- Actions --}}
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
                            <button @click="editOpen = true"
                                    class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('sertifikat.destroy', $s->id) }}" onsubmit="return confirm('Hapus sertifikat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>

                        {{-- Edit Modal --}}
                        <div x-show="editOpen" x-cloak
                             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                             @click.self="editOpen = false"
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto" @click.stop
                                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                                <div class="flex items-center justify-between mb-5">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Sertifikat</h3>
                                    <button @click="editOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('sertifikat.update', $s->id) }}" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pegawai_id" value="{{ $s->pegawai_id }}">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pegawai</label>
                                        <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-lg">{{ $s->pegawai->nama ?? '-' }} ({{ $s->pegawai->nip ?? '-' }})</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Pelatihan</label>
                                        <input type="text" name="nama_pelatihan" value="{{ $s->nama_pelatihan }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Penyelenggara</label>
                                        <input type="text" name="penyelenggara" value="{{ $s->penyelenggara }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mulai</label>
                                            <input type="date" name="tanggal" value="{{ $s->tanggal ? $s->tanggal->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selesai</label>
                                            <input type="date" name="tanggal_akhir" value="{{ $s->tanggal_akhir ? $s->tanggal_akhir->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">JPL</label>
                                            <input type="number" name="jpl" value="{{ $s->jpl }}" min="1" max="200" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Pelatihan</label>
                                        <select name="jenis_pelatihan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none bg-white dark:bg-gray-700">
                                            @foreach($jenisPelatihan as $jp)
                                                <option value="{{ $jp }}" {{ $s->jenis_pelatihan === $jp ? 'selected' : '' }}>{{ $jp }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                                        <input type="text" name="keterangan" value="{{ $s->keterangan }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ganti File PDF</label>
                                        @if($s->pdf)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">File saat ini: <a href="{{ route('sertifikat.pdf', $s->id) }}" target="_blank" class="text-primary-600 hover:underline">{{ $s->pdf }}</a></p>
                                        @endif
                                        <input type="file" name="pdf" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Kosongkan jika tidak ingin mengganti. Maks 2MB.</p>
                                    </div>
                                    <div class="flex justify-end gap-3 pt-2">
                                        <button type="button" @click="editOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Batal</button>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        {{-- Jenis tanpa data --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-3 flex items-center gap-4 opacity-40">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ $index + 1 }}</span>
            </div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $row['jenis'] }}</p>
            <span class="ml-auto text-xs text-gray-300 dark:text-gray-600">Belum ada data</span>
        </div>
        @endif
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data untuk periode ini</p>
        </div>
        @endforelse
    </div>

    @if($totalSertifikat > 0)
    <div class="text-xs text-gray-400 dark:text-gray-500 text-right">
        Menampilkan {{ count($jenisPelatihan) }} jenis pelatihan &middot; {{ $totalSertifikat }} sertifikat &middot; {{ $totalJpl }} JPL
    </div>
    @endif
</div>

{{-- PDF Preview Modal --}}
<div x-data="{ showPreview: false, previewUrl: '' }"
     @open-preview.window="showPreview = true; previewUrl = $event.detail.url">
    <div x-show="showPreview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showPreview = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl mx-4 h-[85vh] flex flex-col" @click.stop>
            <div class="flex items-center justify-between px-5 py-3 border-b dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Preview Sertifikat</h3>
                <button @click="showPreview = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
