@extends('layouts.app')

@section('title', 'Upload Sertifikat')

@section('breadcrumb')
<a href="{{ route('sertifikat.index') }}" class="hover:text-primary-600">Sertifikat</a>
<svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
<span class="text-gray-900 dark:text-white font-medium">Upload</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Upload Sertifikat</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unggah sertifikat pelatihan untuk menambah capaian kompetensi Anda</p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">Terdapat kesalahan pada form:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 ml-7">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('sertifikat.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Pegawai (hidden for Staff, selectable for Admin) --}}
            @if(auth()->user()->level === 'Staff')
                <input type="hidden" name="pegawai_id" value="{{ auth()->user()->id }}">
            @else
                <div>
                    <label for="pegawai_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        Pegawai <span class="text-red-500">*</span>
                    </label>
                    <select name="pegawai_id" id="pegawai_id" required
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors bg-white @error('pegawai_id') border-red-300 @enderror">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($pegawai as $p)
                            <option value="{{ $p->id }}" {{ old('pegawai_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->nip }})</option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Nama Pelatihan --}}
            <div>
                <label for="nama_pelatihan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Pelatihan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_pelatihan" id="nama_pelatihan" value="{{ old('nama_pelatihan') }}" required
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors @error('nama_pelatihan') border-red-300 @enderror"
                       placeholder="Masukkan nama pelatihan">
                @error('nama_pelatihan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Penyelenggara --}}
            <div>
                <label for="penyelenggara" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Penyelenggara <span class="text-red-500">*</span>
                </label>
                <input type="text" name="penyelenggara" id="penyelenggara" value="{{ old('penyelenggara') }}" required
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors @error('penyelenggara') border-red-300 @enderror"
                       placeholder="Masukkan nama penyelenggara">
                @error('penyelenggara')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal & JPL row --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors @error('tanggal') border-red-300 @enderror">
                    @error('tanggal')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ old('tanggal_akhir') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors @error('tanggal_akhir') border-red-300 @enderror">
                    @error('tanggal_akhir')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="jumlah_jpl" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jumlah JPL</label>
                    <input type="number" name="jpl" id="jumlah_jpl" value="{{ old('jumlah_jpl') }}" min="0"
                           class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors @error('jumlah_jpl') border-red-300 @enderror"
                           placeholder="0">
                    @error('jumlah_jpl')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Jenis Pelatihan --}}
            <div>
                <label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jenis Pelatihan</label>
                <select name="jenis_pelatihan" id="jenis_pelatihan"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors bg-white @error('jenis_pelatihan') border-red-300 @enderror">
                    <option value="">-- Pilih Jenis Pelatihan --</option>
                    @foreach($jenisPelatihan as $jenis)
                        <option value="{{ $jenis }}" {{ old('jenis_pelatihan') === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                    @endforeach
                </select>
                @error('jenis_pelatihan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan') }}"
                       class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-colors @error('keterangan') border-red-300 @enderror"
                       placeholder="Keterangan tambahan (opsional)">
                @error('keterangan')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Upload PDF (Drag & Drop) --}}
            <div x-data="{ fileName: '', dragging: false }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Upload Sertifikat (PDF)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg transition-all cursor-pointer"
                     :class="dragging ? 'border-primary-500 bg-primary-50' : (fileName ? 'border-green-400 bg-green-50' : 'border-gray-300 dark:border-gray-600 hover:border-primary-400')"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     @drop.prevent="dragging = false; $refs.pdfInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''"
                     @click="$refs.pdfInput.click()">
                    <div class="space-y-2 text-center">
                        <template x-if="!fileName">
                            <div>
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><span class="font-medium text-primary-600">Klik untuk pilih</span> atau seret file ke sini</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PDF maksimal 2MB</p>
                            </div>
                        </template>
                        <template x-if="fileName">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium text-green-700" x-text="fileName"></span>
                                <button type="button" @click.stop="fileName = ''; $refs.pdfInput.value = ''" class="text-gray-400 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                <input x-ref="pdfInput" name="pdf" type="file" class="sr-only" accept=".pdf"
                       @change="fileName = $event.target.files[0]?.name || ''">
                @error('pdf')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-200 transition-all duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload Sertifikat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
