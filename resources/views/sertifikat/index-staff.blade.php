@extends('layouts.app')

@section('title', 'Daftar Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Sertifikat Saya</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $sertifikat->total() }} sertifikat &middot; Tahun {{ $tahun }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('sertifikat.cetakPribadi') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Cetak Rekap
            </a>
            <a href="{{ route('sertifikat.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Baru
            </a>
        </div>
    </div>

    {{-- Live Search & Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4" x-data="staffLiveSearch()" @click.outside="showResults = false">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="if(results.length) showResults = true"
                       placeholder="Cari pelatihan, penyelenggara, jenis, tanggal, JPL..."
                       class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <div x-show="query.length > 0" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button @click="query = ''; results = []; showResults = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form method="GET" action="{{ route('sertifikat.index') }}" class="flex gap-2">
                <input type="hidden" name="search" :value="query">
                <select name="jenis" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none sm:w-52">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisPelatihan as $j)
                        <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">Filter</button>
                @if(request()->hasAny(['search', 'jenis']))
                    <a href="{{ route('sertifikat.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition-colors text-center">Reset</a>
                @endif
            </form>
        </div>

        {{-- Live Results --}}
        <div x-show="showResults && query.length >= 2" x-cloak class="mt-3 border-t border-gray-100 pt-3">
            <template x-if="loading">
                <div class="py-4 text-center text-sm text-gray-400">Mencari...</div>
            </template>
            <template x-if="!loading && results.length === 0 && query.length >= 2">
                <div class="py-4 text-center text-sm text-gray-400">Tidak ada hasil untuk "<span x-text="query"></span>"</div>
            </template>
            <template x-if="!loading && results.length > 0">
                <div>
                    <p class="text-xs text-gray-400 mb-2"><span x-text="results.length"></span> hasil ditemukan</p>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        <template x-for="r in results" :key="r.id">
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors border border-gray-100">
                                <div class="flex-shrink-0 mt-0.5 w-2 h-2 rounded-full"
                                     :class="r.status === 'approved' ? 'bg-green-400' : (r.status === 'rejected' ? 'bg-red-400' : 'bg-amber-400')"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="r.nama_pelatihan"></p>
                                    <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">
                                        <span x-text="r.penyelenggara"></span>
                                        <span>&middot;</span>
                                        <span x-text="r.tanggal"></span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-[10px] font-medium text-gray-600" x-text="r.jenis_pelatihan"></span>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 text-sm font-bold text-primary-700" x-text="r.jpl + ' JPL'"></span>
                                <template x-if="r.has_pdf">
                                    <button @click="$dispatch('open-preview', { url: r.preview_url })"
                                            class="flex-shrink-0 p-1 text-gray-400 hover:text-primary-600 rounded hover:bg-primary-50 transition-colors" title="Preview">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
    function staffLiveSearch() {
        return {
            query: '{{ request("search", "") }}',
            results: [],
            loading: false,
            showResults: false,
            search() {
                if (this.query.length < 2) { this.results = []; this.showResults = false; return; }
                this.loading = true;
                this.showResults = true;
                fetch('{{ route("sertifikat.liveSearch") }}?q=' + encodeURIComponent(this.query))
                    .then(r => r.json())
                    .then(data => { this.results = data; this.loading = false; })
                    .catch(() => { this.loading = false; });
            }
        }
    }
    </script>

    {{-- Card List --}}
    <div class="space-y-3">
        @forelse($sertifikat as $s)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-gray-300 hover:shadow-sm transition-all p-4 sm:p-5" x-data="{ editOpen: false }">

            {{-- Top: Nama Pelatihan + JPL --}}
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h3 class="text-sm font-semibold text-gray-900 leading-snug">{{ $s->nama_pelatihan }}</h3>
                    <p class="mt-1 text-xs text-gray-500">{{ $s->penyelenggara }}</p>
                </div>
                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-bold bg-primary-50 text-primary-700">
                    {{ $s->jpl }} JPL
                </span>
            </div>

            {{-- Middle: Info --}}
            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}
                    @if($s->tanggal_akhir && $s->tanggal_akhir != $s->tanggal)
                        - {{ \Carbon\Carbon::parse($s->tanggal_akhir)->format('d/m/Y') }}
                    @endif
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-[11px] font-medium text-gray-600">{{ $s->jenis_pelatihan }}</span>
                @if($s->keterangan)
                    <span class="text-gray-400">{{ $s->keterangan }}</span>
                @endif
            </div>

            {{-- Status Timeline --}}
            <div class="mt-3 flex items-start gap-0">
                {{-- Step 1: Diupload (always green) --}}
                <div class="flex items-center gap-0">
                    <div class="flex flex-col items-center">
                        <div class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100 ring-2 ring-green-500">
                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <div class="ml-1.5 mr-1">
                        <span class="text-[11px] font-semibold text-green-700">Diupload</span>
                        <span class="text-[10px] text-gray-400 ml-1">{{ $s->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Connecting line --}}
                <div class="flex items-center self-center">
                    <div class="w-8 h-0.5 {{ $s->status === 'pending' ? 'bg-amber-300' : ($s->status === 'approved' ? 'bg-green-400' : 'bg-red-300') }}"></div>
                </div>

                {{-- Step 2: Status-dependent --}}
                <div class="flex items-center gap-0">
                    <div class="flex flex-col items-center">
                        @if($s->status === 'pending')
                            <div class="flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 ring-2 ring-amber-400">
                                <svg class="w-3 h-3 text-amber-600 animate-spin" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9" stroke-dasharray="4 2" fill="none"/></svg>
                            </div>
                        @elseif($s->status === 'approved')
                            <div class="flex items-center justify-center w-5 h-5 rounded-full bg-green-100 ring-2 ring-green-500">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        @elseif($s->status === 'rejected')
                            <div class="flex items-center justify-center w-5 h-5 rounded-full bg-red-100 ring-2 ring-red-500">
                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-1.5">
                        @if($s->status === 'pending')
                            <span class="text-[11px] font-semibold text-amber-700">Menunggu Verifikasi</span>
                        @elseif($s->status === 'approved')
                            <span class="text-[11px] font-semibold text-green-700">Disetujui</span>
                            @if($s->verified_at)
                                <span class="text-[10px] text-gray-400 ml-1">{{ \Carbon\Carbon::parse($s->verified_at)->format('d/m/Y') }}</span>
                            @endif
                        @elseif($s->status === 'rejected')
                            <span class="text-[11px] font-semibold text-red-700">Ditolak</span>
                            @if($s->verified_at)
                                <span class="text-[10px] text-gray-400 ml-1">{{ \Carbon\Carbon::parse($s->verified_at)->format('d/m/Y') }}</span>
                            @endif
                            @if($s->catatan_verifikasi)
                                <p class="text-[10px] text-red-600 mt-0.5 leading-tight max-w-xs">{{ $s->catatan_verifikasi }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bottom: Actions --}}
            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($s->pdf)
                        <a href="{{ route('sertifikat.pdf', $s->id) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Lihat PDF
                        </a>
                        <span class="text-gray-300">|</span>
                        <button @click="$dispatch('open-preview', { url: '{{ route('sertifikat.preview', $s->id) }}' })"
                                class="inline-flex items-center gap-1 text-xs font-medium text-green-600 hover:text-green-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </button>
                    @else
                        <span class="text-xs text-gray-400">Tidak ada file</span>
                    @endif
                </div>
                <div class="flex items-center gap-1.5">
                    @if($s->status === 'rejected')
                        <button @click="editOpen = true"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Perbaiki & Kirim Ulang
                        </button>
                    @else
                        <button @click="editOpen = true"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                    @endif
                    <form method="POST" action="{{ route('sertifikat.destroy', $s->id) }}" onsubmit="return confirm('Hapus sertifikat ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            {{-- Edit/Resubmit Modal --}}
            <div x-show="editOpen" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                 @click.self="editOpen = false"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[90vh] overflow-y-auto" @click.stop
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $s->status === 'rejected' ? 'Perbaiki & Kirim Ulang' : 'Edit Sertifikat' }}</h3>
                        <button @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @if($s->status === 'rejected' && $s->catatan_verifikasi)
                        <div class="mb-4 px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
                            <span class="font-medium">Alasan penolakan:</span> {{ $s->catatan_verifikasi }}
                        </div>
                    @elseif($s->status === 'approved')
                        <div class="mb-4 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                            <span class="font-medium">Perhatian:</span> Mengubah sertifikat yang sudah disetujui akan memerlukan verifikasi ulang oleh admin.
                        </div>
                    @endif
                    <form method="POST" action="{{ $s->status === 'rejected' ? route('sertifikat.resubmit', $s->id) : route('sertifikat.update', $s->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @if($s->status !== 'rejected')
                            @method('PUT')
                        @endif
                        <input type="hidden" name="pegawai_id" value="{{ $s->pegawai_id }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelatihan</label>
                            <input type="text" name="nama_pelatihan" value="{{ $s->nama_pelatihan }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penyelenggara</label>
                            <input type="text" name="penyelenggara" value="{{ $s->penyelenggara }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                                <input type="date" name="tanggal" value="{{ $s->tanggal ? \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
                                <input type="date" name="tanggal_akhir" value="{{ $s->tanggal_akhir ? \Carbon\Carbon::parse($s->tanggal_akhir)->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">JPL</label>
                                <input type="number" name="jpl" value="{{ $s->jpl }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelatihan</label>
                            <select name="jenis_pelatihan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none bg-white">
                                @foreach($jenisPelatihan as $jenis)
                                    <option value="{{ $jenis }}" {{ $s->jenis_pelatihan === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <input type="text" name="keterangan" value="{{ $s->keterangan }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ganti File PDF</label>
                            @if($s->pdf)
                                <p class="text-xs text-gray-500 mb-2">File saat ini: <a href="{{ route('sertifikat.pdf', $s->id) }}" target="_blank" class="text-primary-600 hover:underline">{{ $s->pdf }}</a></p>
                            @endif
                            <input type="file" name="pdf" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ingin mengganti. Maks 2MB.</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="editOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">
                                {{ $s->status === 'rejected' ? 'Kirim Ulang' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm text-gray-500">Belum ada sertifikat</p>
            <a href="{{ route('sertifikat.create') }}" class="mt-2 inline-block text-sm text-primary-600 hover:text-primary-700 font-medium">Upload Sertifikat Pertama</a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($sertifikat->hasPages())
    <div class="flex justify-center">
        {{ $sertifikat->links() }}
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
