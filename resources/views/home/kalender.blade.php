@extends('layouts.app')

@section('title', 'Kalender Pelatihan')

@section('breadcrumb')
    <span class="text-gray-900 font-medium">Kalender Pelatihan</span>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="kalenderApp()">

    {{-- Header with navigation --}}
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Kalender Pelatihan</h1>
        <span class="text-sm text-gray-500 dark:text-gray-400">Tahun {{ $tahun }}</span>
    </div>

    {{-- Month navigation --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center justify-between">
            @if($prevBulan >= 1)
                <a href="{{ route('home.kalender', ['bulan' => $prevBulan]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Sebelumnya
                </a>
            @else
                <div></div>
            @endif

            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $namaBulan }} {{ $tahun }}</h2>

            @if($nextBulan <= 12)
                <a href="{{ route('home.kalender', ['bulan' => $nextBulan]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Berikutnya
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <div></div>
            @endif
        </div>
    </div>

    {{-- Calendar grid --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Day headers --}}
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)
                <div class="px-2 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider {{ $day === 'Min' || $day === 'Sab' ? 'bg-gray-50 dark:bg-gray-750' : 'bg-white dark:bg-gray-800' }}">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Calendar cells --}}
        <div class="grid grid-cols-7">
            {{-- Empty cells before first day --}}
            @for($i = 1; $i < $firstDayOfWeek; $i++)
                <div class="min-h-[80px] sm:min-h-[100px] border-b border-r border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-850"></div>
            @endfor

            {{-- Day cells --}}
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $hasData = isset($dayData[$day]) && count($dayData[$day]) > 0;
                    $count = $hasData ? count($dayData[$day]) : 0;
                    $isToday = ($day == date('j') && $bulan == date('n') && $tahun == date('Y'));
                    $dayOfWeek = ($firstDayOfWeek + $day - 2) % 7; // 0=Mon...6=Sun
                    $isWeekend = ($dayOfWeek >= 5);
                @endphp
                <div
                    class="min-h-[80px] sm:min-h-[100px] border-b border-r border-gray-100 dark:border-gray-700 p-1.5 sm:p-2 transition-colors duration-150
                           {{ $isWeekend ? 'bg-gray-50/50 dark:bg-gray-850' : 'bg-white dark:bg-gray-800' }}
                           {{ $hasData ? 'cursor-pointer hover:bg-primary-50/50 dark:hover:bg-primary-900/20' : '' }}
                           {{ $isToday ? 'ring-2 ring-inset ring-primary-400' : '' }}"
                    @if($hasData) @click="toggleDay({{ $day }})" @endif
                    :class="selectedDay === {{ $day }} && '!bg-primary-50 dark:!bg-primary-900/30'"
                >
                    {{-- Day number --}}
                    <div class="flex items-start justify-between">
                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-medium rounded-full
                                     {{ $isToday ? 'bg-primary-600 text-white' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ $day }}
                        </span>
                        @if($hasData)
                            <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 rounded-full">
                                {{ $count }}
                            </span>
                        @endif
                    </div>

                    {{-- Training dots/indicators --}}
                    @if($hasData)
                        <div class="mt-1.5 space-y-0.5">
                            @foreach(array_slice($dayData[$day], 0, 3) as $s)
                                <div class="flex items-center gap-1 group">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0
                                        @if($s->jenis_pelatihan === 'Teknis') bg-blue-500
                                        @elseif($s->jenis_pelatihan === 'Fungsional') bg-green-500
                                        @elseif($s->jenis_pelatihan === 'Manajerial') bg-purple-500
                                        @elseif($s->jenis_pelatihan === 'Sosial Kultural') bg-amber-500
                                        @else bg-gray-400
                                        @endif
                                    "></span>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 truncate hidden sm:inline">{{ Str::limit($s->nama_pelatihan, 18) }}</span>
                                </div>
                            @endforeach
                            @if($count > 3)
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">+{{ $count - 3 }} lagi</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endfor

            {{-- Empty cells after last day --}}
            @php
                $totalCells = ($firstDayOfWeek - 1) + $daysInMonth;
                $remainingCells = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for($i = 0; $i < $remainingCells; $i++)
                <div class="min-h-[80px] sm:min-h-[100px] border-b border-r border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-850"></div>
            @endfor
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
        <span class="font-medium text-gray-700 dark:text-gray-300">Jenis:</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Teknis</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Fungsional</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Manajerial</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Sosial Kultural</span>
        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-400"></span> Lainnya</span>
    </div>

    {{-- Expanded day detail --}}
    <div x-show="selectedDay !== null" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                Pelatihan tanggal <span x-text="selectedDay"></span> {{ $namaBulan }} {{ $tahun }}
            </h3>
            <button @click="selectedDay = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @for($day = 1; $day <= $daysInMonth; $day++)
                @if(isset($dayData[$day]) && count($dayData[$day]) > 0)
                    <template x-if="selectedDay === {{ $day }}">
                        <div>
                            @foreach($dayData[$day] as $s)
                                <div class="px-5 py-3 flex items-start gap-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
                                            @if($s->jenis_pelatihan === 'Teknis') bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300
                                            @elseif($s->jenis_pelatihan === 'Fungsional') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                            @elseif($s->jenis_pelatihan === 'Manajerial') bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300
                                            @elseif($s->jenis_pelatihan === 'Sosial Kultural') bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300
                                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                            @endif
                                        ">
                                            {{ strtoupper(substr($s->nama_pelatihan, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $s->nama_pelatihan }}</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                {{ $s->pegawai->nama ?? '-' }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                {{ $s->penyelenggara }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $s->jpl }} JPL
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $s->tanggal->format('d/m/Y') }}{{ $s->tanggal_akhir ? ' - ' . $s->tanggal_akhir->format('d/m/Y') : '' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="flex-shrink-0 text-[10px] font-medium px-2 py-0.5 rounded-full
                                        @if($s->jenis_pelatihan === 'Teknis') bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                        @elseif($s->jenis_pelatihan === 'Fungsional') bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400
                                        @elseif($s->jenis_pelatihan === 'Manajerial') bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                                        @elseif($s->jenis_pelatihan === 'Sosial Kultural') bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400
                                        @else bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-400
                                        @endif
                                    ">{{ $s->jenis_pelatihan }}</span>
                                </div>
                            @endforeach
                        </div>
                    </template>
                @endif
            @endfor
        </div>
    </div>

    {{-- Monthly summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Ringkasan {{ $namaBulan }} {{ $tahun }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-2xl font-bold text-primary-600">{{ $sertifikat->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total Pelatihan</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $sertifikat->sum('jpl') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total JPL</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $sertifikat->unique('pegawai_id')->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pegawai Aktif</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-amber-600">{{ collect($dayData)->filter(fn($d) => count($d) > 0)->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Hari dengan Pelatihan</p>
            </div>
        </div>
    </div>

</div>

<script>
function kalenderApp() {
    return {
        selectedDay: null,
        toggleDay(day) {
            this.selectedDay = this.selectedDay === day ? null : day;
        }
    }
}
</script>
@endsection
