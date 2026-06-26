<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('pegawai_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function markRead($id)
    {
        $notif = Notifikasi::where('id', $id)->where('pegawai_id', Auth::id())->firstOrFail();
        $notif->update(['dibaca' => true]);
        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead()
    {
        Notifikasi::where('pegawai_id', Auth::id())->where('dibaca', false)->update(['dibaca' => true]);
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
