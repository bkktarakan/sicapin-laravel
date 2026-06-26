@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900">Notifikasi</h1>
        @if($notifikasi->where('dibaca', false)->count() > 0)
        <form method="POST" action="{{ route('notifikasi.readAll') }}">
            @csrf
            <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Tandai Semua Dibaca</button>
        </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifikasi as $n)
        <div class="bg-white rounded-xl border {{ $n->dibaca ? 'border-gray-200' : 'border-primary-200 bg-primary-50/30' }} p-4 flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                @if($n->tipe === 'success')
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center"><svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                @elseif($n->tipe === 'warning')
                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div>
                @elseif($n->tipe === 'danger')
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center"><svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>
                @else
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center"><svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ $n->judul }}</p>
                <p class="text-sm text-gray-600 mt-0.5">{{ $n->pesan }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
            </div>
            @if(!$n->dibaca)
            <form method="POST" action="{{ route('notifikasi.read', $n->id) }}">
                @csrf
                <button type="submit" class="text-xs text-primary-600 hover:text-primary-700 font-medium whitespace-nowrap">Tandai Dibaca</button>
            </form>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-sm text-gray-500">Belum ada notifikasi</p>
        </div>
        @endforelse
    </div>

    <div class="flex justify-center">
        {{ $notifikasi->links() }}
    </div>
</div>
@endsection
