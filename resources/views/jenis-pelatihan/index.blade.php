@extends('layouts.app')

@section('title', 'Kelola Jenis Pelatihan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Jenis Pelatihan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola jenis pelatihan tahun {{ $tahun }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            {{-- Copy from Year --}}
            @php $otherYears = $availableYears->reject(fn($y) => $y === $tahun)->values(); @endphp
            @if($otherYears->isNotEmpty())
            <div x-data="{ open: false }">
                <button @click="open = true" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    Salin dari Tahun Lain
                </button>
                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6" @click.stop>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">Salin Jenis Pelatihan</h3>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('jenis-pelatihan.copy') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Salin dari tahun</label>
                                <select name="dari_tahun" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none">
                                    @foreach($otherYears as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-gray-500">Jenis pelatihan yang sudah ada di tahun {{ $tahun }} tidak akan ditimpa.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Salin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Add New --}}
            <div x-data="{ open: false }">
                <button @click="open = true" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Jenis
                </button>
                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">Tambah Jenis Pelatihan</h3>
                            <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('jenis-pelatihan.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Pelatihan</label>
                                <input type="text" name="nama" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Contoh: Workshop Kepemimpinan">
                            </div>
                            <p class="text-xs text-gray-500">Akan ditambahkan untuk tahun {{ $tahun }} saja.</p>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    @php
        $totalAktif = $jenisList->where('aktif', true)->count();
        $totalNonaktif = $jenisList->where('aktif', false)->count();
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-900">{{ $jenisList->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-green-200 bg-green-50/50 p-4">
            <p class="text-xs font-medium text-green-600 uppercase tracking-wider">Aktif</p>
            <p class="mt-1.5 text-2xl font-bold text-green-700">{{ $totalAktif }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nonaktif</p>
            <p class="mt-1.5 text-2xl font-bold text-gray-400">{{ $totalNonaktif }}</p>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-12">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Jenis Pelatihan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-28">Digunakan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jenisList as $index => $j)
                    <tr class="hover:bg-gray-50 transition-colors {{ !$j->aktif ? 'opacity-50' : '' }}" x-data="{ editOpen: false }">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $j->nama }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php $count = $usageCounts[$j->nama] ?? 0; @endphp
                            @if($count > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $count }}</span>
                            @else
                                <span class="text-gray-300">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($j->aktif)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button @click="editOpen = true" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('jenis-pelatihan.toggle', $j->id) }}">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg transition-colors {{ $j->aktif ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-gray-400 hover:text-green-600 hover:bg-green-50' }}" title="{{ $j->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        @if($j->aktif)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                @if($count === 0)
                                <form method="POST" action="{{ route('jenis-pelatihan.destroy', $j->id) }}" onsubmit="return confirm('Hapus jenis pelatihan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>

                            {{-- Edit Modal --}}
                            <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="editOpen = false">
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 text-left" @click.stop>
                                    <div class="flex items-center justify-between mb-5">
                                        <h3 class="text-lg font-semibold text-gray-900">Edit Jenis Pelatihan</h3>
                                        <button @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <form method="POST" action="{{ route('jenis-pelatihan.update', $j->id) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jenis Pelatihan</label>
                                            <input type="text" name="nama" value="{{ $j->nama }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
                                        </div>
                                        @if($count > 0)
                                            <p class="text-xs text-amber-600">Mengubah nama akan memperbarui {{ $count }} sertifikat di tahun {{ $tahun }}.</p>
                                        @endif
                                        <div class="flex justify-end gap-3">
                                            <button type="button" @click="editOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Batal</button>
                                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            <p class="text-sm text-gray-500">Belum ada jenis pelatihan untuk tahun {{ $tahun }}</p>
                            @if($otherYears->isNotEmpty())
                                <p class="mt-1 text-xs text-gray-400">Gunakan tombol "Salin dari Tahun Lain" untuk memulai</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($jenisList->count() > 0)
    <div class="text-xs text-gray-400 text-right">
        Menampilkan {{ $jenisList->count() }} jenis pelatihan untuk tahun {{ $tahun }}
    </div>
    @endif
</div>
@endsection
