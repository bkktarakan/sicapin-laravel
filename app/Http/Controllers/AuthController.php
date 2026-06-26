<?php

namespace App\Http\Controllers;

use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use LogsActivity;
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        // Rate limiting: max 5 attempts per minute
        $key = 'login_' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam ' . $seconds . ' detik.');
        }

        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $credentials = [
            'nip' => $request->nip,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            if (!Auth::user()->aktif) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect()->back()
                    ->withInput($request->only('nip'))
                    ->with('error', 'Akun Anda tidak aktif. Hubungi Admin.');
            }

            \Illuminate\Support\Facades\RateLimiter::clear($key);
            $request->session()->regenerate();
            session(['tahun' => date('Y')]);

            $this->logActivity('login', 'Login berhasil - NIP: ' . $request->nip);

            return redirect()->intended(route('home'));
        }

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        return redirect()->back()
            ->withInput($request->only('nip', 'tahun'))
            ->with('error', 'NIP atau Password salah.');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        $this->logActivity('logout', 'Logout - ' . Auth::user()->nama);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
