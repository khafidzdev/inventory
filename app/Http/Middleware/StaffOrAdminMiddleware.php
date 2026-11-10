<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffOrAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->canManageInventory()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Halaman tersebut hanya dapat diakses oleh Admin atau Petugas Gudang.');
        }

        return $next($request);
    }
}

