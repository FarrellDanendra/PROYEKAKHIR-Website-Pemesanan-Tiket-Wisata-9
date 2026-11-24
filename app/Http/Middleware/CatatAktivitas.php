<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CatatAktivitas
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $identitas = $user ? "User ID: {$user->id} ({$user->email})" : 'Tamu (Guest)';

        Log::info("[AKTIVITAS] {$identitas} mengakses {$request->method()} {$request->fullUrl()} dari IP: {$request->ip()}");

        return $next($request);
    }
}