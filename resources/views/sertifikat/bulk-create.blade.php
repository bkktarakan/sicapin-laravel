@extends('layouts.app')

@section('title', 'Bulk Upload Sertifikat')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="bulkUpload()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Bulk Upload Sertifikat</h1>
            <p class="mt-1 text-sm text-gray-500">Tambah beberapa sertifikat sekaligus &mdash; Tahun {{ session('tahun', date('Y')) }}</p>
        </div>
        <a href="{{ route('sertifikat.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('sertifikat.bulkStore') }}">
        @csrf

        <div class="space-y-4">
            <template x-for="(item, index) in items" :key="index">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700">Sertifikat <span x-text="index + 1"></span></h3>
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Pegawai</label>
                            <select :name="'items[' + index + '][pegawai_id]'" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih --</option>
                                @foreach($pegawai as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Pelatihan</label>
                            <input type="text" :name="'items[' + index + '][nama_pelatihan]'" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Penyelenggara</label>
                            <input type="text" :name="'items[' + index + '][penyelenggara]'" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai</label>
                            <input type="date" :name="'items[' + index + '][tanggal]'" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Selesai</label>
                            <input type="date" :name="'items[' + index + '][tanggal_akhir]'" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">JPL</label>
                            <input type="number" :name="'items[' + index + '][jpl]'" min="1" max="10000" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Pelatihan</label>
                            <select :name="'items[' + index + '][jenis_pelatihan]'" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih --</option>
                                @foreach($jenisPelatihan as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                            <input type="text" :name="'items[' + index + '][keterangan]'" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500" placeholder="Opsional">
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between mt-6">
            <button type="button" @click="addItem()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Baris
            </button>
            <div class="flex gap-3">
                <span class="text-sm text-gray-400 self-center" x-text="items.length + ' sertifikat'"></span>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Semua
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function bulkUpload() {
    return {
        items: [{}],
        addItem() { this.items.push({}); },
        removeItem(index) { this.items.splice(index, 1); }
    }
}
</script>
@endsection
