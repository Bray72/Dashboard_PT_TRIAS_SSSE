<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            // Redirect ke halaman login jika belum login
            return redirect()->route('login')->with('message', 'Silakan login terlebih dahulu');
        }

        return $next($request);
    }
}
