@extends('layouts.app')

@section('title', 'Riwayat Pelatihan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Riwayat Pelatihan</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $user->nama }}</p>
        </div>
        <a href="{{ route('profile.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">&larr; Kembali ke Profil</a>
    </div>

    {{-- JPL Summary per Year --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Ringkasan JPL per Tahun</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($rekapPerTahun as $r)
            <div class="border rounded-lg p-3 text-center {{ $r->keterangan === 'Terpenuhi' ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                <p class="text-lg font-bold {{ $r->keterangan === 'Terpenuhi' ? 'text-green-700' : 'text-gray-700' }}">{{ $r->jumlah_jpl }}</p>
                <p class="text-xs text-gray-500">JPL {{ $r->tahun }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Year selector --}}
    <div class="flex items-center gap-3">
        <span class="text-sm text-gray-600">Pilih Tahun:</span>
        <div class="flex gap-2">
            @foreach($years as $y)
                <a href="{{ route('profile.riwayat', ['tahun' => $y]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium {{ $selectedYear == $y ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} transition-colors">
                    {{ $y }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Sertifikat list --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Pelatihan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Penyelenggara</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">JPL</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sertifikat as $i => $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $s->nama_pelatihan }}</td>
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $s->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $s->penyelenggara }}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-900">{{ $s->jpl }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-50 text-primary-700">{{ $s->jenis_pelatihan }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada pelatihan pada tahun {{ $selectedYear }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
