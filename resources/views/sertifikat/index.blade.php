@extends('layouts.app')

@section('title', 'Daftar Sertifikat')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Sertifikat</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tahun {{ $tahun }}</p>
        </div>
        <a href="{{ route('sertifikat.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Upload Baru
        </a>
    </div>

    {{-- Live Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4" x-data="liveSearchSertifikat()" @click.outside="showResults = false">
        <div class="flex gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" x-model="query" @input.debounce.300ms="search()" @focus="if(results.length) showResults = true"
                       placeholder="Cari nama pelatihan, pegawai, NIP, penyelenggara, jenis, tanggal..."
                       class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <div x-show="query.length > 0" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button @click="query = ''; results = []; showResults = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form method="GET" action="{{ route('sertifikat.index') }}" class="flex gap-2">
                <input type="hidden" name="search" :value="query">
                <button type="submit" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">Cari</button>
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
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-xs font-bold text-primary-700" x-text="r.pegawai_nama.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="r.nama_pelatihan"></p>
                                    <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">
                                        <span class="font-medium text-gray-700" x-text="r.pegawai_nama"></span>
                                        <span>&middot;</span>
                                        <span x-text="r.penyelenggara"></span>
                                        <span>&middot;</span>
                                        <span x-text="r.tanggal"></span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-[10px] font-medium text-gray-600" x-text="r.jenis_pelatihan"></span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium"
                                              :class="r.status === 'approved' ? 'bg-green-100 text-green-700' : (r.status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')"
                                              x-text="r.status === 'approved' ? 'Disetujui' : (r.status === 'rejected' ? 'Ditolak' : 'Pending')"></span>
                                    </div>
                                </div>
                                <span class="flex-shrink-0 text-sm font-bold text-primary-700" x-text="r.jpl + ' JPL'"></span>
                                <div class="flex-shrink-0 flex items-center gap-1">
                                    <template x-if="r.has_pdf">
                                        <button @click="$dispatch('open-preview', { url: r.preview_url })"
                                                class="p-1 text-gray-400 hover:text-primary-600 rounded hover:bg-primary-50 transition-colors" title="Preview">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
    function liveSearchSertifikat() {
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

    {{-- Pegawai List --}}
    <div class="space-y-4">
        @forelse($pegawaiList as $pegawai)
        @php
            $sertifikats = $sertifikatByPegawai->get($pegawai->id, collect());
            $rekap = $rekapByPegawai->get($pegawai->id);
            $jpl = $rekap->jumlah_jpl ?? 0;
            $progress = min(100, round(($jpl / 20) * 100));
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" x-data="{ open: false }">
            {{-- Pegawai Header (clickable) --}}
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
                        <p class="text-lg font-bold {{ $jpl >= 20 ? 'text-green-600' : 'text-gray-900' }}">{{ $jpl }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">JPL</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-900">{{ $sertifikats->count() }}</p>
                        <p class="text-[10px] text-gray-400 uppercase">Sertifikat</p>
                    </div>
                    <div class="w-20">
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $progress >= 100 ? 'bg-green-500' : ($progress >= 50 ? 'bg-primary-500' : 'bg-amber-500') }}" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 text-center mt-0.5">{{ $progress }}%</p>
                    </div>
                </div>

                {{-- Badge status --}}
                @if(($rekap->keterangan ?? '') === 'Terpenuhi')
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Terpenuhi</span>
                @else
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Belum</span>
                @endif

                {{-- Chevron --}}
                <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            {{-- Mobile stats (visible when collapsed) --}}
            <div class="sm:hidden px-5 pb-3 -mt-1 flex items-center gap-3 text-xs text-gray-500">
                <span>{{ $jpl }} JPL</span>
                <span>&middot;</span>
                <span>{{ $sertifikats->count() }} sertifikat</span>
                <span>&middot;</span>
                @if(($rekap->keterangan ?? '') === 'Terpenuhi')
                    <span class="text-green-600 font-medium">Terpenuhi</span>
                @else
                    <span class="text-red-600 font-medium">Belum</span>
                @endif
            </div>

            {{-- Expanded: Sertifikat List --}}
            <div x-show="open" x-cloak x-collapse>
                <div class="border-t border-gray-100">
                    @if($sertifikats->count() > 0)
                        @foreach($sertifikats as $s)
                        <div class="px-5 py-3 flex items-start gap-3 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50" x-data="{ editOpen: false }">
                            {{-- Dot --}}
                            <div class="flex-shrink-0 mt-1.5 w-2 h-2 rounded-full bg-primary-300"></div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $s->nama_pelatihan }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                    <span>{{ $s->penyelenggara }}</span>
                                    <span>&middot;</span>
                                    <span>{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-gray-100 text-[10px] font-medium text-gray-600">{{ $s->jenis_pelatihan }}</span>
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
                                        <h3 class="text-lg font-semibold text-gray-900">Edit Sertifikat</h3>
                                        <button @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('sertifikat.update', $s->id) }}" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        @method('PUT')
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
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="px-5 py-6 text-center text-sm text-gray-400">Belum ada sertifikat pada tahun {{ $tahun }}</div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-sm text-gray-500">Tidak ada pegawai ditemukan</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($pegawaiList->hasPages())
    <div class="flex justify-center">
        {{ $pegawaiList->links() }}
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
