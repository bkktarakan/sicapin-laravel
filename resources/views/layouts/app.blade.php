<!DOCTYPE html>
<html lang="id" :class="darkMode && 'dark'" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SICAPIN - @yield('title', 'Sistem Monitoring Kompetensi ASN')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2/dist/chartjs-plugin-datalabels.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#EEF2FF',
                            100: '#E0E7FF',
                            200: '#C7D2FE',
                            300: '#A5B4FC',
                            400: '#818CF8',
                            500: '#6366F1',
                            600: '#4F46E5',
                            700: '#4338CA',
                            800: '#3730A3',
                            900: '#312E81',
                        }
                    }
                }
            }
        }
        if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased dark:text-gray-200" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200 ease-in-out lg:translate-x-0 flex flex-col"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Sidebar header --}}
        <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 flex-shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-bkktrk.svg') }}" alt="Logo" class="h-9 w-auto">
                <span class="text-lg font-bold text-gray-900 dark:text-white">SICAPIN</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 min-h-0 px-3 py-4 space-y-1 overflow-y-auto">
            {{-- === UTAMA === --}}
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('home') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('home') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            {{-- === SERTIFIKAT === --}}
            <p class="px-3 pt-5 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Sertifikat</p>

            <a href="{{ route('sertifikat.create') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.create') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.create') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Upload Sertifikat
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('sertifikat.bulkCreate') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.bulkCreate') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.bulkCreate') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Bulk Upload
            </a>
            @endif

            <a href="{{ route('sertifikat.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.index') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Daftar Sertifikat
            </a>

            <a href="{{ route('sertifikat.download') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.download*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.download*') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Sertifikat
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('sertifikat.pending') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.pending') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.pending') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Verifikasi
                @php $pendingCount = \App\Models\Sertifikat::where('status', 'pending')->where('tahun', session('tahun', date('Y')))->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold bg-red-500 text-white rounded-full">{{ $pendingCount > 9 ? '9+' : $pendingCount }}</span>
                @endif
            </a>
            @endif

            {{-- === REKAP & LAPORAN === --}}
            <p class="px-3 pt-5 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Rekap & Laporan</p>

            <a href="{{ route('sertifikat.rekap') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.rekap') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.rekap') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rekap JPL
            </a>

            @if(auth()->user()->isAdmin())
            <a href="{{ route('sertifikat.rekapJenis') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('sertifikat.rekapJenis') || request()->routeIs('sertifikat.detailJenis') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('sertifikat.rekapJenis') || request()->routeIs('sertifikat.detailJenis') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Rekap Jenis Pelatihan
            </a>
            @endif


            <a href="{{ route('home.comparison') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('home.comparison') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('home.comparison') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Perbandingan Tahun
            </a>

            {{-- === KELOLA (Admin only) === --}}
            @if(auth()->user()->isAdmin())
            <p class="px-3 pt-5 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Kelola</p>

            <a href="{{ route('pegawai.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('pegawai.index') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('pegawai.index') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Data Pegawai
            </a>

            <a href="{{ route('jenis-pelatihan.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('jenis-pelatihan.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('jenis-pelatihan.*') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Jenis Pelatihan
            </a>

            <a href="{{ route('import.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('import.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('import.*') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Import Pegawai
            </a>

            @if(auth()->user()->level === 'Admin')
            <a href="{{ route('activity.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('activity.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('activity.*') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Log Aktivitas
            </a>
            @endif
            @endif

            {{-- === AKUN === --}}
            <p class="px-3 pt-5 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Akun</p>

            <a href="{{ route('profile.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('profile.*') ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('profile.*') ? 'text-primary-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </a>

            <div class="pt-2 mt-2 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-150">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    {{-- Main content area --}}
    <div class="lg:pl-64 min-h-screen flex flex-col">

        {{-- Top navbar --}}
        <header class="sticky top-0 z-30 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                {{-- Mobile menu button --}}
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Global Search --}}
                <div class="hidden lg:block flex-1 max-w-md mx-4" x-data="{ query: '', open: false, results: [], loading: false }" @click.outside="open = false">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" x-model="query" @focus="if(query.length >= 2) open = true"
                               @input.debounce.300ms="if(query.length >= 2) { loading = true; fetch('{{ route("search") }}?q=' + encodeURIComponent(query)).then(r => r.json()).then(d => { results = d; open = true; loading = false; }); } else { open = false; }"
                               placeholder="Cari pegawai, sertifikat..."
                               class="w-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:bg-white outline-none transition-colors">
                        <div x-show="open && query.length >= 2" x-cloak class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-80 overflow-y-auto z-50">
                            <template x-if="loading">
                                <div class="px-4 py-3 text-sm text-gray-400 text-center">Mencari...</div>
                            </template>
                            <template x-if="!loading && results.length === 0">
                                <div class="px-4 py-3 text-sm text-gray-400 text-center">Tidak ditemukan</div>
                            </template>
                            <template x-for="r in results" :key="r.type + r.id">
                                <a :href="r.url" class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0" @click="open = false">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                         :class="r.type === 'pegawai' ? 'bg-primary-100 text-primary-700' : 'bg-green-100 text-green-700'">
                                        <span class="text-xs font-bold" x-text="r.initial"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="r.title"></p>
                                        <p class="text-xs text-gray-400 truncate" x-text="r.subtitle"></p>
                                    </div>
                                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded"
                                          :class="r.type === 'pegawai' ? 'bg-primary-50 text-primary-600' : 'bg-green-50 text-green-600'"
                                          x-text="r.type === 'pegawai' ? 'Pegawai' : 'Sertifikat'"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Mobile title --}}
                <div class="lg:hidden">
                    <h1 class="text-lg font-semibold text-gray-900">SICAPIN</h1>
                </div>

                {{-- Tahun Selector + Dark Mode --}}
                <div class="flex items-center gap-3">
                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode" class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Dark Mode">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    <form method="POST" action="{{ route('ganti.tahun') }}" class="flex items-center gap-2">
                        @csrf
                        <label class="text-xs text-gray-500 hidden sm:inline">Tahun:</label>
                        <select name="tahun" onchange="this.form.submit()"
                                class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                            @for($y = 2023; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ session('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </form>

                    {{-- Notification Bell --}}
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" class="relative p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(auth()->user()->unreadNotifikasi()->count() > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                    {{ auth()->user()->unreadNotifikasi()->count() > 9 ? '9+' : auth()->user()->unreadNotifikasi()->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Notification Dropdown --}}
                        <div x-show="notifOpen" x-cloak @click.outside="notifOpen = false"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</span>
                                <a href="{{ route('notifikasi.index') }}" class="text-xs text-primary-600 hover:text-primary-700">Lihat Semua</a>
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse(auth()->user()->notifikasi()->latest()->limit(5)->get() as $n)
                                    <div class="px-4 py-3 {{ $n->dibaca ? 'bg-white dark:bg-gray-800' : 'bg-primary-50 dark:bg-primary-900/30' }}">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $n->judul }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($n->pesan, 60) }}</p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                        Belum ada notifikasi
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User info --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="hidden sm:inline text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->nama }}</span>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6 dark:bg-gray-900">

            {{-- Breadcrumb --}}
            @hasSection('breadcrumb')
            <nav class="mb-4 text-xs text-gray-500 flex items-center gap-1.5 print:hidden">
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @yield('breadcrumb')
            </nav>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 sm:px-6 py-4 print:hidden">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">&copy; {{ date('Y') }} SICAPIN - BKK Kelas I Tarakan</p>
        </footer>
    </div>

