@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <h1 class="text-xl font-bold text-gray-900">Log Aktivitas</h1>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" action="{{ route('activity.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
            <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary-500 outline-none">
                <option value="">Semua Aksi</option>
                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
            </select>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 transition-colors">Filter</button>
                @if(request()->hasAny(['search', 'action']))
                    <a href="{{ route('activity.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition-colors">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Log List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $log->pegawai->nama ?? 'System' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $colors = ['login' => 'bg-blue-100 text-blue-700', 'logout' => 'bg-gray-100 text-gray-700', 'create' => 'bg-green-100 text-green-700', 'update' => 'bg-amber-100 text-amber-700', 'delete' => 'bg-red-100 text-red-700'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-700' }}">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada log aktivitas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-center">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
