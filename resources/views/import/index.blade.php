@extends('layouts.app')

@section('title', 'Import Pegawai')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h1 class="text-xl font-bold text-gray-900">Import Data Pegawai</h1>
        <p class="mt-1 text-sm text-gray-500">Upload file CSV untuk menambahkan pegawai secara massal</p>
    </div>

    {{-- Import Errors --}}
    @if(session('importErrors') && count(session('importErrors')) > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <h4 class="text-sm font-medium text-amber-800 mb-2">Detail import:</h4>
        <ul class="text-xs text-amber-700 space-y-1 max-h-40 overflow-y-auto">
            @foreach(session('importErrors') as $err)
                <li>&bull; {{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        {{-- Info --}}
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Format CSV yang diterima:</h4>
            <div class="text-xs text-blue-700 space-y-1">
                <p>Kolom wajib: <strong>nip</strong>, <strong>nama</strong></p>
                <p>Kolom opsional: <strong>jabatan</strong>, <strong>pangkat</strong>, <strong>level</strong></p>
                <p>Level yang valid: Admin, Kepala Kantor, Ka. Subbag Adum, Staff (default: Staff)</p>
                <p>Password default pegawai baru = NIP</p>
            </div>
            <a href="{{ route('import.template') }}" class="inline-flex items-center gap-1 mt-3 text-xs font-medium text-blue-600 hover:text-blue-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Template CSV
            </a>
        </div>

        <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">File CSV</label>
                <input type="file" name="file" accept=".csv,.txt" required
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('file')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Maksimal 2 MB</p>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Import Pegawai
            </button>
        </form>
    </div>
</div>
@endsection