{{-- SweetAlert2 Notifications & Confirm Override --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
    @endif
    
    @if(session('error'))
        Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
    @endif
    
    @if(session('info'))
        Toast.fire({ icon: 'info', title: '{{ session('info') }}' });
    @endif
    
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            html: '<ul class="text-left list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>',
            confirmButtonText: 'Tutup'
        });
    @endif

    // Global Confirm Override
    document.querySelectorAll('form').forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
            if (match) {
                const message = match[1];
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        }
    });
});
</script>

{{-- Session Timeout Warning + Auto-logout --}}
<div x-data="sessionTimer()" x-show="showWarning" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center bg-black/50 print:hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 p-6 text-center">
        <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Sesi Akan Berakhir</h3>
        <p class="mt-2 text-sm text-gray-500">Anda akan keluar otomatis dalam <span class="font-bold text-amber-600" x-text="countdown"></span> detik.</p>
        <div class="mt-5 flex gap-3 justify-center">
            <button @click="extend()" class="px-5 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700">Perpanjang Sesi</button>
            <button @click="logout()" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Logout</button>
        </div>
    </div>
</div>

<script>
function sessionTimer() {
    return {
        showWarning: false,
        countdown: 60,
        idleTimer: null,
        countdownTimer: null,
        IDLE_MS: {{ (int) config('session.lifetime', 120) * 60 * 1000 - 60000 }},
        init() { this.resetTimer(); ['mousemove','keydown','mousedown','touchstart','scroll'].forEach(e => document.addEventListener(e, () => { if (!this.showWarning) this.resetTimer(); }, { passive: true })); },
        resetTimer() { clearTimeout(this.idleTimer); clearInterval(this.countdownTimer); this.showWarning = false; this.idleTimer = setTimeout(() => this.warn(), this.IDLE_MS); },
        warn() { this.showWarning = true; this.countdown = 60; this.countdownTimer = setInterval(() => { this.countdown--; if (this.countdown <= 0) this.logout(); }, 1000); },
        extend() { this.resetTimer(); fetch('{{ route("home") }}', { credentials: 'same-origin' }); },
        logout() { let f = document.createElement('form'); f.method='POST'; f.action='{{ route("logout") }}'; let c = document.createElement('input'); c.type='hidden'; c.name='_token'; c.value='{{ csrf_token() }}'; f.appendChild(c); document.body.appendChild(f); f.submit(); }
    }
}
</script>

{{-- Print Styles --}}
<style>
@media print {
    body { background: white !important; }
    aside, header, footer, nav, .print\\:hidden, #toast-container, [x-cloak] { display: none !important; }
    .lg\\:pl-64 { padding-left: 0 !important; }
    main { padding: 0 !important; }
    .rounded-xl, .rounded-2xl, .rounded-lg { border-radius: 0 !important; }
    .shadow-sm, .shadow-lg, .shadow-2xl { box-shadow: none !important; }
    .border { border-color: #ddd !important; }
    * { color-adjust: exact !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>

{{-- Skeleton Loading CSS --}}
<style>
.skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 0.5rem; }
@keyframes skeleton-loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
</style>
</body>
</html>  
