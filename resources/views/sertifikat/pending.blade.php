@extends('layouts.app')

@section('title', 'Verifikasi Sertifikat')

@section('breadcrumb')
<a href="{{ route('sertifikat.index') }}" class="hover:text-primary-600">Sertifikat</a>
<svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 dark:text-white font-medium">Verifikasi</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ selectedIds: [], selectAll: false, showBatchReject: false }"
     x-init="$watch('selectAll', val => { selectedIds = val ? {{ json_encode($sertifikat->pluck('id')) }} : [] })">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Verifikasi Sertifikat</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $sertifikat->count() }} sertifikat menunggu verifikasi &middot; Tahun {{ $tahun }}</p>
        </div>
    </div>

    @if($sertifikat->count() > 0)
    {{-- Batch Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex flex-wrap items-center gap-3" x-show="selectedIds.length > 0" x-cloak x-transition>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><span x-text="selectedIds.length"></span> dipilih</span>
        <form method="POST" action="{{ route('sertifikat.batchApprove') }}" class="inline">
            @csrf
            <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Setujui Semua
            </button>
        </form>
        <button @click="showBatchReject = true" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 text-xs font-medium rounded-lg hover:bg-red-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Tolak Semua
        </button>

        {{-- Batch Reject Modal --}}
        <div x-show="showBatchReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showBatchReject = false">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tolak <span x-text="selectedIds.length"></span> Sertifikat</h3>
                <form method="POST" action="{{ route('sertifikat.batchReject') }}" class="space-y-4">
                    @csrf
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan Penolakan</label>
                        <input type="text" name="catatan" required placeholder="Alasan penolakan untuk semua yang dipilih..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showBatchReject = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Select All --}}
    <div class="flex items-center gap-2 px-1">
        <input type="checkbox" x-model="selectAll" id="selectAllPending" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
        <label for="selectAllPending" class="text-xs font-medium text-gray-500 dark:text-gray-400">Pilih Semua</label>
    </div>
    @endif

    <div class="space-y-3">
        @forelse($sertifikat as $s)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-amber-200 dark:border-amber-700 p-5" x-data="{ showReject: false }">
            <div class="flex items-start gap-3">
                {{-- Checkbox --}}
                <input type="checkbox" value="{{ $s->id }}" x-model.number="selectedIds" class="mt-1 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">

                <div class="flex-1 min-w-0">
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Menunggu</span>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $s->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $s->nama_pelatihan }}</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $s->penyelenggara }}</p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-bold bg-primary-50 text-primary-700">{{ $s->jpl }} JPL</span>
                    </div>

                    {{-- Info --}}
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $s->pegawai->nama ?? '-' }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-[11px] font-medium text-gray-600 dark:text-gray-300">{{ $s->jenis_pelatihan }}</span>
                        @if($s->pdf)
                        <button @click="$dispatch('open-preview', { url: '{{ route('sertifikat.preview', $s->id) }}' })" class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview PDF
                        </button>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                        <form method="POST" action="{{ route('sertifikat.approve', $s->id) }}" class="inline">
                            @csrf
                            <input type="hidden" name="catatan" value="">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Setujui
                            </button>
                        </form>
                        <button @click="showReject = !showReject" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 text-xs font-medium rounded-lg hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak
                        </button>
                    </div>

                    {{-- Reject form --}}
                    <div x-show="showReject" x-cloak x-collapse class="mt-3">
                        <form method="POST" action="{{ route('sertifikat.reject', $s->id) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="catatan" required placeholder="Alasan penolakan..." class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none">
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">Kirim</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="w-12 h-12 text-green-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Semua sertifikat sudah diverifikasi</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tidak ada sertifikat yang menunggu verifikasi</p>
        </div>
        @endforelse
    </div>
</div>

{{-- PDF Preview Modal --}}
<div x-data="{ showPreview: false, previewUrl: '' }" @open-preview.window="showPreview = true; previewUrl = $event.detail.url">
    <div x-show="showPreview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" @click.self="showPreview = false">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-4xl mx-4 h-[85vh] flex flex-col" @click.stop>
            <div class="flex items-center justify-between px-5 py-3 border-b dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Preview Sertifikat</h3>
                <button @click="showPreview = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 p-1"><iframe :src="previewUrl" class="w-full h-full rounded-lg border-0"></iframe></div>
        </div>
    </div>
</div>
@endsection
