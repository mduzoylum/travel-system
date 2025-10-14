<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'Lütfen giriş yapın.');
        }
        
        if (auth()->user()->role !== 'admin') {
            // Eğer AJAX request ise JSON dön
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bu işlem için yetkiniz yok.'], 403);
            }
            
            // Normal request ise geriye yönlendir ve logout yapma
            return back()->with('error', 'Bu sayfaya erişim yetkiniz yok! Sadece admin kullanıcılar erişebilir.');
        }

        return $next($request);
    }
}
