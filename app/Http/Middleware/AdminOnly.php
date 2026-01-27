<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (auth()->user()->email !== env('ADMIN_EMAIL')) {
            abort(403, 'Kamu bukan admin');
        }

        return $next($request);
    }
}
