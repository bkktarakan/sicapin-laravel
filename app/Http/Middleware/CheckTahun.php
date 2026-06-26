<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTahun
{
    /**
     * Pastikan session 'tahun' tersedia.
     * Jika tidak, default ke tahun berjalan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('tahun')) {
            session(['tahun' => date('Y')]);
        }

        return $next($request);
    }
}
