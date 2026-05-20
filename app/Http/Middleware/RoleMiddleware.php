<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (contoh: super_admin, admin, dosen)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user memiliki role yang diizinkan, lanjutkan request
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses, abort 403 atau redirect ke dashboard sesuai role
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}